<?php

namespace App\Http\Requests;

use App\Enums\UserPermission;
use App\Enums\WebsiteType;
use App\Models\Website;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Update website request.
 */
class UpdateWebsiteRequest extends StoreWebsiteRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array the validation rules
     */
    public function rules(): array
    {
        $rules = parent::rules();
        if ($this->route('website')->type->is(WebsiteType::PRIMARY)) {
            $rules['type'] = [
                'required',
                Rule::in([$this->route('website')->type->description]),
            ];
            $rules['url'] = [
                'required',
                Rule::in($this->route('website')->url),
            ];
        } else {
            $rules['url'] = [
                'required',
                Rule::unique('websites')->ignore($this->route('website')->id),
                'url',
            ];
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator the validator reference
     */
    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);
        $validator->after(function (Validator $validator) {
            $affectedUsers = $this->checkLastWebsiteForUsers($this->route('website'), $this->input('usersEnabled'));
            if (!empty($affectedUsers)) {
                $affectedUsers->map(function ($affectedUser) use ($validator) {
                    $validator->errors()->add('permissions', __("Non è possibile disabiltare l'utente " . $affectedUser->info . " perché questo è l'unico sito per il quale è abilitato."));
                });
            }
        });
    }

    /**
     * Check whether the provided website is the last enabled website for users
     * not listed in the usersEnabled parameter.
     *
     * @param Website $website The website to check
     * @param array $usersEnabled The users enabled array
     *
     * @return array The array of users for whom the website is the last enabled one
     */
    protected function checkLastWebsiteForUsers(Website $website, ?array $usersEnabled): Collection
    {
        return $website->getEnabledNonAdministratorUsers()->whereNotInStrict('id', $usersEnabled)->filter(function ($user) {
            return 1 === $user->abilities->where('name', UserPermission::READ_ANALYTICS)->groupBy('entity_id')->count();
        });
    }
}
