<?php

namespace App\Traits;

use App\Enums\UserRole;
use App\Models\PublicAdministration;

/**
 * Role aware urls.
 */
trait HasRoleAwareUrls
{
    /**
     * Get a proper url based on the role of the user performing the current request.
     *
     * @param string $routeName the route used to generate the url
     * @param array $parameters the parameters to use for the url generation
     * @param PublicAdministration $publicAdministration the public administration to pass as additional parameter
     *
     * @return string the role aware url
     */
    public function getRoleAwareUrl(string $routeName, array $parameters, PublicAdministration $publicAdministration): string
    {
        if (request()->user()->isA(UserRole::SUPER_ADMIN)) {
            $routeName = 'admin.publicAdministration.' . $routeName;
            $parameters = array_merge($parameters, [
                'publicAdministration' => $publicAdministration,
            ]);
        }

        return route($routeName, $parameters);
    }

    /**
     * Get an array proper urls based on the role of the user performing the current request.
     *
     * @param array $routeNames the associative route array used to generate the url in the 'variableName' => 'routeName' format
     * @param array $parameters the parameters to use for the url generation
     * @param PublicAdministration $publicAdministration the public administration to pass as additional parameter
     *
     * @return array the array of role aware urls in the 'variableName' => 'awareUrl' format
     */
    public function getRoleAwareUrlArray(array $routeNames, array $parameters, PublicAdministration $publicAdministration): array
    {
        return collect($routeNames)->map(function ($routeName, $variableName) use ($parameters, $publicAdministration) {
            return $this->getRoleAwareUrl($routeName, $parameters, $publicAdministration);
        })->toArray();
    }
}
