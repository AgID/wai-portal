<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;

class Authenticate
{
    /**
     * Handle an incoming request. User is already authenticated with SPID.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return redirect()->route('auth-register')
                ->withMessage(['warning' => "Prima di usare l'applicazione è necessario completare la registrazione"]); //TODO: put message in lang file
        }

        if (UserStatus::SUSPENDED == $request->user()->status) {
            abort(403, "L'utenza è stata sospesa."); // TODO: put in lang file
        }

        return $next($request);
    }
}
