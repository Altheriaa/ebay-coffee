<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Observers\ActivityLogObserver;
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
        // Register observer untuk auto-logging semua aktivitas admin
        Category::observe(ActivityLogObserver::class);
        Product::observe(ActivityLogObserver::class);
        Customer::observe(ActivityLogObserver::class);
        Order::observe(ActivityLogObserver::class);
        User::observe(ActivityLogObserver::class);
    }
}
