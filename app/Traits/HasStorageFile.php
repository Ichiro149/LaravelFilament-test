<?php

namespace App\Traits;

/**
 * Trait for models that have file storage paths
 *
 * Usage: Add `use HasStorageFile;` to your model
 * Call $this->getStorageUrl('column_name') to get the full URL
 */
trait HasStorageFile
{
    /**
     * Get full URL for a storage file column
     *
     * @param  string  $column  The column name containing the file path
     * @param  string|null  $fallback  Fallback URL if column is empty
     */
    public function getStorageUrl(string $column, ?string $fallback = null): ?string
    {
        $path = $this->{$column};

        if (! $path) {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/'.$path);
    }

    /**
     * Check if a storage file exists
     */
    public function hasStorageFile(string $column): bool
    {
        return ! empty($this->{$column});
    }
}
