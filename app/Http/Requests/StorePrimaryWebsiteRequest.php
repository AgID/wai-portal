<?php

namespace App\Http\Requests;

use App\Enums\WebsiteType;
use App\Jobs\UpdateClosedBetaWhitelist;
use App\Traits\InteractsWithRedisIndex;
use App\Traits\ManageClosedBetaWhitelist;
use App\Traits\ManagePublicAdministrationRegistration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Validator;

/**
 * Store primary website request.
 */
class StorePrimaryWebsiteRequest extends FormRequest
{
    use InteractsWithRedisIndex;
    use ManageClosedBetaWhitelist;
    use ManagePublicAdministrationRegistration;

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
            'email' => 'required|email|max:75',
            'public_administration_name' => 'required|max:255',
            'url' => 'required|unique:websites|max:255',
            'rtd_mail' => 'nullable|email|max:75',
            'ipa_code' => 'nullable|required_without:website_type|unique:public_administrations|max:25',
            'correct_confirmation' => 'accepted',
            'city' => 'nullable|required_with:website_type|alpha_name|min:2|max:40',
            'county' => 'nullable|required_with:website_type|alpha_name|min:2|max:10',
            'region' => 'nullable|required_with:website_type|alpha_name|min:2|max:40',
            'pec' => 'nullable|email:rfc,dns|max:75',
            'rtd_name' => 'nullable|alpha_name|min:2|max:50',
            'rtd_pec' => 'nullable|email:rfc,dns|max:75',
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
            if (!app()->environment('production') && config('wai.custom_public_administrations', false) && filled($this->input('url')) && ('custom' === $this->input('website_type'))) {
                if (!filter_var($this->input('url'), FILTER_VALIDATE_URL)) {
                    $validator->errors()->add('url', __("L'indirizzo inserito non è corretto."));
                }
                $host = parse_url($this->input('url'), PHP_URL_HOST);
                if ($host && !$this->checkIsNotPrimary($host)) {
                    $validator->errors()->add('url', __("L'indirizzo inserito appartiene a un'altra pubblica amministrazione."));
                }

                $publicAdministration = $this->input();
                $publicAdministration['name'] = $publicAdministration['public_administration_name'];
                $websiteStatus = WebsiteType::coerce(WebsiteType::INSTITUTIONAL_PLAY);
                $publicAdministration['type'] = ucfirst($websiteStatus->description);
                $this->publicAdministration = $publicAdministration;
                $this->isCustomPublicAdministration = true;
            }

            if (filled($this->input('ipa_code'))) {
                $publicAdministration = $this->getPublicAdministrationEntryByIpaCode($this->input('ipa_code'));
                $this->publicAdministration = $publicAdministration;
                $this->isCustomPublicAdministration = false;

                if (empty($publicAdministration)) {
                    $validator->errors()->add('public_administration_name', __('Il codice IPA della PA selezionata non è corretto.'));

                    return;
                }

                if (config('wai.closed_beta')) {
                    $whitelist = Cache::rememberForever(UpdateClosedBetaWhitelist::CLOSED_BETA_WHITELIST_KEY, function () {
                        return $this->download();
                    });

                    if (!$whitelist->contains(strtolower($this->publicAdministration['ipa_code']))) {
                        $validator->errors()->add('public_administration_name', __('PA non inclusa in fase di beta chiusa'));
                        $this->redirector->to($this->redirect)->withModal([
                            'title' => __('Accesso limitato'),
                            'icon' => 'it-close-circle',
                            'message' => implode("\n", [
                                __(':app è in una fase di beta chiusa (:closed-beta-faq).', [
                                    'app' => '<strong>' . config('app.name') . '</strong>',
                                    'closed-beta-faq' => '<a href="' . route('faq') . '#beta-chiusa">' . __('cosa significa?') . '</a>',
                                ]),
                                __("Durante questa fase sperimentale, l'accesso è limitato ad un numero chiuso di pubbliche amministrazioni pilota."),
                            ]),
                            'image' => asset('images/closed.svg'),
                        ]);
                    }
                }

                if (!($this->input('skip_rtd_validation') && app()->environment('testing'))) {
                    if (($publicAdministration['rtd_name'] ?? null) !== $this->input('rtd_name')) {
                        $validator->errors()->add('rtd_name', __('Il nominativo RTD immesso non corrisponde a quello presente su IndicePA.'));
                    }
                    if (($publicAdministration['rtd_mail'] ?? null) !== $this->input('rtd_mail')) {
                        $validator->errors()->add('rtd_mail', __("L'indirizzo email RTD immesso non corrisponde a quello presente su IndicePA."));
                    }
                }
            }
        });
    }
}
