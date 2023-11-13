<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        view()->composer('layouts.admin', 'App\Http\ViewComposers\NotificationComposer');
        view()->composer('layouts.sv', 'App\Http\ViewComposers\NotificationComposer');
        view()->composer('layouts.app', 'App\Http\ViewComposers\NotificationComposer');
    }
}
