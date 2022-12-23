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
use App\Exceptions\CommandErrorException;
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

        return $analyticsService->isActive($website->analytics_id);
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
        $analyticsService = app()->make('analytics-service');

        $publicAdministration = $website->publicAdministration;

        $website->status = WebsiteStatus::ACTIVE;
        $website->save();

        if ($publicAdministration->status->is(PublicAdministrationStatus::PENDING)) {
            $publicAdministration->status = PublicAdministrationStatus::ACTIVE;
            $publicAdministration->save();

            $pendingUser = $publicAdministration->users()->where('user_status', UserStatus::PENDING)->first();
            if ($pendingUser) {
                Bouncer::scope()->to($publicAdministration->id);

                // Retract UserRole::REGISTERED role from the pending user
                $pendingUser->retract(UserRole::REGISTERED);
                $pendingUser->assign(UserRole::ADMIN);

                // NOTE: don't use HasWebsitePermissions since this trait is shared with batch jobs
                Bouncer::allow($pendingUser)->to(UserPermission::READ_ANALYTICS, $website);
                Bouncer::allow($pendingUser)->to(UserPermission::MANAGE_ANALYTICS, $website);
                Bouncer::disallow($pendingUser)->to(UserPermission::NO_ACCESS, $website);
                Bouncer::refreshFor($pendingUser);

                // Ensure the user exists in the analytics service
                try {
                    $analyticsService->getUserByEmail($pendingUser->email);
                } catch (CommandErrorException $exception) {
                    // User doesn't exists (?), create account
                    $analyticsService->registerUser($pendingUser->uuid, $pendingUser->analytics_password, $pendingUser->email);
                }

                $analyticsService->setWebsiteAccess($pendingUser->uuid, WebsiteAccessType::WRITE, $website->analytics_id);

                event(new UserWebsiteAccessChanged($pendingUser, $website, WebsiteAccessType::WRITE()));

                // The user could be already ACTIVE if accepted an invitation in another public administration
                if ($pendingUser->status->isNot(UserStatus::ACTIVE)) {
                    $pendingUser->status = UserStatus::ACTIVE;
                    $pendingUser->save();
                }

                $publicAdministration->users()->updateExistingPivot($pendingUser->id, ['user_status' => UserStatus::ACTIVE]);

                event(new UserActivated($pendingUser, $publicAdministration));
            }

            event(new PublicAdministrationActivated($publicAdministration));
        }
    }
}
