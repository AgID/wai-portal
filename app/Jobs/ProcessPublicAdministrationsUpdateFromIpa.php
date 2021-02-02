<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Enums\WebsiteType;
use App\Events\Jobs\PublicAdministrationsUpdateFromIpaCompleted;
use App\Events\PublicAdministration\PublicAdministrationNotFoundInIpa;
use App\Events\PublicAdministration\PublicAdministrationPrimaryWebsiteUpdated;
use App\Events\PublicAdministration\PublicAdministrationUpdated;
use App\Models\PublicAdministration;
use App\Traits\InteractsWithRedisIndex;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Public administrations update job.
 */
class ProcessPublicAdministrationsUpdateFromIpa implements ShouldQueue
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
            'Updating public administrations from IPA index',
            [
                'job' => JobType::UPDATE_PA_FROM_IPA,
            ]
        );

        $publicAdministrationsQuery = PublicAdministration::withTrashed();

        if (config('wai.custom_public_administrations', false)) {
            $publicAdministrationsQuery = $publicAdministrationsQuery->whereHas('websites', function (Builder $query) {
                $query->where('type', '<>', WebsiteType::INSTITUTIONAL_PLAY);
            });
        }

        $registeredPublicAdministrations = $publicAdministrationsQuery->get();

        $report = $registeredPublicAdministrations->mapWithKeys(function ($publicAdministration) {
            $ipaCode = $publicAdministration->ipa_code;
            $updatedPublicAdministration = $this->getPublicAdministrationEntryByIpaCode($ipaCode);

            return [$ipaCode => $this->updateExistingPublicAdministration($publicAdministration, $updatedPublicAdministration)];
        });

        event(new PublicAdministrationsUpdateFromIpaCompleted($report->all()));
    }

    /**
     * Update a registered Public Administration.
     *
     * @param PublicAdministration $publicAdministration the public administration to be updated
     * @param array|null $updatedPublicAdministration the IPA data for the public administration or null if not existing
     *
     * @return array the array containing the list of updated data for the public administration, empty if none
     */
    private function updateExistingPublicAdministration(PublicAdministration $publicAdministration, ?array $updatedPublicAdministration): array
    {
        if (empty($updatedPublicAdministration)) {
            event(new PublicAdministrationNotFoundInIpa($publicAdministration));

            return [];
        }

        // id key from ipa (redisearch) is not related to model id
        unset($updatedPublicAdministration['id']);

        $updates = collect(array_keys($updatedPublicAdministration))->mapWithKeys(function ($updatedPublicAdministrationField) use ($publicAdministration, $updatedPublicAdministration) {
            if (array_key_exists($updatedPublicAdministrationField, $publicAdministration->attributesToArray())) {
                $updatedValue = $updatedPublicAdministration[$updatedPublicAdministrationField];

                if ($publicAdministration->{$updatedPublicAdministrationField} !== $updatedValue) {
                    $update = [
                        $updatedPublicAdministrationField => [
                            'old' => $publicAdministration->{$updatedPublicAdministrationField},
                            'new' => $updatedValue,
                        ],
                    ];
                    $publicAdministration->{$updatedPublicAdministrationField} = $updatedValue;

                    return $update;
                }
            }

            return [];
        })->all();

        $primaryWebsite = $publicAdministration->websites()->where('type', WebsiteType::INSTITUTIONAL)->first();
        if (!empty($primaryWebsite) && !empty($updatedPublicAdministration['site']) && $primaryWebsite->slug !== Str::slug($updatedPublicAdministration['site'])) {
            $updates['site'] = [
                'old' => $primaryWebsite->url,
                'new' => $updatedPublicAdministration['site'],
            ];

            event(new PublicAdministrationPrimaryWebsiteUpdated($publicAdministration, $primaryWebsite, $updatedPublicAdministration['site']));
        }

        if (!empty($updates)) {
            $publicAdministration->save();

            event(new PublicAdministrationUpdated($publicAdministration, $updates));
        }

        return $updates ?? [];
    }
}
