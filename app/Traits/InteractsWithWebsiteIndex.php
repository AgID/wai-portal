<?php

namespace App\Traits;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Jobs\ProcessWebsitesList;
use App\Models\Website;
use Ehann\RediSearch\Exceptions\FieldNotInSchemaException;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PredisAdapter;
use Exception;

/**
 * Websites index search.
 */
trait InteractsWithWebsiteIndex
{
    use InteractsWithIPAIndex;

    /**
     * Search a website.
     *
     * @param string $query the query
     * @param string|null $ipaCode the public administration IPA code or null to not filter
     *
     * @return array the results list or an empty array if none
     */
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
            logger()->error(
                'Unable to search into Website index: ' . $exception->getMessage(),
                [
                    'event' => EventType::EXCEPTION,
                    'type' => ExceptionType::WEBSITE_INDEX_SEARCH,
                ]
            );
        }

        return $results ?? [];
    }

    /**
     * Update websites index.
     *
     * @param Website $website the website to update
     */
    private function updateWebsiteIndex(Website $website): void
    {
        $websiteIndex = new Index(
            (new PredisAdapter())->connect(config('database.redis.indexes.host'), config('database.redis.indexes.port'), config('database.redis.indexes.database')),
            ProcessWebsitesList::WEBSITE_INDEX_NAME
        );

        try {
            $websiteIndex->addTagField('pa')
                ->addTextField('slug', 2.0, true)
                ->addTextField('name', 2.0, true)
                ->create();
        } catch (Exception $e) {
            // Index already exists, it's ok!
        }

        try {
            $websiteDocument = $websiteIndex->makeDocument($website->id);
            $websiteDocument->slug->setValue($website->slug);
            $websiteDocument->name->setValue($website->name);
            $websiteDocument->pa->setValue($website->publicAdministration->ipa_code);
            $websiteIndex->replace($websiteDocument);
        } catch (FieldNotInSchemaException $exception) {
            report($exception);
        }
    }
}
