<?php

namespace App\Listeners;

use App\Models\User;
use Italia\SPIDAuth\Events\LoginEvent;
use Italia\SPIDAuth\Events\LogoutEvent;

class SPIDEventSubscriber
{
    /**
     * Handle SPID login events.
     */
    public function onSPIDLogin(LoginEvent $event) {
        $SPIDUser = $event->getSPIDUser();
        $user = User::findByFiscalNumber($SPIDUser->fiscalNumber);
        if (isset($user) && $user->status != 'invited') {
            auth()->login($user);

            logger()->info('User '.$user->getInfo().' logged in.');
        }
    }

    /**
     * Handle SPID logout events.
     */
    public function onSPIDLogout(LogoutEvent $event) {
        if (auth()->check()) {
            $user = auth()->user();

            logger()->info('User '.$user->getInfo().' logged out.');

            auth()->logout();
            session()->save();
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher $events
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
