<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\HasStorageFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;
    use HasSlug;
    use HasStorageFile;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'short_description',
        'logo',
        'banner',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'country',
        'is_verified',
        'is_active',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * Владелец компании (продавец)
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Товары компании
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Активные товары компании
     */
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }

    /**
     * Подписчики компании
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_follows')
            ->withTimestamps();
    }

    // =========================================================
    // ACCESSORS (using HasStorageFile trait)
    // =========================================================

    /**
     * URL логотипа
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->getStorageUrl('logo');
    }

    /**
     * URL баннера
     */
    public function getBannerUrlAttribute(): ?string
    {
        return $this->getStorageUrl('banner');
    }

    /**
     * Количество подписчиков
     * NOTE: Use withCount('followers') when loading to avoid N+1
     */
    public function getFollowersCountAttribute(): int
    {
        return $this->followers_count ?? $this->followers()->count();
    }

    /**
     * Количество товаров
     * NOTE: Use withCount('activeProducts') when loading to avoid N+1
     */
    public function getProductsCountAttribute(): int
    {
        return $this->active_products_count ?? $this->products()->where('is_active', true)->count();
    }

    // =========================================================
    // HELPER METHODS
    // =========================================================

    /**
     * Проверить, подписан ли пользователь на компанию
     */
    public function isFollowedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->followers()->where('user_id', $user->id)->exists();
    }

    /**
     * Получить URL страницы компании
     */
    public function getUrlAttribute(): string
    {
        return route('companies.show', $this->slug);
    }
}
