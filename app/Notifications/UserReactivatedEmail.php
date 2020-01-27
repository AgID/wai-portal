<?php

namespace App\Notifications;

use App\Mail\UserReactivated;
use App\Models\User;
use Illuminate\Mail\Mailable;

/**
 * User reactivated email notification to public administration administrators.
 */
class UserReactivatedEmail extends UserEmailNotification
{
    /**
     * The reactivated user.
     *
     * @var User the user
     */
    protected $reactivatedUser;

    /**
     * Default constructor.
     *
     * @param User $reactivatedUser the reactivated user
     */
    public function __construct(User $reactivatedUser)
    {
        $this->reactivatedUser = $reactivatedUser;
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
        return new UserReactivated($notifiable, $this->reactivatedUser);
    }
}
