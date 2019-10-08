<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Events\User\UserInvitationLinkExpired;
use App\Exceptions\ExpiredInvitationException;
use App\Exceptions\ExpiredVerificationException;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Routing\Middleware\ValidateSignature as Middleware;
use Illuminate\Support\Facades\Route;

/**
 * Generated URL validation middleware.
 */
class ValidateSignature extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request the request
     * @param \Closure $next the next closure
     *
     * @throws \App\Exceptions\ExpiredInvitationException if invitation link is expired
     * @throws \App\Exceptions\ExpiredVerificationException if verification link is expired
     *
     * @return \Illuminate\Http\Response the response
     */
    public function handle($request, Closure $next)
    {
        $expire = $request->query('expires');
        $uuid = $request->route('uuid');

        if ($expire && $uuid && 'verification.verify' === Route::currentRouteName() && Carbon::now()->getTimestamp() > $expire) {
            $user = User::where('uuid', $uuid)->first();
            if ($user->status->is(UserStatus::INVITED)) {
                event(new UserInvitationLinkExpired($user));

                throw new ExpiredInvitationException();
            }

            throw new ExpiredVerificationException();
        }

        return parent::handle($request, $next);
    }
}
