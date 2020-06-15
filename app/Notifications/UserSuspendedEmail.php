<?php

namespace App\Notifications;

use App\Mail\UserSuspended;
use App\Models\User;
use Illuminate\Mail\Mailable;

/**
 * User suspended email notification to public administration administrators.
 */
class UserSuspendedEmail extends UserEmailNotification
{
    /**
     * The suspended user.
     *
     * @var User the user
     */
    protected $suspendedUser;

    /**
     * Default constructor.
     *
     * @param User $suspendedUser the suspended user
     */
    public function __construct(User $suspendedUser)
    {
        parent::__construct();
        $this->suspendedUser = $suspendedUser;
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
        return new UserSuspended($notifiable, $this->suspendedUser);
    }
}
