<?php

namespace App\Http\Requests;

use App\Enums\UserPermission;
use CodiceFiscale\Checker as FiscalNumberChecker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users',
            'fiscalNumber' => [
                'required',
                'unique:users',
                function ($attribute, $value, $fail) {
                    $chk = new FiscalNumberChecker();
                    if (!$chk->isFormallyCorrect($value)) {
                        return $fail('Il codice fiscale non è formalmente valido.');
                    }
                },
            ],
            'isAdmin' => 'boolean',
            'websitesEnabled' => 'array',
            'websitesEnabled.*' => 'in:enabled',
            'websitesPermissions' => 'required_without:isAdmin|array',
            'websitesPermissions.*' => Rule::in([UserPermission::MANAGE_ANALYTICS, UserPermission::READ_ANALYTICS]),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     *
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (is_array($this->input('websitesPermissions')) && !$this->checkWebsitesIds($this->input('websitesPermissions'))) {
                $validator->errors()->add('websitesPermissions', 'È necessario selezionare tutti i permessi correttamente'); //TODO: put in lang file
            }
        });
    }

    /**
     * Check whether the websitesPermission array contains keys belonging to
     * websites of the current selected public administation.
     *
     * @param array $websitesPermissions The websitesPermissions array
     *
     * @return bool true if the provided user permissions contains keys belonging to websites in the current public administration
     */
    protected function checkWebsitesIds(array $websitesPermissions): bool
    {
        return empty(array_diff(array_keys($websitesPermissions), current_public_administration()->websites->pluck('id')->all()));
    }
}
