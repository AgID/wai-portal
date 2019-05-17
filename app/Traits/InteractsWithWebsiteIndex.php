<?php

namespace App\Traits;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Jobs\ProcessWebsitesList;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PredisAdapter;
use Exception;

trait InteractsWithWebsiteIndex
{
    use InteractsWithIPAIndex;

    public function searchWebsite(string $query, string $ipaCode = null): array
    {
        // Remove negation from query which can be slow and cause high CPU consumption
        // See: http://redisearch.io/Query_Syntax/#pure"_"negative"_"queries
        $query = str_replace('-', '', $query) . '*';

        $websiteIndex = new Index(
            (new PredisAdapter())->connect(config('database.redis.indexes.host'), config('database.redis.indexes.port'), config('database.redis.indexes.database')),
            ProcessWebsitesList::WEBSITE_INDEX_NAME
        );
        try {
            $results = $websiteIndex->limit(0, 100)
                ->sortBy('slug')
                ->inFields(2, ['slug', 'name'])
                ->search($query . (!empty($ipaCode) ? ' @pa:{' . $ipaCode . '}' : ''))
                ->getDocuments();
            if (empty($ipaCode)) {
                foreach ($results as $result) {
                    $publicAdministration = $this->getPublicAdministrationEntryByIPACode($result->pa);
                    $result->pa_name = $publicAdministration['name'];
                }
            }
        } catch (Exception $exception) {
            // RediSearch returned an error, probably malformed query or index not found.
            // TODO: Please notify me!
            if (!app()->environment('testing')) {
                logger()->error(
                    'Unable to search into Website index: ' . $exception->getMessage(),
                    [
                        'event' => EventType::EXCEPTION,
                        'type' => ExceptionType::WEBSITE_INDEX_SEARCH,
                    ]
                );
            }
        }

        return $results ?? [];
    }
}
