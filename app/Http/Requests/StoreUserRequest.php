<?php

namespace App\Http\Requests;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\User;
use CodiceFiscale\Checker as FiscalNumberChecker;
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
            'email' => 'required|email|max:255',
            'fiscal_number' => [
                'required',
                'unique:users',
                function ($attribute, $value, $fail) {
                    $chk = new FiscalNumberChecker();
                    if (!$chk->isFormallyCorrect($value)) {
                        return $fail(__('Il codice fiscale non è formalmente valido.'));
                    }
                },
            ],
            'is_admin' => 'boolean',
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
                if (User::where('email', $this->input('email'))->whereDoesntHave('roles', function ($query) {
                    $query->where('name', UserRole::SUPER_ADMIN);
                })->get()->isNotEmpty()) {
                    $validator->errors()->add('email', __('validation.unique', ['attribute' => __('validation.attributes.email')]));
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
}
