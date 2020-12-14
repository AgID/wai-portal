<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Store key request.
 */
class StoreKeysRequest extends FormRequest
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
            'key_name' => 'required|unique:keys,client_name|min:3|max:255',
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
            $data = $validator->getData();
            $validator->setData($data);
        });
    }

    /*
     * Check whether the 'permissions' array contains keys belonging to
     * websites of the current selected public administration.
     *
     * @param array $usersPermissions The users permissions array
     *
     * @return bool true if the provided user permissions contains keys belonging to users in the current public administration, false otherwise
     */
    /* protected function checkUsersIds(array $usersPermissions): bool
    {
        return empty(array_diff(array_keys($usersPermissions), request()->route('publicAdministration', current_public_administration())->users->pluck('id')->all()));
    } */
}
