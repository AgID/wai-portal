<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserApiTransformer extends TransformerAbstract
{
    public function transform(User $user): array
    {
        return [
            'id'             => (int) $user->id,
            'firstName'      => (string) $user->name,
            'lastName'       => (string) $user->family_name,
            'codice_fiscale' => (string) $user->fiscal_number,
            'email'          => (string) $user->email,
        ];
    }
}