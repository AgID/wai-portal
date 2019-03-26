<?php

namespace App\Providers;

use App\Listeners\EventToLogSubscriber;
use App\Listeners\SPIDEventSubscriber;
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
        \Illuminate\Auth\Events\Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\Auth\Invited::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    protected $subscribe = [
        SPIDEventSubscriber::class,
        EventToLogSubscriber::class,
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
