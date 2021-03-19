<?php

namespace App\Transformers;

use App\Models\Website;
use League\Fractal\TransformerAbstract;

class WebsiteArrayTransformer extends TransformerAbstract
{
    /**
     * Transform website data for API responces
     *
     * @param Website $website The website
     * @return array The responce
     */
    public function transform(Website $website): array
    {
        return [
            'id' => (int) $website->id,
            'name' => (string) $website->name,
            'url' => (string) $website->url,
            'analytics_id' => (int) $website->analytics_id,
            'slug' => (string) $website->slug,
        ];
    }
}
