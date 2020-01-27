<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * Public administration purged email.
 */
class PublicAdministrationPurged extends UserMailable
{
    /**
     * The purged public administration.
     *
     * @var mixed the public administration
     */
    protected $publicAdministration;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param mixed $publicAdministration the purged public administration
     */
    public function __construct(User $recipient, $publicAdministration)
    {
        parent::__construct($recipient);
        $this->publicAdministration = $publicAdministration;
    }

    /**
     * Build the message.
     *
     * @return PublicAdministrationPurged the email
     */
    public function build(): PublicAdministrationPurged
    {
        return $this->subject(__('Pubblica amministrazione eliminata'))
            ->markdown('mail.purged_public_administration')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
