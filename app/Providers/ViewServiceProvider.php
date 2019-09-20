<?php

namespace App\Providers;

use App\Enums\UserStatus;
use App\Http\View\Composers\ModalComposer;
use App\Http\View\Composers\NotificationComposer;
use App\Http\View\Composers\PrimaryMenuComposer;
use App\Http\View\Composers\PublicAdministrationSelectorComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeViewConfig();

        View::composer([
            'auth.*',
            'pages.*',
        ], PublicAdministrationSelectorComposer::class);
        View::composer('*', PrimaryMenuComposer::class);
        View::composer('layouts.includes.modal', ModalComposer::class);
        View::composer('layouts.includes.notification', NotificationComposer::class);
        View::composer('*', function ($view) {
            $view->with('authUser', auth()->user());
            $view->with('spidAuthUser', app()->make('SPIDAuth')->getSPIDUser());
            $view->with('hasActivePublicAdministration', session()->has('tenant_id') && auth()->user()->status->is(UserStatus::ACTIVE));
        });
    }

    /**
     * Merge the view configuration into the app configuration array.
     */
    protected function mergeViewConfig()
    {
        try {
            config(Yaml::parseFile(resource_path('views/config.yml')));
        } catch (ParseException $exception) {
            abort(500, $exception->getMessage());
        }
    }
}
