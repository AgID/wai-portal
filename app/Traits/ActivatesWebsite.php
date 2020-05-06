<?php

namespace App\Traits;

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WebsiteAccessType;
use App\Enums\WebsiteStatus;
use App\Events\PublicAdministration\PublicAdministrationActivated;
use App\Events\User\UserActivated;
use App\Events\User\UserWebsiteAccessChanged;
use App\Models\Website;
use Silber\Bouncer\BouncerFacade as Bouncer;

/**
 * Website activation.
 */
trait ActivatesWebsite
{
    /**
     * Check wether a website started receiving tracking requests.
     *
     * @param Website $website the website
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     *
     * @return bool true if received tracking requests, false otherwise
     */
    public function hasActivated(Website $website): bool
    {
        $analyticsService = app()->make('analytics-service');

        $liveVisits = $analyticsService->getLiveVisits($website->analytics_id, 60);
        $totalVisits = $analyticsService->getSiteTotalVisitsFrom($website->analytics_id, $website->created_at->format('Y-m-d'));

        return $liveVisits > 0 || $totalVisits > 0;
    }

    /**
     * Activate a website.
     *
     * @param Website $website the website
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function activate(Website $website): void
    {
        $publicAdministration = $website->publicAdministration;

        $website->status = WebsiteStatus::ACTIVE;
        $website->save();

        if ($publicAdministration->status->is(PublicAdministrationStatus::PENDING)) {
            $publicAdministration->status = PublicAdministrationStatus::ACTIVE;
            $publicAdministration->save();

            $pendingUser = $publicAdministration->users()->where('user_status', UserStatus::PENDING)->first();
            if ($pendingUser) {
                Bouncer::scope()->to($publicAdministration->id);
                $pendingUser->assign(UserRole::ADMIN);

                //NOTE: don't use HasWebsitePermissions since this trait is shared with batch jobs
                Bouncer::allow($pendingUser)->to(UserPermission::READ_ANALYTICS, $website);
                Bouncer::allow($pendingUser)->to(UserPermission::MANAGE_ANALYTICS, $website);
                Bouncer::disallow($pendingUser)->to(UserPermission::NO_ACCESS, $website);
                Bouncer::refreshFor($pendingUser);

                app()->make('analytics-service')->setWebsiteAccess($pendingUser->uuid, WebsiteAccessType::WRITE, $website->analytics_id);

                event(new UserWebsiteAccessChanged($pendingUser, $website, WebsiteAccessType::WRITE()));

                $pendingUser->save();
                $publicAdministration->users()->updateExistingPivot($pendingUser->id, ['user_status' => UserStatus::ACTIVE]);

                event(new UserActivated($pendingUser, $publicAdministration));
            }

            event(new PublicAdministrationActivated($publicAdministration));
        }
    }
}
