<?php

namespace App\View\Composers;

use App\Services\CacheService;
use Illuminate\View\View;

class NavigationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with('navCategories', CacheService::getCategoriesForMenu());
    }
}
