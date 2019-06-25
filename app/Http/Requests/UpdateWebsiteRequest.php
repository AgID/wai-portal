<?php

namespace App\Http\Requests;

use App\Enums\WebsiteType;
use Illuminate\Validation\Rule;

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
}
