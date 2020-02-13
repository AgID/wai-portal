<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Add custom validation rules.
        Validator::extend('alpha_name', function ($attribute, $value) {
            return preg_match('/^[\pL\s\'.’\-\–\‐]+$/u', $value);
        });
        Validator::extend('alpha_site', function ($attribute, $value) {
            return preg_match('/^[\pL\s\'"“”’.,!¡?¿\(\)\[\]\{\}\<\>\/\\\-\–\‐]+$/u', $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/../helpers.php';
    }
}
