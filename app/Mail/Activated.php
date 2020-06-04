<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * User activated email.
 */
class Activated extends UserMailable
{
    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param PublicAdministration $publicAdministration the activated public administration
     */
    public function __construct(User $recipient, PublicAdministration $publicAdministration)
    {
        parent::__construct($recipient, $publicAdministration);
    }

    /**
     * Build the message.
     *
     * @return Activated the email
     */
    public function build(): Activated
    {
        return $this->subject(__('Utente attivato'))
            ->markdown('mail.activated')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
            ]);
    }
}
