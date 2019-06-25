<?php

namespace App\Http\Requests;

use App\Enums\UserPermission;
use App\Enums\WebsiteType;
use App\Traits\InteractsWithIPAIndex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Store website request.
 */
class StoreWebsiteRequest extends FormRequest
{
    use InteractsWithIPAIndex;

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
            'name' => 'required',
            'url' => 'required|url|unique:websites',
            'type' => [
                'required',
                Rule::in([WebsiteType::SECONDARY, WebsiteType::WEBAPP, WebsiteType::TESTING]),
            ],
            'usersEnabled' => 'array',
            'usersEnabled.*' => 'in:enabled',
            'usersPermissions' => 'array',
            'usersPermissions.*' => Rule::in([UserPermission::MANAGE_ANALYTICS, UserPermission::READ_ANALYTICS]),
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
            if (is_array($this->input('usersPermissions')) && !$this->checkUsersIds($this->input('usersPermissions'))) {
                $validator->errors()->add('usersPermissions', 'È necessario selezionare tutti i permessi correttamente'); //TODO: put in lang file
            }
        });
        $validator->after(function (Validator $validator) {
            if (filled($this->input('url')) && !$this->checkIsNotPrimary($this->input('url'))) {
                $validator->errors()->add('url', "L'indirizzo inserito appartiene ad un'altra pubblica amministrazione."); //TODO: put error message in lang file
            }
        });
    }

    /**
     * Check whether the 'usersPermissions' array contains keys belonging to
     * users of the current selected public administration.
     *
     * @param array $usersPermissions The users permissions array
     *
     * @return bool true if the provided user permissions contains keys belonging to users in the current public administration, false otherwise
     */
    protected function checkUsersIds(array $usersPermissions): bool
    {
        return empty(array_diff(array_keys($usersPermissions), current_public_administration()->users->pluck('id')->all()));
    }

    /**
     * Check whether the provided url does not belong to the primary site
     * of a Public Administration.
     *
     * @param string $url The url to check
     *
     * @return bool true if the provided url is not listed as a primary website url for a public administration, false otherwise
     */
    protected function checkIsNotPrimary(string $url): bool
    {
        $publicAdministration = $this->getPublicAdministrationEntryByPrimaryWebsiteUrl($url);

        if (empty($publicAdministration)) {
            return true;
        }

        $publicAdministrationSite = Str::slug(preg_replace('/^http(s)?:\/\/(www\.)?(.+)$/i', '$3', $publicAdministration['site']));
        $receivedUrl = Str::slug(preg_replace('/^http(s)?:\/\/(www\.)?(.+)$/i', '$3', $url));

        return $publicAdministrationSite !== $receivedUrl;
    }
}
