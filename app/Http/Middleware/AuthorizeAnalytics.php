<?php

namespace App\Http\Middleware;

use Closure;

class AuthorizeAnalytics
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $action
     * @return mixed
     */
    public function handle($request, Closure $next, string $action)
    {
        if (!$request->user()->can($action) ||
                ($request->route('website') &&
                    $request->user()->publicAdministration !=
                    $request->route('website')->publicAdministration)) {

            logger()->info('User '.auth()->user()->getInfo().' requested an unauthorized resource.');

            abort(403);
        }
        return $next($request);
    }
}
