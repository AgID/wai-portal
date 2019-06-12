<?php

namespace App\Jobs;

use App\Events\Jobs\UserIndexUpdateCompleted;
use App\Models\User;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PredisAdapter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Users index update job.
 */
class ProcessUsersList implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Redisearch index name.
     */
    public const USER_INDEX_NAME = 'UserIndex';

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userIndex = new Index(
            (new PredisAdapter())->connect(config('database.redis.indexes.host'), config('database.redis.indexes.port'), config('database.redis.indexes.database')),
            self::USER_INDEX_NAME
        );

        try {
            // Drop the current index as we want a fresh update
            $userIndex->drop();
        } catch (Exception $e) {
            // Index already dropped, it's ok!
        }

        try {
            $userIndex->addTagField('pas')
                ->addTextField('uuid')
                ->addTextField('familyName', 2.0, true)
                ->addTextField('name', 2.0, true)
                ->create();
        } catch (Exception $e) {
            // Index already exists, it's ok!
        }

        $results = User::withTrashed()->get()->mapToGroups(function ($user) use ($userIndex) {
            $userDocument = $userIndex->makeDocument($user->uuid);
            $userDocument->uuid->setValue($user->uuid);
            $userDocument->familyName->setValue($user->familyName);
            $userDocument->name->setValue($user->name);
            $userDocument->pas->setValue(implode(',', $user->publicAdministrations()->get()->pluck('ipa_code')->toArray()));
            if ($userIndex->replace($userDocument)) {
                return [
                    'inserted' => [
                        'user' => $user->uuid,
                    ],
                ];
            }

            return [
                'failed' => [
                    'user' => $user->uuid,
                ],
            ];
        });

        event(new UserIndexUpdateCompleted(
            empty($results->get('inserted')) ? [] : $results->get('inserted')->all(),
            empty($results->get('failed')) ? [] : $results->get('failed')->all()
        ));
    }
}
