<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateWebsiteRequest extends StoreWebsiteRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['url'] = [
            'required',
            Rule::unique('websites')->ignore($this->route('website')->id),
            'url',
        ];
        return $rules;
    }
}
