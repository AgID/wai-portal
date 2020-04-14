<?php

namespace App\Http\Requests;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\User;
use CodiceFiscale\Validator as FiscalNumberChecker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Store user request.
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array the validation rules
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc,dns|max:75',
            'fiscal_number' => [
                'required',
                // 'unique:users',
                function ($attribute, $value, $fail) {
                    $chk = new FiscalNumberChecker($value);
                    if (!$chk->isFormallyValid()) {
                        return $fail(__('Il codice fiscale non è formalmente valido.'));
                    }
                },
            ],
            'is_admin' => 'boolean',
            'paUser' => 'boolean',
            'permissions' => 'required|array',
            'permissions.*' => 'array',
            'permissions.*.*' => Rule::in([UserPermission::MANAGE_ANALYTICS, UserPermission::READ_ANALYTICS]),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator the validator instance
     */
    public function withValidator(Validator $validator): void
    {
        if (!$this->route()->hasParameter('user')) {
            $validator->after(function (Validator $validator) {
                $user = $this->getPublicAdministrationUser($this->input('email'), $this->input('fiscal_number'));
                if (!is_null($user)) {
                    $data = $validator->getData();
                    $data['paUser'] = $user;
                    $validator->setData($data);
                } else {
                    if (User::where('email', $this->input('email'))->whereDoesntHave('roles', function ($query) {
                        $query->where('name', UserRole::SUPER_ADMIN);
                    })->get()->isNotEmpty()) {
                        $validator->errors()->add('email', __('validation.unique', ['attribute' => __('validation.attributes.email')]));
                    } elseif (User::where('fiscal_number', $this->input('fiscal_number'))->whereDoesntHave('roles', function ($query) {
                        $query->where('name', UserRole::SUPER_ADMIN);
                    })->get()->isNotEmpty()) {
                        $validator->errors()->add('fiscal_number', __('validation.unique', ['attribute' => __('validation.attributes.fiscal_number')]));
                    }
                }
            });
        }

        $validator->after(function (Validator $validator) {
            if (is_array($this->input('permissions')) && !$this->checkWebsitesIds($this->input('permissions'))) {
                $validator->errors()->add('permissions', __('È necessario selezionare tutti i permessi correttamente'));
            }
        });
    }

    /**
     * Check whether the websitesPermission array contains keys belonging to
     * websites of the current selected public administration.
     *
     * @param array $websitesPermissions The websitesPermissions array
     *
     * @return bool true if the provided user permissions contains keys belonging to websites in the current public administration
     */
    protected function checkWebsitesIds(array $websitesPermissions): bool
    {
        return empty(array_diff(array_keys($websitesPermissions), request()->route('publicAdministration', current_public_administration())->websites->pluck('id')->all()));
    }

    /**
     *  Check if user already exists in the database.
     *
     *  @param string $email of user
     *  @param string $fiscal_number of user
     *
     *  @return User The user registered
     */
    protected function getPublicAdministrationUser(string $email, string $fiscal_number): ?User
    {
        return User::with('publicAdministrations')->where('email', $email)->where('fiscal_number', $fiscal_number)->whereDoesntHave('roles', function ($query) {
            $query->where('name', UserRole::SUPER_ADMIN);
        })->first();
    }
}
