<?php

namespace App\Traits;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Jobs\ProcessUsersList;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PredisAdapter;
use Exception;

/**
 * Users index search.
 */
trait InteractsWithUserIndex
{
    /**
     * Search a user.
     *
     * @param string $query the query
     * @param string|null $ipaCode the public administration IPA code to use as filter or null to not filter
     *
     * @return array the results list or an empty array if none
     */
    public function searchUser(string $query, string $ipaCode = null): array
    {
        // Remove negation from query which can be slow and cause high CPU consumption
        // See: http://redisearch.io/Query_Syntax/#pure"_"negative"_"queries
        $query = str_replace('-', '', $query) . '*';

        $userIndex = new Index(
            (new PredisAdapter())->connect(config('database.redis.indexes.host'), config('database.redis.indexes.port'), config('database.redis.indexes.database')),
            ProcessUsersList::USER_INDEX_NAME
        );
        try {
            $results = $userIndex->limit(0, 100)
                ->sortBy('familyName')
                ->inFields(2, ['familyName', 'name'])
                ->search($query . (!empty($ipaCode) ? ' @pas:{' . $ipaCode . '}' : ''))
                ->getDocuments();
        } catch (Exception $exception) {
            // RediSearch returned an error, probably malformed query or index not found.
            logger()->error(
                'Unable to search into User index: ' . $exception->getMessage(),
                [
                    'event' => EventType::EXCEPTION,
                    'exception_type' => ExceptionType::USER_INDEX_SEARCH,
                ]
            );
        }

        return $results ?? [];
    }
}
