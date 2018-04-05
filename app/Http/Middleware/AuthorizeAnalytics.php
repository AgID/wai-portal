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
        $unauthorized = false;
        if (!$request->user()->can($action)) {
            $unauthorized = true;
        }
        if ($request->route('website')) {
            if ($request->user()->publicAdministration != $request->route('website')->publicAdministration) {
                $unauthorized = true;
            } elseif ($request->route('website')->status == 'pending') {
                $unauthorized = false;
            }
        }
        if ($unauthorized) {
            abort(403, __('ui.pages.403.description'));
        }
        return $next($request);
    }
}
