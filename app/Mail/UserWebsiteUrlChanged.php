<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

class UserWebsiteUrlChanged extends UserMailable
{
    protected $website;

    public function __construct(User $recipient, Website $website)
    {
        parent::__construct($recipient);
        $this->website = $website;
    }

    public function build(): UserWebsiteUrlChanged
    {
        return $this->subject(__('[Info] - URL sito web modificato'))
            ->markdown('mail.user_website_url_changed')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
