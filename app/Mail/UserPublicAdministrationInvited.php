<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * Website added email to public administration administrators.
 */
class UserPublicAdministrationInvited extends UserMailable
{
    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     */
    public function __construct(User $recipient, PublicAdministration $publicAdministration)
    {
        parent::__construct($recipient, $publicAdministration);
    }

    /**
     * Build the message.
     *
     * @return UserWebsiteAdded the email
     */
    public function build(): UserPublicAdministrationInvited
    {
        return $this->subject(__('Invito su :app', ['pa' => $this->publicAdministration->name, 'app' => config('app.name')]))
            ->markdown('mail.user_invited_no_link')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
