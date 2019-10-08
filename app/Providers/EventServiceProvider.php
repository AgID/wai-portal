<?php

namespace App\Providers;

use App\Events\User\UserInvitationLinkExpired;
use App\Events\User\UserInvited;
use App\Listeners\CheckPendingWebsiteJobsEventsSubscriber;
use App\Listeners\CheckWebsitesMonitoringJobEventsSubscriber;
use App\Listeners\LogSentMessage;
use App\Listeners\PublicAdministrationEventsSubscriber;
use App\Listeners\SendInvitationNotification;
use App\Listeners\SPIDEventSubscriber;
use App\Listeners\UpdatePublicAdministrationsFromIpaJobEventsSubscriber;
use App\Listeners\UserEventsSubscriber;
use App\Listeners\UserExpiredInvitationListener;
use App\Listeners\UsersJobEventsSubscriber;
use App\Listeners\WebsiteEventsSubscriber;
use App\Listeners\WebsitesJobEventSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSent;

/**
 * The application event provider.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array the listeners list
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserInvited::class => [
            SendInvitationNotification::class,
        ],
        MessageSent::class => [
            LogSentMessage::class,
        ],
        UserInvitationLinkExpired::class => [
            UserExpiredInvitationListener::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array the subscribers list
     */
    protected $subscribe = [
        SPIDEventSubscriber::class,
        UserEventsSubscriber::class,
        UpdatePublicAdministrationsFromIpaJobEventsSubscriber::class,
        PublicAdministrationEventsSubscriber::class,
        WebsiteEventsSubscriber::class,
        CheckPendingWebsiteJobsEventsSubscriber::class,
        CheckWebsitesMonitoringJobEventsSubscriber::class,
        UsersJobEventsSubscriber::class,
        WebsitesJobEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
