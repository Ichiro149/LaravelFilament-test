<?php

namespace App\Policies;

use App\Models\CustomerReview;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any reviews.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the review.
     */
    public function view(User $user, CustomerReview $review): bool
    {
        // Admins can view any review
        if ($user->role === 'admin') {
            return true;
        }

        // Sellers can view reviews of their products
        if ($user->role === 'seller' && $review->product) {
            return $review->product->user_id === $user->id;
        }

        // Users can view their own reviews or approved reviews
        return $review->user_id === $user->id || $review->status === 'approved';
    }

    /**
     * Determine whether the user can create reviews.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the review.
     */
    public function update(User $user, CustomerReview $review): bool
    {
        // Admins can update any review (for moderation)
        if ($user->role === 'admin') {
            return true;
        }

        // Users can only update their own pending reviews
        return $review->user_id === $user->id && $review->status === 'pending';
    }

    /**
     * Determine whether the user can delete the review.
     */
    public function delete(User $user, CustomerReview $review): bool
    {
        // Admins can delete any review
        if ($user->role === 'admin') {
            return true;
        }

        // Users can delete their own reviews
        return $review->user_id === $user->id;
    }

    /**
     * Determine whether the user can moderate the review.
     */
    public function moderate(User $user, CustomerReview $review): bool
    {
        return $user->role === 'admin';
    }
}
