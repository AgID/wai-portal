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
     * @var stdClass the purged public administration
     */
    protected $purgedPublicAdministration;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param stdClass $publicAdministration the purged public administration
     */
    public function __construct(User $recipient, $purgedPublicAdministration)
    {
        parent::__construct($recipient);
        $this->purgedPublicAdministration = $purgedPublicAdministration;
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
                'publicAdministration' => $this->purgedPublicAdministration,
            ]);
    }
}
