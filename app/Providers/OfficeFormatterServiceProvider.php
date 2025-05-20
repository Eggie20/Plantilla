<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class OfficeFormatterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load the Office Formatter helper
        require_once app_path('Helpers/OfficeFormatter.php');
    }
}