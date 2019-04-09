<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;

/**
 * User authenticattion.
 */
class Authenticate
{
    /**
     * Handle an incoming request. User is already authenticated with SPID.
     *
     * @param \Illuminate\Http\Request $request the request
     * @param \Closure $next the next closure
     *
     * @return mixed the check result
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return redirect()->route('auth-register')
                ->withMessage(['warning' => "Prima di usare l'applicazione è necessario completare la registrazione"]); //TODO: put message in lang file
        }

        if ($request->user()->status->is(UserStatus::SUSPENDED)) {
            abort(403, "L'utenza è stata sospesa."); // TODO: put in lang file
        }

        return $next($request);
    }
}
