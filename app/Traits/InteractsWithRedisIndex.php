<?php

namespace App\Traits;

use App\Adapter\RedisSentinelPredisAdapter;
use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Models\User;
use App\Models\Website;
use Ehann\RediSearch\Exceptions\FieldNotInSchemaException;
use Ehann\RediSearch\Index;
use Ehann\RediSearch\Query\SearchResult;
use Ehann\RediSearch\RediSearchRedisClient;
use Ehann\RedisRaw\PredisAdapter;
use Ehann\RedisRaw\RedisRawClientInterface;
use Exception;
use Illuminate\Support\Arr;

/**
 * Redis index search.
 */
trait InteractsWithRedisIndex
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
        return $this->searchRedisIndex('ipa', $query, [
            'inFields' => ['ipa_code', 'name', 'city'],
        ]);
    }

    /**
     * Get a Public Administration entry specified by IPA code.
     *
     * @param string $ipaCode The IPA code to search for
     *
     * @return array|null The Public Administration (as an associative array) or null if not found
     */
    public function getPublicAdministrationEntryByIpaCode($ipaCode): ?array
    {
        try {
            $rawResult = $this->getRediSearchRedisClient('ipa')->rawCommand('FT.GET', [
                config('database.redis.indexes.ipa.name'),
                $ipaCode,
            ]);
            $result = SearchResult::makeSearchResult($rawResult ? [1, $ipaCode, $rawResult] : [], true);
        } catch (Exception $exception) {
            // RediSearch returned an error, probably malformed query or index not found.
            logger()->error(
                'Unable to search into IPA index: ' . $exception->getMessage(),
                [
                    'event' => EventType::EXCEPTION,
                    'exception_type' => ExceptionType::IPA_INDEX_SEARCH,
                ]
            );
        }

        return empty($result) ? null : $result->getDocuments()[0];
    }

    /**
     * Get a Public Administration entry specified by primary website URL.
     *
     * @param string $url The url of the primary website to search for
     *
     * @return array The first Public Administration (as an associative array) or an empty array if not found
     */
    public function getPublicAdministrationEntryByPrimaryWebsiteUrl(string $url): array
    {
        return $this->searchRedisIndex('ipa', $url, [
            'inFields' => ['site'],
            'exact' => true,
            'limit' => 1,
            'verbatim' => true,
            'resultsAsArray' => true,
        ]);
    }

    /**
     * Search a user.
     *
     * @param string $query the query
     * @param string|null $ipaCode the public administration IPA code to use as filter or null to not filter
     *
     * @return array the results list or an empty array if none
     */
    public function searchUsersIndex(string $query, ?string $ipaCode = null): array
    {
        $tagFilters = empty($ipaCode) ? null : ['pas' => $ipaCode];

        return $this->searchRedisIndex('users', $query, [
            'inFields' => ['family_name', 'name'],
            'sortBy' => 'family_name',
            'tagFilters' => $tagFilters,
        ]);
    }

    /**
     * Search a website.
     *
     * @param string $query the query
     * @param string|null $ipaCode the public administration IPA code or null to not filter
     *
     * @return array the results list or an empty array if none
     */
    public function searchWebsitesIndex(string $query, ?string $ipaCode = null): array
    {
        $tagFilters = empty($ipaCode) ? null : ['pa' => $ipaCode];
        $results = $this->searchRedisIndex('websites', $query, [
            'inFields' => ['name', 'slug'],
            'sortBy' => 'slug',
            'tagFilters' => $tagFilters,
        ]);

        collect($results)->map(function ($result) {
            $publicAdministration = $this->getPublicAdministrationEntryByIpaCode($result->pa);
            $result->pa_name = $publicAdministration['name'];
        });

        return $results;
    }

    /**
     * Search the index.
     *
     * @param string $indexName the index to search
     * @param string $query the query
     * @param array $options the options to use in the query as an array
     *                       [
     *                       'inFields' => null, // the fields to use in the search
     *                       'exact' => false, // wether exact matching is required
     *                       'limit' => 100, // the maximum number of results to retrun
     *                       'sortBy' => null, // the field to be used for the results sorting
     *                       'tagFilters' => null, // the tags to use in query filtering
     *                       'verbatim' => false, // wether stemming should be disabled in the search
     *                       'resultsAsArray' => false, // wether the results should be returned as array
     *                       ]
     *
     * @return array the results list or an empty array if none
     */
    protected function searchRedisIndex(string $indexName, string $query, array $options = []): array
    {
        $options = array_merge([
            'inFields' => null,
            'exact' => false,
            'limit' => 100,
            'sortBy' => null,
            'tagFilters' => null,
            'verbatim' => false,
            'resultsAsArray' => false,
        ], $options);

        $query = $this->escapeQuery($query);

        // Add a wildcard to the end of each search term
        if (!$options['exact']) {
            $query = implode(' ', preg_replace('/$/', '*', explode(' ', $query)));
        }

        collect($options['tagFilters'])->map(function ($tagFilterValues, $tagFilterName) use (&$query) {
            $query = $query . ' @' . $tagFilterName . ':{' . implode(' | ', Arr::wrap($tagFilterValues)) . '}';
        });

        $index = $this->getRedisIndex($indexName);

        try {
            $queryBuilder = $index->limit(0, $options['limit']);
            if (is_array($options['inFields'])) {
                $queryBuilder->inFields(sizeof($options['inFields']), $options['inFields']);
            }
            if ($options['verbatim']) {
                $queryBuilder->verbatim();
            }
            if ($options['sortBy']) {
                $queryBuilder->sortBy($options['sortBy']);
            }
            $results = $queryBuilder->search($query, $options['resultsAsArray'])->getDocuments();
        } catch (Exception $exception) {
            // RediSearch returned an error, probably malformed query or index not found.
            logger()->error(
                'Unable to search into Redis index ' . $indexName . ': ' . $exception->getMessage(),
                [
                    'event' => EventType::EXCEPTION,
                    'exception_type' => ExceptionType::REDIS_INDEX_SEARCH,
                ]
            );
        }

        if ($options['resultsAsArray'] && 1 === $options['limit']) {
            $results = $results[0] ?? [];
        }

        return $results ?? [];
    }

    protected function escapeQuery(string $query)
    {
        return str_replace([':', '-', '@'], ['\:', '\-', '\@'], $query);
    }

    /**
     * Get a RedisSearch index.
     *
     * @param string $index the index to get, as configured in config/database.php
     *
     * @return Index the RedisSearch index
     */
    protected function getRedisIndex(string $index): Index
    {
        return new Index($this->getConnectedRedisClient($index), config('database.redis.indexes.' . $index . '.name'));
    }

    /**
     * Get a RediSearchRedisClient instance for a specified index.
     *
     * @param string $index the index to get as configured in config/database.php
     *
     * @return RediSearchRedisClient the RediSearchRedisClient instance
     */
    protected function getRediSearchRedisClient(string $index): RediSearchRedisClient
    {
        return new RediSearchRedisClient($this->getConnectedRedisClient($index));
    }

    /**
     * Update websites index.
     *
     * @param Website $website the website to update
     */
    protected function updateWebsitesIndex(Website $website): void
    {
        $websitesIndex = $this->getRedisIndex('websites');

        try {
            $websitesIndex->addTagField('pa')
                ->addTextField('slug', 2.0, true)
                ->addTextField('name', 2.0, true)
                ->create();
        } catch (Exception $e) {
            // Index already exists, it's ok!
        }

        try {
            $websiteDocument = $websitesIndex->makeDocument($website->id);
            $websiteDocument->slug->setValue($website->slug);
            $websiteDocument->name->setValue($website->name);
            $websiteDocument->pa->setValue($website->publicAdministration->ipa_code);
            $websitesIndex->replace($websiteDocument);
        } catch (FieldNotInSchemaException $exception) {
            report($exception);
        }
    }

    /**
     * Update users index.
     *
     * @param User $user the user to update
     */
    protected function updateUsersIndex(User $user): void
    {
        $usersIndex = $this->getRedisIndex('users');

        try {
            $usersIndex->addTagField('pas')
                ->addTextField('uuid')
                ->addTextField('family_name', 2.0, true)
                ->addTextField('name', 2.0, true)
                ->create();
        } catch (Exception $e) {
            // Index already exists, it's ok!
        }

        try {
            $userDocument = $usersIndex->makeDocument($user->uuid);
            $userDocument->uuid->setValue($user->uuid);
            $userDocument->name->setValue($user->name);
            $userDocument->family_name->setValue($user->family_name);
            $userDocument->pas->setValue(implode(',', $user->publicAdministrations()->get()->pluck('ipa_code')->toArray()));
            $usersIndex->replace($userDocument);
        } catch (FieldNotInSchemaException $exception) {
            report($exception);
        }
    }

    /**
     * Retrieve the initialized RediSearch client.
     *
     * @param string $index the index name
     *
     * @return RedisRawClientInterface the client
     */
    private function getConnectedRedisClient(string $index): RedisRawClientInterface
    {
        if (('ipa' === $index && env('REDIS_IPA_INDEX_USE_SENTINELS', false)) || ('ipa' !== $index && env('REDIS_INDEXES_USE_SENTINELS', false))) {
            return (new RedisSentinelPredisAdapter($index))->connect();
        }

        return (new PredisAdapter())->connect(
            config('database.redis.indexes.' . $index . '.host'),
            config('database.redis.indexes.' . $index . '.port'),
            config('database.redis.indexes.' . $index . '.database'),
            config('database.redis.indexes.' . $index . '.password')
        );
    }
}
