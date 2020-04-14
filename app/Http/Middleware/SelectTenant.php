<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;

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
        if ($authUser) {
            if (empty(session('tenant_id')) && $authUser->publicAdministrations->isNotEmpty()) {
                if (1 === count($authUser->publicAdministrations)) {
                    session()->put('tenant_id', $authUser->publicAdministrations()->first()->id);
                } elseif (!$request->is('select-public-administration')) {
                    return redirect()->route('publicAdministration.tenant.select');
                }
            }

            if ($authUser->isA(UserRole::SUPER_ADMIN)) {
                $selectedPublicAdministrationIpaCode = $request->route('publicAdministration');
                if (is_object($selectedPublicAdministrationIpaCode)) {
                    $selectedPublicAdministrationIpaCode = $selectedPublicAdministrationIpaCode->ipa_code;
                }

                if (empty(session('super_admin_tenant_ipa_code')) && $selectedPublicAdministrationIpaCode) {
                    session()->put('super_admin_tenant_ipa_code', $selectedPublicAdministrationIpaCode);
                }
            } else {
                $selectedPublicAdministrationId = $request->query('publicAdministration');
                if (is_object($selectedPublicAdministrationId)) {
                    $selectedPublicAdministrationId = $selectedPublicAdministrationId->id;
                }

                if (empty(session('tenant_id')) && $selectedPublicAdministrationId) {
                    session()->put('tenant_id', $selectedPublicAdministrationId);
                }
            }
        }

        return $next($request);
    }
}
