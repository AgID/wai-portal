<?php

namespace App\Providers;

use App\Events\Auth\UserInvited;
use App\Listeners\EventToLogSubscriber;
use App\Listeners\IPAJobEventsSubscriber;
use App\Listeners\PublicAdministrationEventsSubscriber;
use App\Listeners\SendInvitationNotification;
use App\Listeners\SPIDEventSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserInvited::class => [
            SendInvitationNotification::class,
        ],
    ];

    protected $subscribe = [
        SPIDEventSubscriber::class,
        EventToLogSubscriber::class,
        IPAJobEventsSubscriber::class,
        PublicAdministrationEventsSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
