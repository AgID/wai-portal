<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

class WebsiteAdded extends UserMailable
{
    protected $website;

    protected $javascriptSnippet;

    public function __construct(User $recipient, Website $website, string $javascriptSnippet)
    {
        parent::__construct($recipient);
        $this->website = $website;
        $this->javascriptSnippet = $javascriptSnippet;
    }

    public function build(): WebsiteAdded
    {
        return $this->subject(__('Sito web aggiunto'))
            ->markdown('mail.added_website')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
                'javascriptSnippet' => $this->javascriptSnippet,
            ]);
    }
}
