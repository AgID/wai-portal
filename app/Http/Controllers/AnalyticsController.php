<?php

namespace App\Http\Controllers;

use App\Enums\Logs\EventType;
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

        logger()->info(
            'User ' . auth()->user()->uuid . ' logged in the Analytics Service.',
            [
                'user' => auth()->user()->uuid,
                'pa' => current_public_administration()->ipa_code,
                'event' => EventType::ANALYTICS_LOGIN,
            ]
        );

        $hashedPassword = md5(auth()->user()->analytics_password);

        return app()->make('analytics-service')
                    ->loginAndRedirectUser(auth()->user()->uuid, $hashedPassword);
    }
}
