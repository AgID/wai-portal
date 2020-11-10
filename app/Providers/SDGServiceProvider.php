<?php

namespace App\Providers;

use App\Services\SingleDigitalGatewayService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Single Digital Gateway service provider.
 */
class SDGServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('sdg-service', function ($app) {
            return new SingleDigitalGatewayService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array the provided services array
     */
    public function provides(): array
    {
        return ['sdg-service'];
    }
}
