<?php

namespace App\Notifications;

use App\Mail\UserWebsiteAccessChanged;
use App\Models\User;
use Illuminate\Mail\Mailable;

/**
 * User website access changed email notification to public administration administrators.
 */
class UserWebsiteAccessChangedEmail extends UserEmailNotification
{
    /**
     * The modified user.
     *
     * @var User the user
     */
    protected $modifiedUser;

    /**
     * Default constructor.
     *
     * @param User $modifiedUser the modified user
     */
    public function __construct(User $modifiedUser)
    {
        $this->modifiedUser = $modifiedUser;
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
        return new UserWebsiteAccessChanged($notifiable, $this->modifiedUser);
    }
}
