<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'user_id',
        'company_id',
        'name',
        'slug',
        'description',
        'long_description',
        'price',
        'sale_price',
        'category_id',
        'stock_quantity',
        'sku',
        'is_featured',
        'is_active',
        'weight',
        'attributes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'attributes' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = 'SKU-'.strtoupper(Str::random(8));
            }
        });

        // Если у продукта нет primary изображения, делаем первое primary
        static::saved(function ($product) {
            if (! $product->hasPrimaryImage() && $product->images()->count() > 0) {
                $product->images()->first()->update(['is_primary' => true]);
            }
        });
    }

    public static function generateUniqueSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, function ($query, $ignoreId) {
            return $query->where('id', '!=', $ignoreId);
        })->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * Продавец (владелец товара)
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias для seller
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Компания-продавец
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CustomerReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(CustomerReview::class)->where('status', 'approved');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('id');
    }

    public function activityLogs()
    {
        return $this->morphMany(\App\Models\ActivityLog::class, 'subject');
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function comparisons(): HasMany
    {
        return $this->hasMany(ProductComparison::class);
    }

    // =========================================================
    // SELLER HELPERS
    // =========================================================

    /**
     * Проверка: принадлежит ли товар пользователю
     */
    public function belongsToUser(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Scope: товары конкретного продавца
     */
    public function scopeBySeller($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    // =========================================================
    // PRICE & DISCOUNT HELPERS
    // =========================================================

    public function getCurrentPrice()
    {
        return $this->sale_price ?: $this->price;
    }

    public function getDiscountPercentage()
    {
        if (! $this->sale_price || ! $this->price) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    // =========================================================
    // RATING ACCESSORS (use withCount for optimization)
    // =========================================================

    /**
     * Get average rating
     * NOTE: For lists, use Product::withAvg('approvedReviews', 'overall_rating')
     */
    public function getAverageRatingAttribute(): ?float
    {
        // Check if we have preloaded the average
        if (isset($this->attributes['approved_reviews_avg_overall_rating'])) {
            $avg = $this->attributes['approved_reviews_avg_overall_rating'];

            return $avg ? round($avg, 1) : null;
        }

        $avg = $this->approvedReviews()->avg('overall_rating');

        return $avg ? round($avg, 1) : null;
    }

    /**
     * Get reviews count
     * NOTE: For lists, use Product::withCount('approvedReviews')
     */
    public function getReviewsCountAttribute(): int
    {
        // Check if we have preloaded the count
        if (isset($this->attributes['approved_reviews_count'])) {
            return (int) $this->attributes['approved_reviews_count'];
        }

        return $this->approvedReviews()->count();
    }

    // =========================================================
    // IMAGE HELPERS
    // =========================================================

    public function getPrimaryImage()
    {
        // If images are already loaded, use collection methods
        if ($this->relationLoaded('images')) {
            return $this->images->firstWhere('is_primary', true)
                ?? $this->images->first();
        }

        return $this->images()->where('is_primary', true)->first()
            ?? $this->images()->orderBy('sort_order')->first();
    }

    public function getPrimaryImageUrlAttribute()
    {
        $primaryImage = $this->getPrimaryImage();
        if (! $primaryImage) {
            return asset('images/placeholder.png');
        }

        return asset('storage/'.$primaryImage->image_path);
    }

    public function getImageGallery()
    {
        return $this->images()->orderBy('sort_order')->get();
    }

    public function hasPrimaryImage(): bool
    {
        if ($this->relationLoaded('images')) {
            return $this->images->contains('is_primary', true);
        }

        return $this->images()->where('is_primary', true)->exists();
    }

    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
}
