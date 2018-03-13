<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
                    ->markdown('email.verification')->with(['user' => $this->user]);
    }
}
