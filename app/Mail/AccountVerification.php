<?php

namespace App\Mail;

use App\Models\PublicAdministration;
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
    public $user;

    /**
     * The public administration selected for the invitation.
     *
     * @var App\Models\PublicAdministration
     */
    public $publicAdministration;

    /**
     * The user issuing the invitation.
     *
     * @var App\Models\User
     */
    public $invitedBy;

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
    public function __construct(User $user, PublicAdministration $publicAdministration = null, User $invitedBy = null, string $signedUrl)
    {
        $this->user = $user;
        $this->publicAdministration = $publicAdministration;
        $this->invitedBy = $invitedBy;
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
                        'publicAdministration' => $this->publicAdministration,
                        'invitedBy' => $this->invitedBy,
                        'signedUrl' => $this->signedUrl,
                    ]);
    }
}
