<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * User email for primary website tracking failing.
 */
class UserPrimaryWebsiteNotTracking extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The user to notify.
     *
     * @var User the user
     */
    protected $user;

    /**
     * Mail constructor.
     *
     * @param User $user the user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the mail.
     *
     * @return UserPrimaryWebsiteNotTracking the mail
     */
    public function build(): UserPrimaryWebsiteNotTracking
    {
        return $this->subject(__('[Attenzione] - Mancato tracciamento sito istituzionale'))
                    ->markdown('mail.primary_website_not_tracking_user_email')->with([
                        'locale' => Lang::getLocale(),
                        'fullName' => $this->user->full_name,
                    ]);
    }
}
