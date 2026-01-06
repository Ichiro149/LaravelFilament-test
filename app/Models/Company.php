<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            if (empty($company->slug) && ! empty($company->name)) {
                $company->slug = static::generateUniqueSlug($company->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

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
    // ACCESSORS
    // =========================================================

    /**
     * URL логотипа
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        if (str_starts_with($this->logo, 'http')) {
            return $this->logo;
        }

        return asset('storage/'.$this->logo);
    }

    /**
     * URL баннера
     */
    public function getBannerUrlAttribute(): ?string
    {
        if (! $this->banner) {
            return null;
        }

        if (str_starts_with($this->banner, 'http')) {
            return $this->banner;
        }

        return asset('storage/'.$this->banner);
    }

    /**
     * Количество подписчиков
     */
    public function getFollowersCountAttribute(): int
    {
        return $this->followers()->count();
    }

    /**
     * Количество товаров
     */
    public function getProductsCountAttribute(): int
    {
        return $this->products()->where('is_active', true)->count();
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
