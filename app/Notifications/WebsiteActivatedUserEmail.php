<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

/**
 * Website activated user notification.
 */
class WebsiteActivatedUserEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The activated website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Notification constructor.
     *
     * @param Website $website the website
     */
    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    /**
     * Notification channels.
     *
     * @param User $notifiable the user
     *
     * @return array the channels
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the message.
     *
     * @param User $notifiable the user
     *
     * @return MailMessage the mail message
     */
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage())
            ->from(Config::get('mail.from.address'), Config::get('mail.from.name'))
            ->subject(Lang::get('mail.website.activated.user.subject'))
            ->markdown('mail.website_activated_user_email')->with([
                'locale' => Lang::getLocale(),
                'fullName' => $notifiable->full_name,
                'website' => $this->website->name,
            ]);
    }
}
