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
        $rules['emailPublicAdministrationUser'] = 'required|email:rfc,dns|max:75';

        $user = ('api.users.update' === $this->route()->getName())
          ? get_user_from_fiscalnumber()
          : $user = $this->route('user');

        if (!$user->status->is(UserStatus::INVITED)) {
            unset($rules['fiscal_number']);
        } else {
            unset($rules['fiscal_number'][array_search('unique:users', $rules['fiscal_number'])]);
            $rules['fiscal_number'][] = Rule::unique('users')->ignore($user->id);
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
        $publicAdministration = $this->route('publicAdministration', current_public_administration());

        $userFromFiscalNumber = 'application/json' === $this->header('Content-Type') ? get_user_from_fiscalnumber() : [];
        $user = $this->route('user', $userFromFiscalNumber);

        $validator->after(function (Validator $validator) use ($user) {
            if (User::where('email', $this->input('email'))->where('id', '<>', $user->id)->whereDoesntHave('roles', function ($query) {
                $query->where('name', UserRole::SUPER_ADMIN);
            })->get()->isNotEmpty()) {
                $validator->errors()->add('email', __('validation.unique', ['attribute' => __('validation.attributes.email')]));
            }
        });

        $validator->after(function (Validator $validator) use ($user, $publicAdministration) {
            $publicAdministration = request()->route('publicAdministration', current_public_administration());
            if (null === $publicAdministration) {
                $publicAdministration = get_public_administration_from_token();
            }
            $publicAdministrationFromRoute = request()->route('publicAdministration', $publicAdministration);
            if ($user->isTheLastActiveAdministratorOf($publicAdministrationFromRoute) && !$this->input('is_admin')) {
                $validator->errors()->add('is_admin', __('Deve restare almeno un utente amministratore per ogni PA.'));
            }
        });

        parent::withValidator($validator);
    }
}
