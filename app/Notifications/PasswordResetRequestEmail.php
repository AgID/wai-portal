<?php

namespace App\Notifications;

use App\Mail\PasswordReset;
use Illuminate\Mail\Mailable;

class PasswordResetRequestEmail extends UserEmailNotification
{
    protected $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new PasswordReset($notifiable, $this->token);
    }
}
