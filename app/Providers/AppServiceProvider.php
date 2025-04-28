<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OcsInventoryService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OcsInventoryService::class, function ($app) {
            return new OcsInventoryService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
