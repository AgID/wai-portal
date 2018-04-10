<?php

namespace App\Http\Middleware;

use Closure;

class Authenticate
{
    /**
     * Handle an incoming request. User is already authenticated with SPID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->guest(route('auth-register'))
                             ->withMessage(['warning' => "Prima di usare l'applicazione è necessario completare la registrazione"]); //TODO: put message in lang file
        } elseif (in_array(auth()->user()->status, ['inactive', 'invited'])) {
            return redirect()->route('auth-verify');
        } elseif (auth()->user()->status == 'suspended') {
            abort(403, "L'utenza è stata sospesa.");// TODO: put in lang file
        }
        return $next($request);
    }
}
