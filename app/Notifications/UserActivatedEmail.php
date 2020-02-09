<?php

namespace App\Notifications;

use App\Mail\UserActivated;
use App\Models\User;
use Illuminate\Mail\Mailable;

/**
 * User activated email notification to public administration administrators.
 */
class UserActivatedEmail extends UserEmailNotification
{
    /**
     * The activated user.
     *
     * @var User the user
     */
    private $activatedUser;

    /**
     * Default constructor.
     *
     * @param User $activatedUser the activated user
     */
    public function __construct(User $activatedUser)
    {
        $this->activatedUser = $activatedUser;
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
        return new UserActivated($notifiable, $this->activatedUser);
    }
}
