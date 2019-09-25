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
     * The user to notify.
     *
     * @var User the user
     */
    protected $user;

    /**
     * The archived website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Manual flag.
     *
     * @var bool wether the website was archived manually
     */
    protected $manual;

    /**
     * Mail constructor.
     *
     * @param User $user the user
     * @param Website $website the website
     * @param bool $manual wether the website was archived manually
     */
    public function __construct(User $user, Website $website, bool $manual)
    {
        $this->user = $user;
        $this->website = $website;
        $this->manual = $manual;
    }

    /**
     * Build the mail.
     *
     * @return UserWebsiteArchived the mail
     */
    public function build(): UserWebsiteArchived
    {
        return $this->subject(__('[Info] - Sito web archiviato'))
                    ->markdown('mail.website_archived_user_email')->with([
                        'locale' => Lang::getLocale(),
                        'fullName' => $this->user->full_name,
                        'website' => $this->website->name,
                        'manual' => $this->manual,
                        'expire' => config('wai.archive_expire'),
                    ]);
    }
}
