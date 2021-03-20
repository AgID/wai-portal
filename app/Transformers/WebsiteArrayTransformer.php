<?php

namespace App\Transformers;

use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Models\Website;
use League\Fractal\TransformerAbstract;

class WebsiteArrayTransformer extends TransformerAbstract
{
    /**
     * Transform website data for API responces.
     *
     * @param Website $website The website
     *
     * @return array The responce
     */
    public function transform(Website $website): array
    {
        return [
            'name' => $website->name,
            'url' => $website->url,
            'slug' => $website->slug,
            'status' => WebsiteStatus::fromValue($website->status)->description,
            'type' => WebsiteType::fromValue($website->type)->description,
        ];
    }
}
