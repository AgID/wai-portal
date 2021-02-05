<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * User invited email to public administration administrators.
 */
class UserEmailForPublicAdministrationChanged extends UserMailable
{
    /**
     * Create a new mail message instance.
     *
     * @param User $recipient the mail recipient
     * @param PublicAdministration $publicAdministration the public administration the invited user belogns to
     */
    public function __construct(User $recipient, PublicAdministration $publicAdministration, string $updatedEmail)
    {
        parent::__construct($recipient, $publicAdministration);
        $this->updatedEmail = $updatedEmail;
    }

    /**
     * Build the message.
     *
     * @return UserInvitUserEmailForPublicAdministrationChanged the email
     */
    public function build(): UserEmailForPublicAdministrationChanged
    {
        return $this->subject(__("Modifica dell'indirizzo email"))
            ->markdown('mail.user_email_pa_changed')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
                'updatedEmail' => $this->updatedEmail,
            ]);
    }
}
