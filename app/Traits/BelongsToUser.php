<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait for models that belong to a user
 *
 * Usage: Add `use BelongsToUser;` to your model
 */
trait BelongsToUser
{
    /**
     * Get the user that owns this model
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?? auth()->id();

        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by current authenticated user
     */
    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    /**
     * Check if this model belongs to a specific user
     */
    public function belongsToUser($userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        return $this->user_id === $userId;
    }
}
