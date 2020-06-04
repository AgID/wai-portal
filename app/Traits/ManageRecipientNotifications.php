<?php

namespace App\Traits;

use App\Models\PublicAdministration;
use App\Models\User;

/**
 * Manage recipients on notifications.
 */
trait ManageRecipientNotifications
{
    /**
     * Get specific email for the administration.
     *
     * @param User $user the recipient of the notification
     * @param PublicAdministration $publicAdministration the public administration that the user belongs to
     *
     * @return string the correct user email
     */
    public function getUserEmailForPublicAdministration(User $user, ?PublicAdministration $publicAdministration = null): string
    {
        $publicAdministrationUser = $user->publicAdministrations()->where('public_administration_id', $publicAdministration->id ?? null)->first();
        if ($publicAdministrationUser && $publicAdministrationUser->pivot->user_email) {
            return $publicAdministrationUser->pivot->user_email;
        }

        return $user->email;
    }
}
