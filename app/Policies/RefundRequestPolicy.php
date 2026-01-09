<?php

namespace App\Policies;

use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RefundRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any refund requests.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the refund request.
     */
    public function view(User $user, RefundRequest $refundRequest): bool
    {
        // Admins can view any refund request
        if ($user->role === 'admin') {
            return true;
        }

        // Sellers can view refund requests for their products
        if ($user->role === 'seller') {
            /** @var \App\Models\Order|null $order */
            $order = $refundRequest->order;
            if ($order) {
                return $order->items()->whereHas('product', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->exists();
            }
        }

        // Users can only view their own refund requests
        return $refundRequest->user_id === $user->id;
    }

    /**
     * Determine whether the user can create refund requests.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the refund request.
     */
    public function update(User $user, RefundRequest $refundRequest): bool
    {
        // Only admins can update refund requests (approve/reject)
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the refund request.
     */
    public function delete(User $user, RefundRequest $refundRequest): bool
    {
        // Only admins can delete refund requests
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can cancel the refund request.
     */
    public function cancel(User $user, RefundRequest $refundRequest): bool
    {
        // Users can cancel their own pending refund requests
        return $refundRequest->user_id === $user->id && $refundRequest->status === 'pending';
    }
}
