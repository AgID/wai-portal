<?php

namespace App\Http\Requests;

use App\Traits\InteractsWithRedisIndex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Store primary website request.
 */
class StorePrimaryWebsiteRequest extends FormRequest
{
    use InteractsWithRedisIndex;

    /**
     * The validated public administration array in this request.
     *
     * @var array the public administration in this request
     */
    public $publicAdministration;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // TODO: this check should be managed otherwise when the support for tenant
        // switch will be enabled
        return $this->user()->publicAdministrations->isEmpty();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array the validation rules
     */
    public function rules(): array
    {
        return [
            'public_administration_name' => 'required',
            'url' => 'required|unique:websites',
            'rtd_mail' => 'nullable|email',
            'ipa_code' => 'required|unique:public_administrations',
            'correct_confirmation' => 'accepted',
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
            if (filled($this->input('ipa_code'))) {
                $publicAdministration = $this->getPublicAdministrationEntryByIpaCode($this->input('ipa_code'));
                $this->publicAdministration = $publicAdministration;

                if (empty($publicAdministration)) {
                    $validator->errors()->add('public_administration_name', __('Il codice IPA della PA selezionata non è corretto.'));
                }

                // NOTE: uncomment and add "required" rule to rtd_name and rtd_mail to enforce rtd validation
                // elseif (!($this->input('skip_rtd_validation') && app()->environment('testing'))) {
                //     if ($this->input('rtd_name') !== ($publicAdministration['rtd_name'] ?? '')) {
                //         $validator->errors()->add('rtd_name', __('Il nominativo RTD immesso non corrisponde a quello presente su indice IPA.'));
                //     }
                //     if ($this->input('rtd_mail') !== ($publicAdministration['rtd_mail'] ?? '')) {
                //         $validator->errors()->add('rtd_mail', __("L'indirizzo email RTD immesso non corrisponde a quello presente su indice IPA."));
                //     }
                // }
            }
        });
    }
}
