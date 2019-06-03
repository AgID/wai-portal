<?php

namespace App\Traits;

use Ehann\RediSearch\Index;
use Ehann\RediSearch\Query\SearchResult;
use Ehann\RediSearch\RediSearchRedisClient;
use Ehann\RedisRaw\PredisAdapter;
use Exception;

trait InteractsWithIPAIndex
{
    /**
     * Search for a Public Administration in the IPA index.
     *
     * @param string $query the query parameter
     *
     * @return array the Public Administrations found
     */
    public function searchPublicAdministration(string $query): array
    {
        // Remove negation from query which can be slow and cause high CPU consumption
        // See: http://redisearch.io/Query_Syntax/#pure"_"negative"_"queries
        $query = str_replace('-', '', $query) . '*';

        try {
            $ipaIndex = new Index((new PredisAdapter())->connect(config('database.redis.ipaindex.host'), config('database.redis.ipaindex.port'), config('database.redis.ipaindex.database')), 'IPAIndex');
            $result = $ipaIndex->limit(0, 100)
                ->inFields(3, ['ipa_code', 'name', 'city'])
                ->search($query)
                ->getDocuments();
        } catch (Exception $e) {
            // RediSearch returned an error, probably malformed query or index not found.
            // TODO: Please notify me!
            if (!app()->environment('testing')) {
                logger()->error($e);
            }
        }

        return $result ?? [];
    }

    /**
     * Get a Public Administration entry specified by ipa code.
     *
     * @param string $ipaCode The ipa code to search for
     *
     * @return array|null The Public Administration (as an assoc array) if found
     */
    protected function getPublicAdministrationEntryByIpaCode($ipaCode): ?array
    {
        try {
            $redisSearchClient = new RediSearchRedisClient((new PredisAdapter())->connect(config('database.redis.ipaindex.host'), config('database.redis.ipaindex.port'), config('database.redis.ipaindex.database')));
            $rawResult = $redisSearchClient->rawCommand('FT.GET', ['IPAIndex', $ipaCode]);
            $result = SearchResult::makeSearchResult($rawResult ? [1, $ipaCode, $rawResult] : [], true);
        } catch (Exception $e) {
            // RediSearch returned an error, probably malformed query or index not found.
            // TODO: Please notify me!
            if (!app()->environment('testing')) {
                logger()->error($e);
            }
        }

        return empty($result) ? null : $result->getDocuments()[0];
    }

    /**
     * Get a Public Administration entry specified by primary website URL.
     *
     * @param string $url The url of the primary website to search for
     *
     * @return array|null The first Public Administration (as an assoc array) if found
     */
    protected function getPublicAdministrationEntryByPrimaryWebsiteUrl($url): ?array
    {
        try {
            $ipaIndex = new Index((new PredisAdapter())->connect(config('database.redis.ipaindex.host'), config('database.redis.ipaindex.port'), config('database.redis.ipaindex.database')), 'IPAIndex');
            $url = parse_url($url, PHP_URL_HOST);
            $result = $ipaIndex->limit(0, 1)
                ->inFields(1, ['site'])
                ->verbatim()
                ->search(str_replace([':', '-', '@'], ['\:', '\-', '\@'], $url), true)
                ->getDocuments();
        } catch (Exception $e) {
            // RediSearch returned an error, probably malformed query or index not found.
            // TODO: Please notify me!
            if (!app()->environment('testing')) {
                logger()->error($e);
            }
        }

        return empty($result) ? null : $result[0];
    }
}
