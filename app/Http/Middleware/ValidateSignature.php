<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserInvitationLinkExpired;
use App\Exceptions\ExpiredInvitationException;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Routing\Middleware\ValidateSignature as Middleware;

class ValidateSignature extends Middleware
{

    public function handle($request, Closure $next)
    {
        $expire = $request->query('expires');
        $uuid = $request->route('uuid');
        if ($expire && $uuid && Carbon::now()->getTimestamp() > $expire) {
            $user = User::where('uuid', $uuid)->first();
            if ($user->status->is(UserStatus::INVITED) && (!$user->isA(UserRole::SUPER_ADMIN))) {
                event(new UserInvitationLinkExpired($user));

                throw new ExpiredInvitationException();
            }
        }

        return parent::handle($request, $next);
    }
}
