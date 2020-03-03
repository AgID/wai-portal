<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * Public administration registered email to RTD.
 */
class RTDPublicAdministrationRegistered extends RTDMailable
{
    /**
     * The registering user.
     *
     * @var User the user
     */
    protected $registeringUser;

    /**
     * Default constructor.
     *
     * @param PublicAdministration $recipient the mail recipient
     * @param User $registeringUser the registering user
     */
    public function __construct(PublicAdministration $recipient, User $registeringUser)
    {
        $this->registeringUser = $registeringUser;
        parent::__construct($recipient);
    }

    /**
     * Build the message.
     *
     * @return RTDPublicAdministrationRegistered the email
     */
    public function build(): RTDPublicAdministrationRegistered
    {
        return $this->subject(__('Pubblica amministrazione registrata'))
            ->markdown('mail.rtd_public_administration_registered')->with([
                'locale' => Lang::getLocale(),
                'publicAdministration' => $this->recipient,
                'registeringUser' => $this->registeringUser,
            ]);
    }
}
