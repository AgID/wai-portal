<?php

namespace App\Http\Controllers;

use App\Enums\Logs\EventType;
use Illuminate\Http\RedirectResponse;

class AnalyticsServiceController extends Controller
{
    /**
     * Login and redirect the current user to the Analytics Service.
     *
     * @param string|null $websiteAnalyticsId the Analytics Service website ID to redirect to
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind analytics service
     *
     * @return RedirectResponse the server redirect response
     */
    public function login(string $websiteAnalyticsId = null): RedirectResponse
    {
        $user = auth()->user();
        if (!$user->hasAnalyticsServiceAccount()) {
            abort(404);
        }

        logger()->info(
            'User ' . $user->uuid . ' logged in the Analytics Service.',
            [
                'user' => $user->uuid,
                'pa' => current_public_administration()->ipa_code,
                'event' => EventType::ANALYTICS_LOGIN,
            ]
        );

        $hashedPassword = md5($user->analytics_password);

        return app()->make('analytics-service')
                    ->loginAndRedirectUser($user->uuid, $hashedPassword, $websiteAnalyticsId);
    }
}
