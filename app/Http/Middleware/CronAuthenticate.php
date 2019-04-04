<?php

namespace App\Http\Middleware;

use Closure;

/**
 * CronJob authentication middleware.
 */
class CronAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     * @param \Closure $next the next closure
     *
     * @return mixed the next closure or a JSON response if handling fails
     */
    public function handle($request, Closure $next)
    {
        if (!empty(config('cron-auth.cron_token')) && $request->input('token') === config('cron-auth.cron_token')) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
