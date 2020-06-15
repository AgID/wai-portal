<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
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

    /**
     * The mail recipient.
     *
     * @var User the user
     */
    protected $recipient;

    /**
     * The public administration.
     *
     * @var PublicAdministration|null the public administration
     */
    protected $publicAdministration;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param PublicAdministration $publicAdministration the public administration
     */
    public function __construct(User $recipient, ?PublicAdministration $publicAdministration = null)
    {
        $this->recipient = $recipient;
        $this->publicAdministration = $publicAdministration;
    }
}
