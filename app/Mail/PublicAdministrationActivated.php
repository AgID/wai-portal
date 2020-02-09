<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * Public administration activated email.
 */
class PublicAdministrationActivated extends UserMailable
{
    /**
     * The activated public administration.
     *
     * @var PublicAdministration the public administration
     */
    protected $publicAdministration;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param PublicAdministration $publicAdministration the activated public administration
     */
    public function __construct(User $recipient, PublicAdministration $publicAdministration)
    {
        parent::__construct($recipient);
        $this->publicAdministration = $publicAdministration;
    }

    /**
     * Build the message.
     *
     * @return PublicAdministrationActivated the email
     */
    public function build(): PublicAdministrationActivated
    {
        return $this->subject(__('Pubblica amministrazione attivata'))
            ->markdown('mail.activated_public_administration')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
