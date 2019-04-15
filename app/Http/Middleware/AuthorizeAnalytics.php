<?php

namespace App\Http\Middleware;

use App\Enums\WebsiteStatus;
use Closure;

/**
 * Analytics Service authorization middleware.
 */
class AuthorizeAnalytics
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request the request
     * @param \Closure $next the next closure
     * @param string $action the requested analytics action
     *
     * @return mixed the check result
     */
    public function handle($request, Closure $next, string $action)
    {
        $unauthorized = false;
        if (!$request->user()->can($action)) {
            $unauthorized = true;
        }
        if ($request->route('website')) {
            if (current_public_administration()->id !== $request->route('website')->publicAdministration->id) {
                $unauthorized = true;
            } elseif ($request->route('website')->status->is(WebsiteStatus::PENDING)) {
                $unauthorized = false;
            }
        }
        if ($unauthorized) {
            abort(403);
        }

        return $next($request);
    }
}
