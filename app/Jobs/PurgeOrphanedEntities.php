<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Events\Jobs\PurgeOrphanedEntitiesCompleted;
use App\Models\PublicAdministration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Purge orphaned entities job.
 */
class PurgeOrphanedEntities implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info(
            'Start purging orphaned entities',
            [
                'job' => JobType::PURGE_ORPHANED_ENTITIES,
            ]
        );

        $processedPublicAdministrations = $this->purgePublicAdministrationsWithoutPrimaryWebsite();

        event(new PurgeOrphanedEntitiesCompleted([
            'publicAdministrations' => [
                'not_purged' => empty($processedPublicAdministrations->get('not_purged')) ? [] : $processedPublicAdministrations->get('not_purged')->all(),
                'purged' => empty($processedPublicAdministrations->get('purged')) ? [] : $processedPublicAdministrations->get('purged')->all(),
            ],
        ]));
    }

    protected function purgePublicAdministrationsWithoutPrimaryWebsite()
    {
        return PublicAdministration::doesntHave('websites')->get()->mapToGroups(function ($publicAdministration) {
            $abilities = DB::table('abilities')->where('scope', $publicAdministration->id)->get();
            if ($abilities->isNotEmpty()) {
                return [
                    'not_purged' => [
                        'publicAdministration' => $publicAdministration->ipa_code,
                        'reason' => 'Abilities table not empty',
                    ],
                ];
            }

            $assigned_roles = DB::table('assigned_roles')->where('scope', $publicAdministration->id)->get();
            if ($assigned_roles->isNotEmpty()) {
                return [
                    'not_purged' => [
                        'publicAdministration' => $publicAdministration->ipa_code,
                        'reason' => 'Assigned roles table not empty',
                    ],
                ];
            }

            $permissions = DB::table('permissions')->where('scope', $publicAdministration->id)->get();
            if ($permissions->isNotEmpty()) {
                return [
                    'not_purged' => [
                        'publicAdministration' => $publicAdministration->ipa_code,
                        'reason' => 'Permissions table not empty',
                    ],
                ];
            }

            if ($publicAdministration->users->isNotEmpty()) {
                return [
                    'not_purged' => [
                        'publicAdministration' => $publicAdministration->ipa_code,
                        'reason' => 'Public administration has some users',
                    ],
                ];
            }

            $publicAdministration->forceDelete();

            return [
                'purged' => [
                    'publicAdministration' => $publicAdministration->ipa_code,
                ],
            ];
        });
    }
}
