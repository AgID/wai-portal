<?php

namespace App\Services;

use Elastica\Client;

/**
 * ElasticSearch log push initializer.
 */
class ElasticSearchClientSetup
{
    /**
     * Initialize the client to push log messages to ElasticSearch.
     *
     * @return \Elastica\Client the client
     */
    public function getElasticSearchClient(): Client
    {
        return new Client([
            'host' => config('elastic-search.host'),
            'port' => config('elastic-search.port'),
        ]);
    }
}
