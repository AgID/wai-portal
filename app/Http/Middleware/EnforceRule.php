<?php

namespace App\Http\Middleware;

use App\Enums\UserPermission;
use App\Enums\UserStatus;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class EnforceRule
{
    public function handle($request, Closure $next, ...$rules)
    {
        if (in_array('forbid-spid', $rules, true) && app()->make('SPIDAuth')->isAuthenticated()) {
            throw new AuthorizationException('SPID authenticated users are not authorized for route ' . $request->route()->getName() . '.');
        }

        if (in_array('forbid-invited', $rules, true) && $request->user()->status->is(UserStatus::INVITED)) {
            $redirectTo = $request->user()->can(UserPermission::ACCESS_ADMIN_AREA) ?
                route('admin.verification.notice') :
                route('verification.notice');

            return redirect($redirectTo);
        }

        return $next($request);
    }
}
