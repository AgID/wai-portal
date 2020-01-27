<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

class UserWebsiteUnarchived extends UserMailable
{
    protected $website;

    public function __construct(User $recipient, Website $website)
    {
        parent::__construct($recipient);
        $this->website = $website;
    }

    public function build(): UserWebsiteUnarchived
    {
        return $this->subject(__('[Info] - Sito web riattivato'))
            ->markdown('mail.user_website_unarchived')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
