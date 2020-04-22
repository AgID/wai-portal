<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * Website added email to public administration administrators.
 */
class UserSimplePublicAdministrationInvite extends UserMailable
{
    private $publicAdministration;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param Website $website the added website
     */
    public function __construct(User $recipient, PublicAdministration $publicAdministration)
    {
        parent::__construct($recipient);
        $this->publicAdministration = $publicAdministration;
    }

    /**
     * Build the message.
     *
     * @return UserWebsiteAdded the email
     */
    public function build(): UserSimplePublicAdministrationInvite
    {
        logger()->notice('UserSimplePublicAdministrationInvite@build');

        return $this->subject(__('Invito alla :pa su :app', ['pa' => $this->publicAdministration->name, 'app' => config('app.name')]))
            ->markdown('mail.user_invited_no_link')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
