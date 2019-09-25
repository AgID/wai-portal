<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
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
        unset($rules['fiscal_number']);

        return $rules;
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator the validator instance
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (User::where('email', $this->input('email'))->where('id', '<>', $this->route('user')->id)->whereDoesntHave('roles', function ($query) {
                $query->where('name', UserRole::SUPER_ADMIN);
            })->get()->isNotEmpty()) {
                $validator->errors()->add('email', __('validation.unique', ['attribute' => __('validation.attributes.email')]));
            }
        });

        $validator->after(function (Validator $validator) {
            $user = $this->route('user');
            $lastAdministrator = 1 === request()->route('publicAdministration', current_public_administration())->getActiveAdministrators()->count();
            if ($lastAdministrator && $user->status->is(UserStatus::ACTIVE) && $user->isAn(UserRole::ADMIN) && !$this->input('is_admin')) {
                $validator->errors()->add('is_admin', __('Deve restare almeno un utente amministratore per ogni PA.'));
            }
        });

        parent::withValidator($validator);
    }
}
