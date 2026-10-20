<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register application services here.
    }

    public function boot(): void
    {
        // Use a lightweight SIAKADU paginator instead of the Tailwind view.
        // This prevents oversized SVG arrows when Tailwind CSS is not installed.
        Paginator::defaultView('vendor.pagination.siakadu');
    }
}
