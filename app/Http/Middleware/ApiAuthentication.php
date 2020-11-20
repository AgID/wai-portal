<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Middleware\StartSession;
use Silber\Bouncer\BouncerFacade as Bouncer;

class ApiAuthentication extends StartSession
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
        $publicAdministration = get_public_administration_from_token();

        session()->put('tenant_id', $publicAdministration->id);
        Bouncer::scope()->to($publicAdministration->id);

        return $next($request);
    }
}
