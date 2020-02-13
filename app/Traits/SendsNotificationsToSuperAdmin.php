<?php

namespace App\Traits;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Notifications\SuperAdminPublicAdministrationNotFoundInIpaEmail;
use Silber\Bouncer\BouncerFacade as Bouncer;

trait SendsNotificationsToSuperAdmin
{
    public function sendPublicAdministrationNotFoundInIpa(PublicAdministration $publicAdministration): void
    {
        Bouncer::scope()->onceTo(0, function () use ($publicAdministration) {
            User::whereIs(UserRole::SUPER_ADMIN)->where('status', UserStatus::ACTIVE)->get()->each(function (User $user) use ($publicAdministration) {
                $user->notify(new SuperAdminPublicAdministrationNotFoundInIpaEmail($publicAdministration));
            });
        });
    }
}
