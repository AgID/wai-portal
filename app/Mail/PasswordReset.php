<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * Password reset request email.
 */
class PasswordReset extends UserMailable
{
    /**
     * The token user for password reset.
     *
     * @var string
     */
    protected $token;

    /**
     * Create a new message instance.
     *
     * @param User $recipient the user requesting the password change
     * @param string $token the reset token
     */
    public function __construct(User $recipient, string $token)
    {
        parent::__construct($recipient);
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return PasswordReset the email
     */
    public function build(): PasswordReset
    {
        return $this->subject(__('Reset della password'))
            ->markdown('mail.admin_password_reset')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'token' => $this->token,
            ]);
    }
}
