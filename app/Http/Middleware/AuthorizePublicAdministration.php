<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Closure;
use Illuminate\Auth\AuthenticationException;

/**
 * Analytics Service authorization middleware.
 */
class AuthorizePublicAdministration
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request the request
     * @param \Closure $next the next closure
     *
     * @return mixed the check result
     */
    public function handle($request, Closure $next)
    {
        $authUser = $request->user();

        if ( !empty(session('tenant_id')) && $authUser->suspendedPublicAdministrations->where('id', session('tenant_id'))->isNotEmpty()) {
            $publicAdministrationUser = $authUser->suspendedPublicAdministrations->where('id', session('tenant_id'))->first();
            throw new AuthenticationException('User suspended.', [], $this->redirectSuspendedTo($request,  $publicAdministrationUser));
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are suspended.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function redirectSuspendedTo($request, $publicAdministration): string
    {
        $request->session()->flash('notification', [
            'title' => __('accesso negato'),
            'message' => __("L'utenza su <b>:name</b> è stata sospesa.", ['name' => $publicAdministration->name] ),
            'status' => 'error',
            'icon' => 'it-close-circle',
        ]);

        return route('publicAdministrations.show');
    }
}
