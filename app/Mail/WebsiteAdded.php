<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * Website added email.
 */
class WebsiteAdded extends UserMailable
{
    /**
     * The added website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * The Javascript tracking code.
     *
     * @var string the tracking code
     */
    protected $javascriptSnippet;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param Website $website the added website
     * @param string $javascriptSnippet the tracking code
     */
    public function __construct(User $recipient, Website $website, string $javascriptSnippet)
    {
        parent::__construct($recipient);
        $this->website = $website;
        $this->javascriptSnippet = $javascriptSnippet;
    }

    /**
     * Build the message.
     *
     * @return WebsiteAdded the email
     */
    public function build(): WebsiteAdded
    {
        return $this->subject(__('Nuovo sito web aggiunto'))
            ->markdown('mail.added_website')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
                'javascriptSnippet' => $this->javascriptSnippet,
            ]);
    }
}
