<?php

namespace App\Listeners;

use App\Events\User\UserLogin;
use App\Events\User\UserLogout;
use App\Models\User;
use Italia\SPIDAuth\Events\LoginEvent;
use Italia\SPIDAuth\Events\LogoutEvent;

class SPIDEventSubscriber
{
    /**
     * Handle SPID login events.
     *
     * @param LoginEvent $event
     */
    public function onSPIDLogin(LoginEvent $event)
    {
        auth()->logout();
        $SPIDUser = $event->getSPIDUser();
        $user = User::findByFiscalNumber($SPIDUser->fiscalNumber);
        if (isset($user)) {
            auth()->login($user);

            event(new UserLogin($user));
        }
    }

    /**
     * Handle SPID logout events.
     *
     * @param LogoutEvent $event
     */
    public function onSPIDLogout(LogoutEvent $event)
    {
        if (auth()->check()) {
            $user = auth()->user();

            session()->invalidate();

            event(new UserLogout($user));
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Italia\SPIDAuth\Events\LoginEvent',
            'App\Listeners\SPIDEventSubscriber@onSPIDLogin'
        );

        $events->listen(
            'Italia\SPIDAuth\Events\LogoutEvent',
            'App\Listeners\SPIDEventSubscriber@onSPIDLogout'
        );
    }
}
