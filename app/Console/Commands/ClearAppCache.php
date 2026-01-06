<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class ClearAppCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-cache 
                            {--products : Clear only product caches}
                            {--categories : Clear only category caches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear application caches (products, categories)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('products')) {
            CacheService::clearProductCache();
            $this->info('✓ Product caches cleared.');

            return self::SUCCESS;
        }

        if ($this->option('categories')) {
            CacheService::clearCategoryCache();
            $this->info('✓ Category caches cleared.');

            return self::SUCCESS;
        }

        // Clear all
        CacheService::clearAll();
        $this->info('✓ All application caches cleared.');

        return self::SUCCESS;
    }
}
