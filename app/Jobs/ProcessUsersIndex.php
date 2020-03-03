<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Events\Jobs\UserIndexUpdateCompleted;
use App\Models\User;
use App\Traits\InteractsWithRedisIndex;
use Ehann\RediSearch\Index;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Users index update job.
 */
class ProcessUsersIndex implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use InteractsWithRedisIndex;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info(
            'Processing users for redis index update',
            [
                'job' => JobType::PROCESS_USERS_INDEX,
            ]
        );

        $userIndex = $this->getRedisIndex('users');

        try {
            // Drop the current index as we want a fresh update
            $userIndex->drop();
        } catch (Exception $e) {
            // Index already dropped, it's ok!
        }

        try {
            $userIndex->addTagField('pas')
                ->addTextField('uuid')
                ->addTextField('family_name', 2.0, true)
                ->addTextField('name', 2.0, true)
                ->create();
        } catch (Exception $e) {
            // Index already exists, it's ok!
        }

        $results = User::withTrashed()->get()->mapToGroups(function ($user) use ($userIndex) {
            $userDocument = $userIndex->makeDocument($user->uuid);
            $userDocument->uuid->setValue($user->uuid);
            $userDocument->family_name->setValue($user->family_name);
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
