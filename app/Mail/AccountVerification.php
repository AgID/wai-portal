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
     * The token user for account verification
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
     * @param string The verification token.
     * @return $this
     */
    public function build()
    {
        //TODO: make sender configurable
        return $this->from('noreply@analytics.italia.it')
                    ->subject('Please verify your email') //TODO: string in lang file
                    ->markdown('email.verification')->with([
                        'user' => $this->user,
                        'token' => $this->token
                    ]);
    }
}
