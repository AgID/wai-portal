<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Events\User\UserInvitationLinkExpired;
use App\Exceptions\ExpiredInvitationException;
use App\Exceptions\ExpiredVerificationException;
use App\Models\User;
use Closure;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\URL;

/**
 * Generated signed URL validation middleware.
 */
class ValidateSignature
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request the request
     * @param \Closure $next the next closure
     *
     * @throws \App\Exceptions\ExpiredInvitationException if invitation link is expired
     * @throws \App\Exceptions\ExpiredVerificationException if verification link is expired
     * @throws \Illuminate\Routing\Exceptions\InvalidSignatureException if verification link is expired
     *
     * @return \Illuminate\Http\Response the response
     */
    public function handle($request, Closure $next)
    {
        if ($request->hasValidSignature()) {
            return $next($request);
        }

        // if this request hasn't a valid signature but it's correct then must be expired
        if ($request->route()->named('verification.verify') && URL::hasCorrectSignature($request)) {
            $user = User::where('uuid', $request->route('uuid'))->first();
            if ($user && $user->status->is(UserStatus::INVITED)) {
                event(new UserInvitationLinkExpired($user));

                throw new ExpiredInvitationException();
            }

            throw new ExpiredVerificationException();
        }

        throw new InvalidSignatureException();
    }
}
