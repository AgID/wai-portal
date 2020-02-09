<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * Public administration registered email.
 */
class PublicAdministrationRegistered extends UserMailable
{
    /**
     * The registered public administration.
     *
     * @var PublicAdministration the public administration
     */
    protected $publicAdministration;

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
     * @param PublicAdministration $publicAdministration the registered public administration
     * @param string $javascriptSnippet the tracking code
     */
    public function __construct(User $recipient, PublicAdministration $publicAdministration, string $javascriptSnippet)
    {
        parent::__construct($recipient);
        $this->publicAdministration = $publicAdministration;
        $this->javascriptSnippet = $javascriptSnippet;
    }

    /**
     * Build the message.
     *
     * @return PublicAdministrationRegistered the email
     */
    public function build(): PublicAdministrationRegistered
    {
        return $this->subject(__('Pubblica amministrazione registrata'))
            ->markdown('mail.registered_public_administration')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
                'javascriptSnippet' => $this->javascriptSnippet,
            ]);
    }
}
