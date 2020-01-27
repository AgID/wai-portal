<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

class UserInvited extends UserMailable
{
    private $invitedUser;

    private $publicAdministration;

    public function __construct(User $recipient, User $invitedUser, PublicAdministration $publicAdministration)
    {
        parent::__construct($recipient);
        $this->invitedUser = $invitedUser;
        $this->publicAdministration = $publicAdministration;
    }

    public function build(): UserInvited
    {
        return $this->subject(__('[Info] - Utente invitato'))
            ->markdown('mail.user_invited')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'invitedUser' => $this->invitedUser,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
