<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * User mail for website scheduled for purging notification.
 */
class UserWebsitePurging extends Mailable
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
     * The activated website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Mail constructor.
     *
     * @param User $user the user
     * @param Website $website the website
     */
    public function __construct(User $user, Website $website)
    {
        $this->user = $user;
        $this->website = $website;
    }

    /**
     * Build the mail.
     *
     * @return UserWebsitePurging the mail
     */
    public function build(): UserWebsitePurging
    {
        return $this->subject(__('[Attenzione] - Avviso rimozione'))
                    ->markdown('mail.website_purging_user_email')->with([
                        'locale' => Lang::getLocale(),
                        'fullName' => $this->user->full_name,
                        'website' => $this->website->name,
                    ]);
    }
}
