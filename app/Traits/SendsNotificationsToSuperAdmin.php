<?php

namespace App\Traits;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Notifications\SuperAdminPublicAdministrationNotFoundInIpaEmail;
use Silber\Bouncer\BouncerFacade as Bouncer;

/**
 * System notifications to super-administrators.
 */
trait SendsNotificationsToSuperAdmin
{
    /**
     * Send a registered public administration is missing from latest iPA.
     *
     * @param PublicAdministration $publicAdministration the missing public administration
     */
    public function sendPublicAdministrationNotFoundInIpa(PublicAdministration $publicAdministration): void
    {
        Bouncer::scope()->onceTo(0, function () use ($publicAdministration) {
            User::whereIs(UserRole::SUPER_ADMIN)->where('status', UserStatus::ACTIVE)->get()->each(function (User $user) use ($publicAdministration) {
                $user->notify(new SuperAdminPublicAdministrationNotFoundInIpaEmail($publicAdministration));
            });
        });
    }
}
