<?php

namespace App\Http\Requests;

use Illuminate\Validation\Validator;

/**
 * Update credential request.
 */
class UpdateCredentialRequest extends StoreCredentialsRequest
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
            'credential_name' => 'required|min:3|max:255',
            'type' => 'required',
            'permissions' => 'array',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator the validator reference
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (!is_array($this->input('permissions')) && 'admin' !== $this->input('type')) {
                $validator->errors()->add('permissions', __('È necessario selezionare tutti i permessi correttamente'));
            }
        });
        $validator->after(function (Validator $validator) {
            $data = $validator->getData();
            $validator->setData($data);
        });
    }
}
