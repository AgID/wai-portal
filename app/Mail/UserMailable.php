<?php

namespace App\Mail;

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
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     */
    public function __construct(User $recipient)
    {
        $this->recipient = $recipient;
    }
}
