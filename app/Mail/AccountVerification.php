<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountVerification extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The user this mail will be sent to.
     *
     * @var User
     */
    protected $user;

    /**
     * The signed url user for account verification.
     *
     * @var string
     */
    protected $signedUrl;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user, string $signedUrl)
    {
        $this->user = $user;
        $this->signedUrl = $signedUrl;
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
            if ($this->user->isA('super-admin')) {
                $mailTemplate = 'mail.admin_invited_verification';
            } else {
                $mailTemplate = 'mail.invited_verification';
            }
        } else {
            $mailTemplate = 'mail.verification';
        }
        //TODO: make sender configurable
        return $this->from('noreply@analytics.italia.it')
                    ->subject('Please verify your email') //TODO: string in lang file
                    ->markdown($mailTemplate)->with([
                        'user' => $this->user,
                        'signedUrl' => $this->signedUrl,
                    ]);
    }
}
