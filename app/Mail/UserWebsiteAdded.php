<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

class UserWebsiteAdded extends UserMailable
{
    protected $website;

    public function __construct(User $recipient, Website $website)
    {
        parent::__construct($recipient);
        $this->website = $website;
    }

    public function build(): UserWebsiteAdded
    {
        return $this->subject(__('[Info] - Sito web aggiunto'))
            ->markdown('mail.user_website_added')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
