<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * User suspended email.
 */
class Deleted extends UserMailable
{
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
