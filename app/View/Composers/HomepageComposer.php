<?php

namespace App\View\Composers;

use App\Services\CacheService;
use Illuminate\View\View;

class HomepageComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with([
            'featuredProducts' => CacheService::getFeaturedProducts(),
            'newArrivals' => CacheService::getNewArrivals(),
            'onSaleProducts' => CacheService::getOnSaleProducts(),
            'popularProducts' => CacheService::getPopularProducts(),
            'categories' => CacheService::getActiveCategories(),
            'stats' => CacheService::getHomepageStats(),
        ]);
    }
}
