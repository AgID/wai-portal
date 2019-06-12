<?php

namespace App\Jobs;

use App\Events\Jobs\WebsiteIndexUpdateCompleted;
use App\Models\Website;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PredisAdapter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Websites index update job.
 */
class ProcessWebsitesList implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Redisearch index name.
     */
    public const WEBSITE_INDEX_NAME = 'WebsiteIndex';

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $websiteIndex = new Index(
            (new PredisAdapter())->connect(config('database.redis.indexes.host'), config('database.redis.indexes.port'), config('database.redis.indexes.database')),
            self::WEBSITE_INDEX_NAME
        );

        try {
            // Drop the current index as we want a fresh update
            $websiteIndex->drop();
        } catch (Exception $e) {
            // Index already dropped, it's ok!
        }

        try {
            $websiteIndex->addTagField('pa')
                ->addTextField('slug', 2.0, true)
                ->addTextField('name', 2.0, true)
                ->create();
        } catch (Exception $e) {
            // Index already exists, it's ok!
        }

        $results = Website::withTrashed()->get()->mapToGroups(function ($website) use ($websiteIndex) {
            $websiteDocument = $websiteIndex->makeDocument($website->id);
            $websiteDocument->slug->setValue($website->slug);
            $websiteDocument->name->setValue($website->name);
            $websiteDocument->pa->setValue($website->publicAdministration->ipa_code);
            if ($websiteIndex->replace($websiteDocument)) {
                return [
                    'inserted' => [
                        'website' => $website->slug,
                    ],
                ];
            }

            return [
                'failed' => [
                    'website' => $website->slug,
                ],
            ];
        });

        event(new WebsiteIndexUpdateCompleted(
            empty($results->get('inserted')) ? [] : $results->get('inserted')->all(),
            empty($results->get('failed')) ? [] : $results->get('failed')->all()
        ));
    }
}
