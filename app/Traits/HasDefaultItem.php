<?php

namespace App\Traits;

/**
 * Trait for models that support "default" item selection per user
 *
 * Usage: Add `use HasDefaultItem;` to your model
 * Override `defaultOwnerColumn()` if the owner column is not 'user_id'
 */
trait HasDefaultItem
{
    /**
     * Set this item as default, unset all others for the same owner
     */
    public function setAsDefault(): void
    {
        $ownerColumn = $this->defaultOwnerColumn();

        self::where($ownerColumn, $this->{$ownerColumn})
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    /**
     * The column that identifies the owner (usually 'user_id')
     */
    protected function defaultOwnerColumn(): string
    {
        return 'user_id';
    }

    /**
     * Scope to get only default items
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get items for a specific owner
     */
    public function scopeForOwner($query, $ownerId)
    {
        return $query->where($this->defaultOwnerColumn(), $ownerId);
    }
}
