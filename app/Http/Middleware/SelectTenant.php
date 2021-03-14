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
        $noRedirectRoutes = [
            'websites.index',
            'analytics',
        ];

        if ($authUser && !$authUser->isA(UserRole::SUPER_ADMIN)) {
            if (empty(session('tenant_id'))) {
                $publicAdministrationsCount = $authUser->publicAdministrationsWithSuspended->count();
                if (1 === $publicAdministrationsCount) {
                    $publicAdministration = $authUser->publicAdministrationsWithSuspended()->first();
                    $userPublicAdministrationStatus = $authUser->getStatusforPublicAdministration($publicAdministration);

                    if ($userStatus->is(UserStatus::ACTIVE)) {
                        session()->put('tenant_id', $publicAdministration->id);
                        Bouncer::scope()->to($publicAdministration->id);

                        return $next($request);
                    }
                }

                if (!$request->routeIs($noRedirectRoutes)) {
                    return redirect()->route('publicAdministrations.show');
                }

                return $next($request);
            }

            if ($authUser->publicAdministrations->where('id', session('tenant_id'))->isEmpty()) {
                $request->session()->forget('tenant_id');

                return redirect()->route('publicAdministrations.show');
            }
        }

        return $next($request);
    }
}
