<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use App\Traits\ManageRecipientNotifications;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * User mailable template.
 */
abstract class UserMailable extends Mailable
{
    use Queueable;
    use SerializesModels;
    use ManageRecipientNotifications;

    /**
     * The mail recipient.
     *
     * @var User the user
     */
    protected $recipient;
    protected $publicAdministration;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     */
    public function __construct(User $recipient, ?PublicAdministration $publicAdministration = null)
    {
        $recipient->email = $this->recipientSetSpecificEmailForUserPublicAdministration($recipient, $publicAdministration);
        $this->recipient = $recipient;
        $this->publicAdministration = $publicAdministration;
    }
}
