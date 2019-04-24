<?php

namespace App\Traits;

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Events\PublicAdministration\PublicAdministrationActivated;
use App\Events\User\UserActivated;
use App\Models\Website;
use Silber\Bouncer\BouncerFacade as Bouncer;

/**
 * Website activation.
 */
trait ActivatesWebsite
{
    /**
     * Get if a website started receiving tracking requests.
     *
     * @param Website $website the website
     * @param string $tokenAuth the authentication token
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     *
     * @return bool true if received tracking requests, false otherwise
     */
    public function hasActivated(Website $website, string $tokenAuth): bool
    {
        $analyticsService = app()->make('analytics-service');

        return $analyticsService->getLiveVisits($website->analytics_id, 60, $tokenAuth) > 0 || $analyticsService->getSiteTotalVisitsFrom($website->analytics_id, $website->created_at->format('Y-m-d'), $tokenAuth) > 0;
    }

    /**
     * Activate a website.
     *
     * @param Website $website the website
     * @param string $tokenAuth the authentication token
     */
    public function activate(Website $website, string $tokenAuth): void
    {
        $publicAdministration = $website->publicAdministration;

        $website->status = WebsiteStatus::ACTIVE;
        $website->save();

        if ($publicAdministration->status->is(PublicAdministrationStatus::PENDING)) {
            $publicAdministration->status = PublicAdministrationStatus::ACTIVE;
            $publicAdministration->save();

            $pendingUser = $publicAdministration->users()->where('status', UserStatus::PENDING)->first();
            if ($pendingUser) {
                $pendingUser->status = UserStatus::ACTIVE;
                $pendingUser->save();

                $pendingUser->roles()->detach();
                Bouncer::scope()->to($publicAdministration->id);
                $pendingUser->assign(UserRole::ADMIN);

                event(new UserActivated($pendingUser));
            }

            event(new PublicAdministrationActivated($publicAdministration));
        }
    }
}
