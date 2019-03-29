<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class AnalyticsController extends Controller
{
    /**
     * Login and redirect the current user to the Analytics Service.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind analytics service
     *
     * @return \Illuminate\Http\Response the server response
     */
    public function login(): Response
    {
        if (empty(auth()->user()->partial_analytics_password)) {
            abort(404);
        }

        logger()->info('User ' . auth()->user()->getInfo() . ' logged in the Analytics Service.');

        $hashedPassword = md5(auth()->user()->analytics_password);

        return app()->make('analytics-service')
                    ->loginAndRedirectUser(auth()->user()->email, $hashedPassword);
    }
}
