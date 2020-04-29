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
            if (empty(session('tenant_id')) && $authUser->publicAdministrations->isNotEmpty()) {
                $selectNoRedirectRoutes = ['public-administrations', 'public-administrations/*', 'spid/logout',
                    'user/verify', 'user/verify/*', 'search-ipa-index', 'websites/store-primary', ];

                $activePublicAdministrations = $authUser->publicAdministrations()->where('pa_status', UserStatus::ACTIVE)->get()->toArray();
                switch (count($activePublicAdministrations)) {
                    case 1:
                        $publicAdministrationId = $authUser->publicAdministrations()->where('pa_status', UserStatus::ACTIVE)->first()->id;
                        session()->put('tenant_id', $publicAdministrationId);
                        Bouncer::scope()->to($publicAdministrationId);
                        break;
                    case 0:
                        if (!$request->is($selectNoRedirectRoutes)) {
                            return redirect()->route('publicAdministrations.show');
                        }
                        break;
                    default:
                        if (!$request->is($selectNoRedirectRoutes)) {
                            return redirect()->route('publicAdministrations.select');
                        }
                        break;
                }
            }
        }

        return $next($request);
    }
}
