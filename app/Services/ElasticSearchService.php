<?php

namespace App\Services;

use Elastica\Client;

/**
 * ElasticSearch service.
 */
class ElasticSearchService
{
    /**
     * Initialize the client to push log messages to ElasticSearch.
     *
     * @return \Elastica\Client the client
     */
    public function getClient(): Client
    {
        return new Client([
            'host' => config('elastic-search.host'),
            'port' => config('elastic-search.port'),
        ]);
    }
}
