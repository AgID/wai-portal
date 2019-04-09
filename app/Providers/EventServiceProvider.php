<?php

namespace App\Providers;

use App\Events\Auth\UserInvited;
use App\Listeners\CheckPendingWebsiteJobsEventsSubscriber;
use App\Listeners\IPAJobEventsSubscriber;
use App\Listeners\PublicAdministrationEventsSubscriber;
use App\Listeners\SendInvitationNotification;
use App\Listeners\SPIDEventSubscriber;
use App\Listeners\UserEventsSubscriber;
use App\Listeners\WebsiteEventsSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array the subscribers list
     */
    protected $subscribe = [
        SPIDEventSubscriber::class,
        UserEventsSubscriber::class,
        IPAJobEventsSubscriber::class,
        PublicAdministrationEventsSubscriber::class,
        WebsiteEventsSubscriber::class,
        CheckPendingWebsiteJobsEventsSubscriber::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
