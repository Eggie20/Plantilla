<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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
        // Disable password validation features
        Password::defaults(function () {
            return Password::min(6)->uncompromised(0);
        });
        
        // Share role checking function with all views
        View::share('hasRole', function ($role) {
            return Auth::check() && Auth::user()->role === $role;
        });
    }
}
