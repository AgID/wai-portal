<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * Public administration not found in iPA email to super-administrators.
 */
class SuperAdminPublicAdministrationNotFoundInIpa extends UserMailable
{
    /**
     * The public administration missing in iPA.
     *
     * @var PublicAdministration the public administration
     */
    protected $publicAdministration;

    /**
     * Default constructor.
     *
     * @param User $recipient the recipient
     * @param PublicAdministration $publicAdministration the missing public administration
     */
    public function __construct(User $recipient, PublicAdministration $publicAdministration)
    {
        parent::__construct($recipient);
        $this->publicAdministration = $publicAdministration;
    }

    /**
     * Build the message.
     *
     * @return SuperAdminPublicAdministrationNotFoundInIpa the email
     */
    public function build(): SuperAdminPublicAdministrationNotFoundInIpa
    {
        return $this->subject(__('Pubblica amministrazione non trovata in iPA'))
            ->markdown('mail.super_admin_public_administration_not_in_ipa')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
