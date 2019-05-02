<?php

namespace App\Http\Requests;

use App\Traits\InteractsWithIPAIndex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePrimaryWebsiteRequest extends FormRequest
{
    use InteractsWithIPAIndex;

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
            'public_administration_name' => 'required',
            'url' => 'required|unique:websites',
            'pec' => 'email|nullable',
            'ipa_code' => 'required|unique:public_administrations',
            'accept_terms' => 'required',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (filled($this->input('ipa_code'))) {
                $publicAdministration = $this->getPublicAdministrationEntryByIPACode($this->input('ipa_code'));
                $this->publicAdministration = $publicAdministration;
                if (empty($publicAdministration)) {
                    $validator->errors()->add('public_administration_name', 'La PA selezionata non esiste'); //TODO: put error message in lang file
                }
            }
        });
    }
}
