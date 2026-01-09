<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait for models that need unique slug generation
 *
 * Usage: Add `use HasSlug;` to your model
 * Override `slugSourceColumn()` if slug should be generated from a column other than 'name'
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug) && ! empty($model->{$model->slugSourceColumn()})) {
                $model->slug = static::generateUniqueSlug($model->{$model->slugSourceColumn()});
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty($model->slugSourceColumn()) && empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->{$model->slugSourceColumn()}, $model->id);
            }
        });
    }

    /**
     * The column to use as source for slug generation
     */
    protected function slugSourceColumn(): string
    {
        return 'name';
    }

    /**
     * Generate a unique slug
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Use slug for route model binding
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
