<?php

namespace App\Http\Controllers;

class AnalyticsController extends Controller
{
    /**
     * Login and redirect the current user to the Analytics Service.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        logger()->info('User ' . auth()->user()->getInfo() . ' logged in the Analytics Service.');

        return app()->make('analytics-service')
                    ->loginAndRedirectUser(auth()->user()->email, auth()->user()->analytics_password);
    }
}
