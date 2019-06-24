<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends StoreUserRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['email'] = [
            'required',
            'email',
            Rule::unique('users')->ignore($this->route('user')->id),
        ];
        unset($rules['fiscalNumber']);

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);
        $validator->after(function (Validator $validator) {
            $user = $this->route('user');
            $lastAdministrator = 1 === current_public_administration()->getActiveAdministrators()->count();
            if ($lastAdministrator && $user->status->is(UserStatus::ACTIVE) && $user->isA(UserRole::ADMIN) && !$this->input('isAdmin')) {
                $validator->errors()->add('isAdmin', 'Deve restare almeno un utente amministratore per ogni PA.'); //TODO: put error message in lang file
            }
        });
    }
}
