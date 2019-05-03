<?php

namespace App\Services;

use Elastica\Client;

class ElasticSearchClientSetup
{
    public function getElasticSearchClient(): Client
    {
        return new Client([
            'host' => config('elastic-search.host'),
            'port' => config('elastic-search.port'),
        ]);
    }
}
