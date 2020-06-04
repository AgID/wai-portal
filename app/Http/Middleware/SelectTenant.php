<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Closure;
use Silber\Bouncer\BouncerFacade as Bouncer;

class SelectTenant
{
    /**
     * Check whether the session has a tenant selected for the current request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authUser = $request->user();

        if ($authUser && !$authUser->isA(UserRole::SUPER_ADMIN)) {
            if (empty(session('tenant_id')) && $authUser->publicAdministrationsWithSuspended->isNotEmpty()) {
                switch ($authUser->publicAdministrationsWithSuspended->count()) {
                    case 1:
                        $publicAdministration = $authUser->publicAdministrationsWithSuspended()->first();
                        $userStatus = UserStatus::coerce(intval($publicAdministration->pivot->user_status));
                        if ($userStatus->is(UserStatus::ACTIVE)) {
                            session()->put('tenant_id', $publicAdministration->id);
                            Bouncer::scope()->to($publicAdministration->id);
                            break;
                        }
                        // no break
                    default:
                        return redirect()->route('publicAdministrations.show');
                        break;
                }
            } elseif (!empty(session('tenant_id')) && $authUser->publicAdministrations->where('id', session('tenant_id'))->isEmpty()) {
                $request->session()->forget('tenant_id');

                return redirect()->route('publicAdministrations.show');
            }
        }

        return $next($request);
    }
}
