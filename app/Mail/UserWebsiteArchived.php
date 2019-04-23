<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * User mail for website archived notification.
 */
class UserWebsiteArchived extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var User the user
     */
    protected $user;

    /**
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
     * @return UserWebsiteArchived the mail
     */
    public function build(): UserWebsiteArchived
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject(trans('mail.website.archived.user.subject'))
            ->markdown('mail.website_archived_user_email')->with([
                'locale' => Lang::getLocale(),
                'fullName' => $this->user->full_name,
                'website' => $this->website->name,
                'expire' => config('wai.archive_expire'),
            ]);
    }
}
