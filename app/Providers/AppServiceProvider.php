<?php

namespace App\Providers;

use App\Services\CardService;
use App\Services\PosnetService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CardService::class, function ($app) {
            return new CardService();
        });

        $this->app->singleton(PosnetService::class, function ($app) {
            return new PosnetService();
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
