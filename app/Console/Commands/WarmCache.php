<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class WarmCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:warm-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up application caches for better performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Warming up caches...');

        $this->task('Categories menu', fn () => CacheService::getCategoriesForMenu());
        $this->task('Active categories', fn () => CacheService::getActiveCategories());
        $this->task('Featured products', fn () => CacheService::getFeaturedProducts());
        $this->task('New arrivals', fn () => CacheService::getNewArrivals());
        $this->task('On-sale products', fn () => CacheService::getOnSaleProducts());
        $this->task('Popular products', fn () => CacheService::getPopularProducts());
        $this->task('Homepage stats', fn () => CacheService::getHomepageStats());

        $this->newLine();
        $this->info('✓ Cache warming completed!');

        return self::SUCCESS;
    }

    /**
     * Run a task and display result
     */
    private function task(string $name, callable $callback): void
    {
        $this->output->write("  {$name}... ");

        try {
            $callback();
            $this->output->writeln('<fg=green>done</>');
        } catch (\Exception $e) {
            $this->output->writeln('<fg=red>failed</>');
            $this->error("    Error: {$e->getMessage()}");
        }
    }
}
