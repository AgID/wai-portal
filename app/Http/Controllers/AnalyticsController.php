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
        if (empty(auth()->user()->partial_analytics_password)) {
            abort(404);
        }

        logger()->info('User ' . auth()->user()->getInfo() . ' logged in the Analytics Service.');

        $hashedPassword = md5(auth()->user()->analytics_password);

        return app()->make('analytics-service')
                    ->loginAndRedirectUser(auth()->user()->email, $hashedPassword);
    }
}
