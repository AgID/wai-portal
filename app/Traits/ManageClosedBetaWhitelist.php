<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

trait ManageClosedBetaWhitelist
{
    /**
     * @param array|null $payload
     *
     * @return Collection
     */
    public function download(?array $payload = null): Collection
    {
        $client = new Client(['base_uri' => 'https://raw.githubusercontent.com']);
        $response = $client->get(
            ($this->payload['repository']['full_name'] ?? config('webhook-client.configs.0.repository.full_name')) .
            '/' . (Str::afterLast($payload['ref'], '/') ?? config('webhook-client.configs.0.repository.branch')) .
            '/' . config('webhook-client.configs.0.repository.file_name'),
            );

        $content = $response->getBody()->getContents();

        return collect(Yaml::parse($content));
    }
}
