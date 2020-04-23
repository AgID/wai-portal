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
     * Set specific email for the administration.
     *
     * @param User $user the recipient of the notification
     * @param PublicAdministration $publicAdministration the public administration that the user belongs to
     *
     * @return string the correct user email
     */
    public function recipientSetSpecificEmailForUserPublicAdministration(User $user, ?PublicAdministration $publicAdministration = null): string
    {
        if ($publicAdministration) {
            $publicAdministrationUser = $user->publicAdministrations()->where('public_administration_id', $publicAdministration->id)->first();
            if ($publicAdministrationUser && $publicAdministrationUser->pivot->pa_email) {
                return $publicAdministrationUser->pivot->pa_email;
            }
        }

        return $user->email;
    }
}
