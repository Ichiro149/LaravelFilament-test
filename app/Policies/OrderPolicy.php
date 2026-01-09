<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all, users can view their own (handled in controller)
        return true;
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // Admin can view any order
        if ($user->role === 'admin') {
            return true;
        }

        // Seller can view orders containing their products
        if ($user->role === 'seller') {
            return $order->items()->whereHas('product', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->exists();
        }

        // Customer can view their own orders
        return $order->user_id === $user->id || $order->customer_email === $user->email;
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Only admins can update orders
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        // Only admins can delete orders
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can download the invoice.
     */
    public function downloadInvoice(User $user, Order $order): bool
    {
        return $this->view($user, $order);
    }

    /**
     * Determine whether the user can request a refund.
     */
    public function requestRefund(User $user, Order $order): bool
    {
        // Only order owner can request refund
        return ($order->user_id === $user->id || $order->customer_email === $user->email)
            && in_array($order->status->slug, ['delivered', 'completed']);
    }
}
