<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

class UserWebsitePurged extends UserMailable
{
    protected $website;

    public function __construct(User $recipient, $website)
    {
        parent::__construct($recipient);
        $this->website = $website;
    }

    public function build(): UserWebsitePurged
    {
        return $this->subject(__('[Attenzione] - Sito web eliminato'))
            ->markdown('mail.user_website_purged')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
