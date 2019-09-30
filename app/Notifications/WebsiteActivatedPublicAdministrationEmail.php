<?php

namespace App\Notifications;

use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Website activated Public Administration PEC notification.
 */
class WebsiteActivatedPublicAdministrationEmail extends Notification implements ShouldQueue
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
     * @param PublicAdministration $notifiable the public administration
     *
     * @return array the channels array
     */
    public function via(PublicAdministration $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Create mail message.
     *
     * @param PublicAdministration $notifiable the public administrationr
     *
     * @return mixed
     */
    public function toMail($notifiable)
    {
        //TODO: creare il mail message: attività "Invio mail e PEC"
        return null;
    }
}
