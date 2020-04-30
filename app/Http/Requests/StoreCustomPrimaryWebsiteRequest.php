<?php

namespace App\Http\Requests;

use App\Traits\ManagePublicAdministrationRegistration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCustomPrimaryWebsiteRequest extends FormRequest
{
    use ManagePublicAdministrationRegistration;

    public function authorize(): bool
    {
        // TODO: this check should be managed otherwise when the support for tenant
        // switch will be enabled
        return $this->user()->publicAdministrations->isEmpty();
    }

    public function rules(): array
    {
        return [
            'public_administration_name' => 'required|max:255',
            'url' => 'required|url|unique:websites|max:255',
            'city' => 'required|alpha_name|min:2|max:40',
            'county' => 'required|alpha_name|min:2|max:10',
            'region' => 'required|alpha_name|min:2|max:40',
            'pec' => 'nullable|email:rfc,dns|max:75',
            'rtd_name' => 'nullable|alpha_name|min:2|max:50',
            'rtd_mail' => 'nullable|required_with:rtd_name|email:rfc,dns|max:75',
            'rtd_pec' => 'nullable|email:rfc,dns|max:75',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (filled($this->input('url'))) {
                $host = parse_url($this->input('url'), PHP_URL_HOST);
                if ($host && !$this->checkIsNotPrimary($host)) {
                    $validator->errors()->add('url', __("L'indirizzo inserito appartiene a un'altra pubblica amministrazione."));
                }
            }
        });
    }
}
