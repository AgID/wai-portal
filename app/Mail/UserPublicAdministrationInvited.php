<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

/**
 * Website added email to public administration administrators.
 */
class UserPublicAdministrationInvited extends UserMailable
{
    /**
     * Build the message.
     *
     * @return UserWebsiteAdded the email
     */
    public function build(): UserPublicAdministrationInvited
    {
        return $this->subject(__('Invito su :app', ['pa' => $this->publicAdministration->name, 'app' => config('app.name')]))
            ->markdown('mail.invited')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
