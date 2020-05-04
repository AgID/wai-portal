<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
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

                switch ($authUser->activePublicAdministrations->count()) {
                    case 1:
                        $publicAdministrationId = $authUser->activePublicAdministrations()->first()->id;
                        session()->put('tenant_id', $publicAdministrationId);
                        Bouncer::scope()->to($publicAdministrationId);

                        break;
                    default:
                        if (!$request->is($selectNoRedirectRoutes)) {
                            return redirect()->route('publicAdministrations.show');
                        }

                        break;
                }
            }
        }

        return $next($request);
    }
}
