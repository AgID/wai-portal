<?php

namespace App\Transformers;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\PublicAdministration;
use App\Models\User;
use League\Fractal\TransformerAbstract;
use Silber\Bouncer\BouncerFacade as Bouncer;

class UserArrayTransformer extends TransformerAbstract
{
    /**
     * Transform the user resources for json responses.
     *
     * @param User $user the user
     * @param PublicAdministration|null $publicAdministration the public administration the user belongs to
     *
     * @return array the response
     */
    public function transform(User $user, ?PublicAdministration $publicAdministration = null): array
    {
        $email = is_null($publicAdministration)
            ? $user->email
            : $user->getEmailforPublicAdministration($publicAdministration);
        $status = is_null($publicAdministration)
            ? $user->status
            : $user->getStatusforPublicAdministration($publicAdministration)->description ?? null;

        $isAdmin = $user->isAn(UserRole::ADMIN);

        $websitesPermissions = $user->publicAdministrations->mapWithKeys(function ($publicAdministration) use ($user, $isAdmin) {
            return Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user, $publicAdministration, $isAdmin) {
                return $publicAdministration->websites->mapWithKeys(function ($website) use ($user, $isAdmin) {
                    $permission = [];
                    if ($isAdmin || $user->can(UserPermission::READ_ANALYTICS, $website)) {
                        array_push($permission, UserPermission::READ_ANALYTICS);
                    }
                    if ($isAdmin || $user->can(UserPermission::MANAGE_ANALYTICS, $website)) {
                        array_push($permission, UserPermission::MANAGE_ANALYTICS);
                    }

                    return [$website->slug => $permission];
                });
            });
        })->toArray();

        return [
            'uuid' => $user->uuid,
            'firstName' => $user->name,
            'lastName' => $user->family_name,
            'fiscal_number' => $user->fiscal_number,
            'email' => $email,
            'status' => $status,
            'permissions' => $websitesPermissions,
            'roles' => $user->allRoleNames,
        ];
    }
}
