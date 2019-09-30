<?php

namespace App\Services;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

/**
 * ElasticSearch service.
 */
class ElasticSearchService
{
    /**
     * Initialize the client to push log messages to ElasticSearch.
     *
     * @return \Elasticsearch\Client the client
     */
    public function getClient(): Client
    {
        return ClientBuilder::create()
            ->setHosts([
                config('elastic-search.host') . ':' . config('elastic-search.port'),
            ])
            ->build();
    }
}
