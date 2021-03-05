<?php

namespace App\Transformers;

use App\Models\PublicAdministration;
use App\Models\User;
use League\Fractal\TransformerAbstract;

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

        return [
            'id' => $user->uuid,
            'firstName' => $user->name,
            'lastName' => $user->family_name,
            'fiscalNumber' => $user->fiscal_number,
            'email' => $email,
            'status' => $status,
        ];
    }
}
