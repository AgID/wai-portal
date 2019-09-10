<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Enums\WebsiteType;
use App\Events\Jobs\PublicAdministrationsUpdateFromIpaCompleted;
use App\Events\PublicAdministration\PublicAdministrationUpdated;
use App\Events\PublicAdministration\PublicAdministrationWebsiteUpdated;
use App\Models\PublicAdministration;
use App\Traits\InteractsWithRedisIndex;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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

        $registeredPublicAdministrations = PublicAdministration::withTrashed()->get();
        $report = $registeredPublicAdministrations->mapWithKeys(function ($publicAdministration) {
            $ipaCode = $publicAdministration->ipa_code;
            $updatedPublicAdministration = $this->getPublicAdministrationEntryByIpaCode($ipaCode);

            return [$ipaCode => $this->updateExistingPA($publicAdministration, $updatedPublicAdministration)];
        });

        event(new PublicAdministrationsUpdateFromIpaCompleted($report->all()));
    }

    /**
     * Update a registered Public Administration.
     *
     * @param PublicAdministration $publicAdministration the public administration to be updated
     * @param array $updatedPublicAdministration the IPA data for the public administration
     *
     * @return array the array containing the list of updated data for the public administration, empty if none
     */
    private function updateExistingPA(PublicAdministration $publicAdministration, array $updatedPublicAdministration): array
    {
        if (empty($updatedPublicAdministration)) {
            // TODO: public administration not present in ipa, what should be done?
            return [];
        }

        if ($publicAdministration->name !== $updatedPublicAdministration['name']) {
            $updates['name'] = [
                'old' => $publicAdministration->name,
                'new' => $updatedPublicAdministration['name'],
            ];
            $publicAdministration->name = $updatedPublicAdministration['name'];
        }

        if ($publicAdministration->city !== $updatedPublicAdministration['city']) {
            $updates['city'] = [
                'old' => $publicAdministration->city,
                'new' => $updatedPublicAdministration['city'],
            ];
            $publicAdministration->city = $updatedPublicAdministration['city'];
        }
        if ($publicAdministration->county !== $updatedPublicAdministration['county']) {
            $updates['county'] = [
                'old' => $publicAdministration->county,
                'new' => $updatedPublicAdministration['county'],
            ];
            $publicAdministration->county = $updatedPublicAdministration['county'];
        }
        if ($publicAdministration->region !== $updatedPublicAdministration['region']) {
            $updates['region'] = [
                'old' => $publicAdministration->region,
                'new' => $updatedPublicAdministration['region'],
            ];
            $publicAdministration->region = $updatedPublicAdministration['region'];
        }
        if ($publicAdministration->type !== $updatedPublicAdministration['type']) {
            $updates['type'] = [
                'old' => $publicAdministration->type,
                'new' => $updatedPublicAdministration['type'],
            ];
            $publicAdministration->type = $updatedPublicAdministration['type'];
        }
        if ($publicAdministration->pec_address !== $updatedPublicAdministration['pec']) {
            $updates['type'] = [
                'old' => $publicAdministration->pec_address,
                'new' => $updatedPublicAdministration['pec'],
            ];
            $publicAdministration->pec_address = $updatedPublicAdministration['pec'];
        }

        $primaryWebsite = $publicAdministration->websites()->where('type', WebsiteType::PRIMARY)->first();
        if (!empty($primaryWebsite) && !empty($updatedPublicAdministration['site']) && $primaryWebsite->slug !== Str::slug($updatedPublicAdministration['site'])) {
            $updates['site'] = [
                'old' => $primaryWebsite->url,
                'new' => $updatedPublicAdministration['site'],
            ];
            event(new PublicAdministrationWebsiteUpdated($publicAdministration, $primaryWebsite, $updatedPublicAdministration['site']));
        }

        if (!empty($updates)) {
            $publicAdministration->save();
            event(new PublicAdministrationUpdated($publicAdministration, $updates));
        }

        return $updates ?? [];
    }
}
