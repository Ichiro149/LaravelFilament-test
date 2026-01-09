<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Show the checkout page.
     */
    public function show()
    {
        $userId = Auth::id();

        $cartItems = CartItem::with(['product.images', 'variant'])
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $subtotal = $this->calculateSubtotal($cartItems);
        $discount = 0;
        $total = $subtotal - $discount;
        $cartCount = $cartItems->sum('quantity');

        $user = Auth::user();
        $savedAddresses = $user->addresses()->orderByDesc('is_default')->get();
        $savedPaymentMethods = $user->paymentMethods()->orderByDesc('is_default')->get();

        return view('checkout.index', compact(
            'cartItems',
            'subtotal',
            'discount',
            'total',
            'cartCount',
            'savedAddresses',
            'savedPaymentMethods'
        ));
    }

    /**
     * Place an order.
     */
    public function placeOrder(Request $request)
    {
        $userId = auth()->id();

        if (! $userId) {
            return redirect()->route('login');
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'address' => 'required|string',
                'payment_method' => 'required|in:fake',
            ]);

            $cartItems = CartItem::with(['product', 'variant'])
                ->where('user_id', $userId)
                ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty');
            }

            $cartTotal = $this->calculateSubtotal($cartItems);
            [$discount, $couponId, $couponCode] = $this->applyCouponDiscount($cartItems);
            $finalTotal = $cartTotal - $discount;

            $order = $this->createOrder($request, $cartTotal, $discount, $couponCode, $finalTotal, $userId);
            $this->createOrderItems($order, $cartItems);
            $this->createOrderStatusHistory($order, $userId);

            // Clear cart and coupon
            CartItem::where('user_id', $userId)->delete();
            session()->forget('coupon');
            session(['last_order_id' => $order->id, 'recent_order_id' => $order->id]);

            activity_log('placed_order:'.__('activity_log.log.placed_order'));

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'redirect' => route('checkout.success', $order->id),
            ]);
        } catch (\Exception $e) {
            Log::error('Order placement error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the order success page.
     */
    public function success($orderId)
    {
        $order = Order::with(['items.product', 'status'])->findOrFail($orderId);

        if (auth()->check()) {
            if ($order->customer_email !== auth()->user()->email) {
                abort(403, 'Access Denied - You don\'t have permission');
            }
        } else {
            $sessionOrderId = session('last_order_id');
            if ($sessionOrderId != $orderId) {
                abort(403, 'Access Denied - You don\'t have permission');
            }
        }

        return view('checkout.success', compact('order'));
    }

    /**
     * Show order verification form.
     */
    public function verifyOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        return view('checkout.verify-order', compact('order'));
    }

    /**
     * Handle order verification.
     */
    public function verifyOrderPost(Request $request, $orderId)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $order = Order::findOrFail($orderId);

        if ($order->customer_email !== $request->email) {
            return back()->withErrors([
                'email' => 'Email does not match order email address.',
            ]);
        }

        session(['recent_order_id' => $orderId]);

        return redirect()->route('checkout.success', $orderId);
    }

    /**
     * Calculate cart subtotal.
     */
    protected function calculateSubtotal($cartItems): float
    {
        return $cartItems->sum(function ($item) {
            $source = $item->variant ?? $item->product;
            $price = $source->sale_price ?? $source->price ?? 0;

            return $price * $item->quantity;
        });
    }

    /**
     * Apply coupon discount from session.
     */
    protected function applyCouponDiscount($cartItems): array
    {
        $couponData = session('coupon');
        $discount = 0;
        $couponId = null;
        $couponCode = null;

        if (! $couponData) {
            return [$discount, $couponId, $couponCode];
        }

        $coupon = Coupon::find($couponData['id']);

        if (! $coupon || ! $coupon->isValid()) {
            return [$discount, $couponId, $couponCode];
        }

        $applicableTotal = 0;
        foreach ($cartItems as $item) {
            if ($coupon->appliesTo($item->product)) {
                $applicableTotal += $item->product->getCurrentPrice() * $item->quantity;
            }
        }

        if ($applicableTotal > 0) {
            $discount = $coupon->calculateDiscount($applicableTotal);
            $couponId = $coupon->id;
            $couponCode = $coupon->code;
            $coupon->incrementUsage();
        }

        return [$discount, $couponId, $couponCode];
    }

    /**
     * Create the order record.
     */
    protected function createOrder(Request $request, float $cartTotal, float $discount, ?string $couponCode, float $finalTotal, int $userId): Order
    {
        $pendingStatus = OrderStatus::where('slug', 'pending')->first();

        return Order::create([
            'order_number' => 'ORD-'.strtoupper(uniqid()),
            'user_id' => $userId,
            'order_status_id' => $pendingStatus?->id,
            'customer_name' => $request->name,
            'customer_email' => $request->email,
            'shipping_address' => $request->address,
            'notes' => $request->notes,
            'subtotal' => $cartTotal,
            'discount_amount' => $discount,
            'coupon_code' => $couponCode,
            'total' => $finalTotal,
            'payment_method' => 'fake',
            'payment_status' => 'completed',
        ]);
    }

    /**
     * Create order items and decrement stock.
     */
    protected function createOrderItems(Order $order, $cartItems): void
    {
        foreach ($cartItems as $item) {
            $variant = $item->variant;
            $price = $variant
                ? ($variant->sale_price ?? $variant->price)
                : $item->product->getCurrentPrice();

            $variantName = null;
            if ($variant) {
                $attrs = is_array($variant->attributes)
                    ? collect($variant->attributes)->map(fn ($v, $k) => "$k: $v")->join(', ')
                    : null;
                $variantName = $attrs ?: $variant->sku;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id ?? null,
                'product_name' => $item->product->name,
                'variant_name' => $variantName,
                'quantity' => $item->quantity,
                'product_price' => $price,
                'total' => $price * $item->quantity,
            ]);

            try {
                if ($variant) {
                    $variant->decrement('stock_quantity', $item->quantity);
                } else {
                    $item->product->decrement('stock_quantity', $item->quantity);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to decrement stock: '.$e->getMessage());
            }
        }
    }

    /**
     * Create order status history entry.
     */
    protected function createOrderStatusHistory(Order $order, int $userId): void
    {
        $pendingStatus = OrderStatus::where('slug', 'pending')->first();

        if (! $pendingStatus) {
            return;
        }

        try {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $pendingStatus->id,
                'changed_by' => $userId,
                'notes' => 'Order placed successfully',
                'changed_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not create order history: '.$e->getMessage());
        }
    }
}
