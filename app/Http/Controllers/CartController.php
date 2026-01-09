<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller for cart operations (add, update, remove items).
 *
 * @see CheckoutController for checkout/order logic
 * @see CartCouponController for coupon logic
 */
class CartController extends Controller
{
    /**
     * Show the cart page.
     */
    public function index(): View|RedirectResponse
    {
        $userId = auth()->id();

        if (! $userId) {
            return redirect()->route('login')->with('error', 'Please login to view your cart.');
        }

        $cartItems = CartItem::with(['product', 'variant'])->where('user_id', $userId)->get();

        $subtotal = $this->calculateSubtotal($cartItems);
        $coupon = session('coupon');
        $discount = $coupon['discount'] ?? 0;
        $total = $subtotal - $discount;

        return view('cart.index', compact('cartItems', 'subtotal', 'total', 'discount', 'coupon'));
    }

    /**
     * Redirect to index (alias).
     */
    public function show()
    {
        return redirect()->route('cart.index');
    }

    /**
     * Add a product to cart.
     */
    public function add(Request $request, int $productId): JsonResponse
    {
        $userId = auth()->id();

        if (! $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $qty = max(1, (int) $request->input('quantity', 1));
        $variantId = $request->input('variant_id');
        $product = Product::findOrFail($productId);

        // Validate stock availability
        $available = $this->getAvailableStock($productId, $variantId);
        if ($available === null) {
            return response()->json(['success' => false, 'message' => 'Invalid variant'], 400);
        }

        $existingItem = $this->findCartItem($userId, $productId, $variantId);
        $existingQty = $existingItem?->quantity ?? 0;

        if (($existingQty + $qty) > $available) {
            return response()->json(['success' => false, 'message' => 'Requested quantity not available'], 400);
        }

        $this->addOrMergeCartItem($userId, $productId, $variantId, $qty);

        activity_log('added_to_cart:'.__('activity_log.log.added_to_cart', ['product' => $product->name, 'qty' => $qty]));

        $count = CartItem::where('user_id', $userId)->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cartCount' => $count,
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, int $itemId): JsonResponse
    {
        $userId = auth()->id();

        if (! $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $qty = max(1, (int) $request->input('quantity', 1));
        $cartItem = CartItem::where('id', $itemId)->where('user_id', $userId)->first();

        if (! $cartItem) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        $cartItem->quantity = $qty;
        $cartItem->save();

        activity_log('updated_cart:'.__('activity_log.log.updated_cart', ['product' => $cartItem->product->name, 'qty' => $qty]));

        $cartItems = CartItem::where('user_id', $userId)->with(['product', 'variant'])->get();
        $subtotal = $this->calculateSubtotal($cartItems);

        $priceSource = $cartItem->variant ?? $cartItem->product;
        $itemSubtotal = ($priceSource->sale_price ?? $priceSource->price) * $qty;

        return response()->json([
            'success' => true,
            'subtotal' => $itemSubtotal,
            'total' => $subtotal,
            'cartCount' => $cartItems->sum('quantity'),
        ]);
    }

    /**
     * Remove an item from cart.
     */
    public function remove(Request $request, int $itemId): JsonResponse
    {
        $userId = auth()->id();

        if (! $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $cartItem = CartItem::where('id', $itemId)->where('user_id', $userId)->first();

        if (! $cartItem) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        $productName = $cartItem->product->name;
        $cartItem->delete();

        activity_log('removed_from_cart:'.__('activity_log.log.removed_from_cart', ['product' => $productName]));

        $cartCount = CartItem::where('user_id', $userId)->sum('quantity');

        return response()->json([
            'success' => true,
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * Get cart item count (AJAX).
     */
    public function getCartCount(): JsonResponse
    {
        $userId = auth()->id();

        if (! $userId) {
            return response()->json(['count' => 0]);
        }

        $count = CartItem::where('user_id', $userId)->sum('quantity');

        return response()->json(['count' => $count]);
    }

    /**
     * Calculate subtotal for cart items.
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
     * Get available stock for product/variant.
     */
    protected function getAvailableStock(int $productId, ?int $variantId): ?int
    {
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if (! $variant || $variant->product_id != $productId) {
                return null;
            }

            return $variant->stock_quantity;
        }

        return Product::find($productId)?->stock_quantity;
    }

    /**
     * Find existing cart item.
     */
    protected function findCartItem(int $userId, int $productId, ?int $variantId): ?CartItem
    {
        $query = CartItem::where('user_id', $userId)->where('product_id', $productId);

        if ($variantId) {
            $query->where('variant_id', $variantId);
        } else {
            $query->whereNull('variant_id');
        }

        return $query->first();
    }

    /**
     * Add or merge cart item (handles unique constraint).
     */
    protected function addOrMergeCartItem(int $userId, int $productId, ?int $variantId, int $qty): void
    {
        $existingItem = $this->findCartItem($userId, $productId, $variantId);

        if ($existingItem) {
            $existingItem->quantity += $qty;
            $existingItem->save();

            return;
        }

        // Check for any existing item with same product (different variant)
        $existingAny = CartItem::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($existingAny) {
            $existingAny->quantity += $qty;
            if ($variantId) {
                $existingAny->variant_id = $variantId;
            }
            $existingAny->save();

            return;
        }

        try {
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $qty,
                'session_id' => session()->getId(),
            ]);
        } catch (UniqueConstraintViolationException $e) {
            $fallback = CartItem::where('user_id', $userId)->where('product_id', $productId)->first();
            if ($fallback) {
                $fallback->quantity += $qty;
                if ($variantId) {
                    $fallback->variant_id = $variantId;
                }
                $fallback->save();
            } else {
                throw $e;
            }
        }
    }
}
