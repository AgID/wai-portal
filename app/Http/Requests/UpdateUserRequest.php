<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Update user request.
 */
class UpdateUserRequest extends StoreUserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array the validation rules
     */
    public function rules(): array
    {
        $rules = parent::rules();

        if (!$this->route('user')->status->is(UserStatus::INVITED)) {
            unset($rules['fiscal_number']);
        } else {
            unset($rules['fiscal_number'][array_search('unique:users', $rules['fiscal_number'])]);
            $rules['fiscal_number'][] = Rule::unique('users')->ignore($this->route('user')->id);
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator the validator instance
     */
    public function withValidator(Validator $validator): void
    {
        $user = $this->route('user');

        $validator->after(function (Validator $validator) use ($user) {
            if (User::where('email', $this->input('email'))->where('id', '<>', $user->id)->whereDoesntHave('roles', function ($query) {
                $query->where('name', UserRole::SUPER_ADMIN);
            })->get()->isNotEmpty()) {
                $validator->errors()->add('email', __('validation.unique', ['attribute' => __('validation.attributes.email')]));
            }
        });

        $validator->after(function (Validator $validator) use ($user) {
            $publicAdministration = request()->route('publicAdministration', current_public_administration());
            if ($user->isTheLastActiveAdministratorOf($publicAdministration) && !$this->input('is_admin')) {
                $validator->errors()->add('is_admin', __('Deve restare almeno un utente amministratore per ogni PA.'));
            }
        });

        parent::withValidator($validator);
    }
}
