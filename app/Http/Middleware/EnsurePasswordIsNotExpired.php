<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class EnsurePasswordIsNotExpired
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->isPasswordExpired()) {
            return redirect()->guest(route('admin.password.change.show'))->withNotification([
                'title' => __('scadenza password'),
                'message' => __('La password è scaduta e deve essere cambiata.'),
                'status' => 'warning',
                'icon' => 'it-error',
            ]);
        }

        return $next($request);
    }
}
