<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Retrieve trashed users and websites to be injected in the
        // admin.publicAdministration.users.restore route
        Route::bind('trashed_user', function ($id) {
            return User::onlyTrashed()->where('uuid', $id)->first();
        });

        Route::bind('trashed_website', function ($id) {
            return Website::onlyTrashed()->where('slug', $id)->first();
        });
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        // NOTE: CronJobs routes disabled.
        // $this->mapCronRoutes();

        $this->mapWebHooksRoutes();

        if (!$this->app->environment('production')) {
            $this->mapTestingRoutes();
        }
        if ($this->app->environment('staging')) {
            $this->mapStagingRoutes();
        }

        if (config('wai.custom_public_administrations')) {
            $this->mapCustomPublicAdministration();
        }
    }

    /**
     * Define the "cron" routes for the application.
     *
     * This routes are stateless and should be reserved for CronJob tasks submission
     */
    public function mapCronRoutes(): void
    {
        Route::prefix('cron')
            ->middleware('cron')
            ->namespace($this->namespace)
            ->group(base_path('routes/cron.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "testing" routes for the application.
     *
     * These routes are only for testing purposes.
     */
    protected function mapTestingRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/testing.php'));
    }

    /**
     * Define the "staging" routes for the application.
     *
     * These routes are only for testing purposes.
     */
    protected function mapStagingRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/staging.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "web hook" routes for the application.
     *
     * These routes are stateless.
     */
    protected function mapWebHooksRoutes(): void
    {
        Route::prefix('hooks')
            ->middleware('hooks')
            ->namespace($this->namespace)
            ->group(base_path('routes/webhooks.php'));
    }

    /**
     * Define additional "web" routes for the application
     * to manage custom public administrations creation.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapCustomPublicAdministration(): void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/custom-pa.php'));
    }
}
