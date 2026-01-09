<?php

namespace App\Traits;

/**
 * Trait for models with status fields that need labels and colors
 *
 * Usage: Add `use HasStatusLabels;` to your model
 * Override `statusColors()` and `statusLabels()` arrays
 */
trait HasStatusLabels
{
    /**
     * Get status color for UI display
     */
    public function getStatusColorAttribute(): string
    {
        return $this->statusColors()[$this->status] ?? 'gray';
    }

    /**
     * Get human-readable status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->statusLabels()[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Check if status is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if status is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if status is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Define status colors mapping
     * Override this method in your model
     */
    protected function statusColors(): array
    {
        return [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'gray',
        ];
    }

    /**
     * Define status labels mapping
     * Override this method in your model
     */
    protected function statusLabels(): array
    {
        return [
            'pending' => __('Pending'),
            'approved' => __('Approved'),
            'rejected' => __('Rejected'),
            'completed' => __('Completed'),
            'failed' => __('Failed'),
            'cancelled' => __('Cancelled'),
        ];
    }
}
