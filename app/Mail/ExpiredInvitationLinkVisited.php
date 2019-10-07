<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class ExpiredInvitationLinkVisited extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $user;

    protected $invitedUser;

    public function __construct(User $user, User $invitedUser)
    {
        $this->user = $user;
        $this->invitedUser = $invitedUser;
    }

    public function build(): ExpiredInvitationLinkVisited
    {
        return $this->subject(__('[Info] - Avviso utilizzo invito scaduto'))
            ->markdown('mail.expired_invitation_link_visited')->with([
                'locale' => Lang::getLocale(),
                'fullName' => $this->user->full_name,
                'invitedFullName' => $this->invitedUser->full_name,
                'profileUrl' => route('users.show', ['user' => $this->invitedUser]),
            ]);
    }
}
