<?php

namespace App\Http\Requests;

use App\Enums\UserPermission;
use App\Enums\WebsiteType;
use App\Traits\InteractsWithRedisIndex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Store website request.
 */
class StoreWebsiteRequest extends FormRequest
{
    use InteractsWithRedisIndex;

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
            'website_name' => 'required|max:255',
            'url' => 'required|url|unique:websites|max:255',
            'type' => [
                'required',
                Rule::in([WebsiteType::SECONDARY, WebsiteType::WEBAPP, WebsiteType::TESTING]),
            ],
            'permissions' => 'array',
            'permissions.*' => 'array',
            'permissions.*.*' => Rule::in([UserPermission::MANAGE_ANALYTICS, UserPermission::READ_ANALYTICS]),
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
            if (is_array($this->input('permissions')) && !$this->checkUsersIds($this->input('permissions'))) {
                $validator->errors()->add('permissions', __('È necessario selezionare tutti i permessi correttamente'));
            }
        });
        $validator->after(function (Validator $validator) {
            if (filled($this->input('url'))) {
                $host = parse_url($this->input('url'), PHP_URL_HOST);
                if ($host && !$this->checkIsNotPrimary($host)) {
                    $validator->errors()->add('url', __("L'indirizzo inserito appartiene ad un'altra pubblica amministrazione."));
                }
            }
        });
    }

    /**
     * Check whether the 'permissions' array contains keys belonging to
     * users of the current selected public administration.
     *
     * @param array $usersPermissions The users permissions array
     *
     * @return bool true if the provided user permissions contains keys belonging to users in the current public administration, false otherwise
     */
    protected function checkUsersIds(array $usersPermissions): bool
    {
        return empty(array_diff(array_keys($usersPermissions), request()->route('publicAdministration', current_public_administration())->users->pluck('id')->all()));
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

        $publicAdministrationPrimaryWebsiteHost = Str::slug(preg_replace('/^http(s)?:\/\/(www\.)?(.+)$/i', '$3', $publicAdministration['site']));
        $inputHost = Str::slug(preg_replace('/^http(s)?:\/\/(www\.)?(.+)$/i', '$3', $url));

        return $publicAdministrationPrimaryWebsiteHost !== $inputHost;
    }
}
