<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\User\UserLogin;
use App\Events\User\UserLogout;
use App\Models\User;
use Italia\SPIDAuth\Events\LoginEvent;
use Italia\SPIDAuth\Events\LogoutEvent;

/**
 * SPID related events subscriber.
 */
class SPIDEventSubscriber
{
    /**
     * Handle SPID login events.
     *
     * @param LoginEvent $event
     */
    public function onSPIDLogin(LoginEvent $event): void
    {
        auth()->logout();
        $SPIDUser = $event->getSPIDUser();
        $user = User::findByFiscalNumber($SPIDUser->fiscalNumber);
        if (isset($user) && $user->isNotAn(UserRole::SUPER_ADMIN)) {
            auth()->login($user);

            event(new UserLogin($user));
        }
    }

    /**
     * Handle SPID logout events.
     *
     * @param LogoutEvent $event
     */
    public function onSPIDLogout(LogoutEvent $event): void
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
    public function subscribe($events): void
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
