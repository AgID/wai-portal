<?php

if (!function_exists('current_public_administration')) {
    /**
     * Get the public administration corresponding to the current selcted tenant.
     *
     * @return @return PublicAdministration|null the PublicAdministration found or null if not found
     */
    function current_public_administration()
    {
        if (auth()->user()) {
            return auth()->user()->publicAdministrations()->where('id', session('tenant_id'))->first();
        }

        return null;
    }
}

if (!function_exists('current_user_auth_token')) {
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return string|null
     */
    function current_user_auth_token()
    {
        if (auth()->user()) {
            return app()->make('analytics-service')->getUserAuthToken(auth()->user()->uuid, md5(auth()->user()->analytics_password));
        }

        return null;
    }
}
