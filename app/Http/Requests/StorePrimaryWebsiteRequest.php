<?php

namespace App\Http\Requests;

use App\Jobs\UpdateClosedBetaWhitelist;
use App\Traits\InteractsWithRedisIndex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;
use Symfony\Component\Yaml\Yaml;

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

                if (config('wai.closed_beta')) {
                    $whitelist = Cache::rememberForever(UpdateClosedBetaWhitelist::CLOSED_BETA_WHITELIST_KEY, function () {
                        return collect(Yaml::parse(Storage::get(UpdateClosedBetaWhitelist::CLOSED_BETA_WHITELIST_FILENAME)));
                    });

                    if (!$whitelist->contains($this->publicAdministration['ipa_code'])) {
                        $validator->errors()->add('public_administration_name', __('PA non iscritta alla closed-beta'));
                        $this->redirector->to($this->redirect)->withModal([
                            'title' => __('Progetto in closed-beta!'),
                            'icon' => 'it-close-circle',
                            'message' => implode("\n", [
                                    __('Il portale ' . config('app.name') . ' si trova attualmente in fase closed-beta.'),
                                    __('Durante questa fase sperimentale, l\'accesso è limitato ad un numero chiuso di pubbliche amministrazioni pilota.'),
                            ]),
                            'image' => asset('images/website-archive.svg'),
                        ]);
                    }
                }

                // NOTE: uncomment and add "required" rule to rtd_name and rtd_mail to enforce rtd validation
                // elseif (!($this->input('skip_rtd_validation') && app()->environment('testing'))) {
                //     if ($this->input('rtd_name') !== ($publicAdministration['rtd_name'] ?? '')) {
                //         $validator->errors()->add('rtd_name', __('Il nominativo RTD immesso non corrisponde a quello presente su IndicePA.'));
                //     }
                //     if ($this->input('rtd_mail') !== ($publicAdministration['rtd_mail'] ?? '')) {
                //         $validator->errors()->add('rtd_mail', __("L'indirizzo email RTD immesso non corrisponde a quello presente su IndicePA."));
                //     }
                // }
            }
        });
    }
}
