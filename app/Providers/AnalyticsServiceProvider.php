<?php

namespace App\Providers;

use App\Services\MatomoService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Analytics service provider.
 */
class AnalyticsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('analytics-service', function ($app) {
            return new MatomoService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array the provided services array
     */
    public function provides(): array
    {
        return ['analytics-service'];
    }
}
