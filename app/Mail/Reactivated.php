<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * User reactivated email.
 */
class Reactivated extends UserMailable
{
    /**
     * Create a new mail message instance.
     *
     * @param User $recipient the mail recipient
     * @param PublicAdministration $publicAdministration the public administration the invited user belogns to
     */
    public function __construct(User $recipient, PublicAdministration $publicAdministration)
    {
        parent::__construct($recipient, $publicAdministration);
    }

    /**
     * Build the message.
     *
     * @return Reactivated the email
     */
    public function build(): Reactivated
    {
        return $this->subject(__('Utente riattivato'))
            ->markdown('mail.reactivated')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
