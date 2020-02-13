<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * Public administration RTD email address changed email to new RTD.
 */
class RTDEmailAddressChanged extends RTDMailable
{
    /**
     * The oldest active administrator for the public administration.
     *
     * @var User the administrator
     */
    protected $earliestRegisteredAdministrator;

    /**
     * Default constructor.
     *
     * @param PublicAdministration $recipient the recipient
     * @param User $earliestRegisteredAdministrator the oldest active administrator
     */
    public function __construct(PublicAdministration $recipient, User $earliestRegisteredAdministrator)
    {
        parent::__construct($recipient);
        $this->earliestRegisteredAdministrator = $earliestRegisteredAdministrator;
    }

    /**
     * Build the message.
     *
     * @return RTDEmailAddressChanged the email
     */
    public function build(): RTDEmailAddressChanged
    {
        return $this->subject(__('Nuovo indirizzo email RTD'))
            ->markdown('mail.rtd_email_changed')->with([
                'locale' => Lang::getLocale(),
                'publicAdministration' => $this->recipient,
                'earliestRegisteredAdministrator' => $this->earliestRegisteredAdministrator,
            ]);
    }
}
