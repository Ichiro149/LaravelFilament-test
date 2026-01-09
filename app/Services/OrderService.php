<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Calculate cart subtotal
     */
    public function calculateSubtotal(Collection $cartItems): float
    {
        return $cartItems->sum(function ($item) {
            $source = $item->variant ?? $item->product;
            $price = $source->sale_price ?? $source->price ?? 0;

            return (float) $price * $item->quantity;
        });
    }

    /**
     * Calculate discount amount for a coupon
     */
    public function calculateDiscount(Collection $cartItems, ?Coupon $coupon): float
    {
        if (! $coupon) {
            return 0;
        }

        $subtotal = $this->calculateSubtotal($cartItems);

        if ($coupon->type === 'percentage') {
            return round($subtotal * ($coupon->value / 100), 2);
        }

        return min($coupon->value, $subtotal);
    }

    /**
     * Create an order from cart items
     */
    public function createOrder(array $data, Collection $cartItems, ?Coupon $coupon = null): Order
    {
        return DB::transaction(function () use ($data, $cartItems, $coupon) {
            $subtotal = $this->calculateSubtotal($cartItems);
            $discount = $this->calculateDiscount($cartItems, $coupon);
            $total = max(0, $subtotal - $discount);

            // Generate unique order number
            $orderNumber = $this->generateOrderNumber();

            // Get pending status
            $pendingStatus = OrderStatus::pending();

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
                'order_status_id' => $pendingStatus?->id,
                'customer_name' => $data['name'],
                'customer_email' => $data['email'],
                'customer_phone' => $data['phone'] ?? null,
                'shipping_address' => $data['address'],
                'shipping_city' => $data['city'] ?? null,
                'shipping_state' => $data['state'] ?? null,
                'shipping_postal_code' => $data['postal_code'] ?? null,
                'shipping_country' => $data['country'] ?? null,
                'notes' => $data['notes'] ?? null,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
            ]);

            // Create order items and update stock
            $this->createOrderItems($order, $cartItems);

            // Create initial status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $pendingStatus?->id,
                'notes' => 'Order placed',
            ]);

            // Increment coupon usage
            if ($coupon) {
                $coupon->increment('used_count');
            }

            // Clear cart
            $this->clearCart($cartItems);

            return $order;
        });
    }

    /**
     * Create order items from cart items
     */
    protected function createOrderItems(Order $order, Collection $cartItems): void
    {
        foreach ($cartItems as $item) {
            $source = $item->variant ?? $item->product;
            $price = $source->sale_price ?? $source->price ?? 0;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->variant_id,
                'product_name' => $item->product->name,
                'variant_name' => $item->variant?->name,
                'quantity' => $item->quantity,
                'price' => $price,
                'subtotal' => $price * $item->quantity,
            ]);

            // Update stock
            $this->decrementStock($item);
        }
    }

    /**
     * Decrement stock for a cart item
     */
    protected function decrementStock(CartItem $item): void
    {
        if ($item->variant) {
            $item->variant->decrement('stock_quantity', $item->quantity);
        } else {
            $item->product->decrement('stock_quantity', $item->quantity);
        }
    }

    /**
     * Clear cart items
     */
    protected function clearCart(Collection $cartItems): void
    {
        CartItem::whereIn('id', $cartItems->pluck('id'))->delete();
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-'.strtoupper(Str::random(8));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get cart items for current user/session
     */
    public function getCartItems(): Collection
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        return CartItem::with(['product.images', 'product.category', 'variant'])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->get();
    }

    /**
     * Validate coupon and return it if valid
     */
    public function validateCoupon(string $code, float $subtotal): ?Coupon
    {
        $coupon = Coupon::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $coupon) {
            return null;
        }

        // Check dates
        $now = now();
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            return null;
        }
        if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
            return null;
        }

        // Check usage limit
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return null;
        }

        // Check minimum order amount
        if ($coupon->minimum_amount && $subtotal < $coupon->minimum_amount) {
            return null;
        }

        return $coupon;
    }
}
