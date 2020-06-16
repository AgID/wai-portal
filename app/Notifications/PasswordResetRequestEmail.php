<?php

namespace App\Notifications;

use App\Mail\PasswordReset;
use Illuminate\Mail\Mailable;

/**
 * Password reset request email notification.
 */
class PasswordResetRequestEmail extends UserEmailNotification
{
    /**
     * The password reset token.
     *
     * @var string the token
     */
    protected $token;

    /**
     * Default constructor.
     *
     * @param string $token the reset token
     */
    public function __construct(string $token)
    {
        parent::__construct();

        $this->token = $token;
    }

    /**
     * Initialize the mail message.
     *
     * @param mixed $notifiable the target
     *
     * @return Mailable the mail message
     */
    protected function buildEmail($notifiable): Mailable
    {
        return new PasswordReset($notifiable, $this->token);
    }
}
