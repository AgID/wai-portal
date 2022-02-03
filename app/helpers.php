<?php

use App\Models\PublicAdministration;
use App\Models\User;

if (!function_exists('current_public_administration')) {
    /**
     * Get the public administration corresponding to the current selected tenant.
     *
     * @return PublicAdministration|null the PublicAdministration found or null if not found
     */
    function current_public_administration(): ?PublicAdministration
    {
        $user = auth()->user();
        $tenantId = session('tenant_id');

        if ($user) {
            return $user->publicAdministrations()->where('id', $tenantId)->first();
        }

        return null;
    }
}
