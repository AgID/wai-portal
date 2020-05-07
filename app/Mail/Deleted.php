<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * User suspended email.
 */
class Deleted extends UserMailable
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
     * @return Deleted the email
     */
    public function build(): Deleted
    {
        return $this->subject(__('Utente cancellato'))
            ->markdown('mail.deleted')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
