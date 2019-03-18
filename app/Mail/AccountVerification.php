<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountVerification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user this mail will be sent to.
     *
     * @var User
     */
    protected $user;

    /**
     * The token user for account verification.
     *
     * @var string
     */
    protected $token;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @param string the verification token
     *
     * @return $this
     */
    public function build()
    {
        if ('invited' == $this->user->status) {
            if ($this->user->can('access-admin-area')) {
                $mailTemplate = 'email.admin_invited_verification';
            } else {
                $mailTemplate = 'email.invited_verification';
            }
        } else {
            $mailTemplate = 'email.verification';
        }
        //TODO: make sender configurable
        return $this->from('noreply@analytics.italia.it')
                    ->subject('Please verify your email') //TODO: string in lang file
                    ->markdown($mailTemplate)->with([
                        'user' => $this->user,
                        'token' => $this->token,
                    ]);
    }
}
