<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class AnalyticsController extends Controller
{
    /**
     * Login and redirect the current user to the Analytics Service.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind analytics service
     *
     * @return RedirectResponse the server redirect response
     */
    public function login(): RedirectResponse
    {
        if (!auth()->user()->hasAnalyticsServiceAccount()) {
            abort(404);
        }

        logger()->info('User ' . auth()->user()->getInfo() . ' logged in the Analytics Service.');

        $hashedPassword = md5(auth()->user()->analytics_password);

        return app()->make('analytics-service')
                    ->loginAndRedirectUser(auth()->user()->uuid, $hashedPassword);
    }
}
