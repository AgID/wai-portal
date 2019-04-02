<?php

namespace App\Jobs;

use App\Enums\WebsiteType;
use App\Events\Jobs\IPAUpdateCompleted;
use App\Events\Jobs\IPAUpdateFailed;
use App\Events\PublicAdministration\PublicAdministrationUpdated;
use App\Events\PublicAdministration\PublicAdministrationWebsiteUpdated;
use App\Models\PublicAdministration;
use Carbon\Carbon;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PredisAdapter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * IPA update job.
 */
class ProcessIPAList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $IPACSVUrl = 'https://www.indicepa.gov.it/public-services/opendata-read-service.php?dstype=FS&filename=amministrazioni.txt';
        $filename = 'ipa_csv/ipa_csv_' . Carbon::now()->toDateString() . '.csv';

        if (!Storage::exists($filename)) {
            try {
                $ipaResource = fopen($IPACSVUrl, 'rb');
            } catch (Exception $e) {
                logger()->error('Download of IPA CSV list failed at ' . Carbon::now());
                event(new IPAUpdateFailed('Download failed'));

                return;
            }

            Storage::put($filename, $ipaResource);
        }

        $ipaIndex = new Index((new PredisAdapter())->connect(config('database.redis.ipaindex.host'), config('database.redis.ipaindex.port'), config('database.redis.ipaindex.database')), 'IPAIndex');

        try {
            $ipaIndex->addTextField('ipa_code', 2.0, true)
                ->addTextField('name', 2.0, true)
                ->addTextField('site')
                ->addTextField('pec')
                ->addTextField('city', 1.5)
                ->addTextField('county')
                ->addTextField('region')
                ->addTextField('type')
                ->create();
        } catch (Exception $e) {
            // Index already exists, it's ok!
        }

        $handle = fopen(storage_path('app/' . $filename), 'rb');

        if (!$handle) {
            event(new IPAUpdateFailed('Unable to open IPA file'));

            return;
        }

        // Drop header row
        $count = count(fgetcsv($handle, 0, "\t"));
        if ($count < 12) {
            event(new IPAUpdateFailed('Wrong IPA file format'));

            return;
        }

        while (false !== ($data = fgetcsv($handle, 0, "\t"))) {
            try {
                for ($i = 16; $i <= 24 && $i < $count; $i += 2) {
                    if ('pec' === strtolower($data[$i + 1])) {
                        $pec = $data[$i];
                        break;
                    }
                }

                if (empty($pec)) {
                    logger()->warning($count . 'No PEC email found into IPA data for Public Administration ' . $data[1] . ' [' . $data[0] . ']. Some functions may not work and notifications cannot be sent');
                }

                $retrievedPA = [
                    'ipa_code' => $data[0],
                    'name' => $data[1],
                    'site' => $data[8],
                    'city' => $data[2],
                    'county' => $data[6],
                    'region' => $data[7],
                    'type' => $data[11],
                    'pec' => $pec ?? '',
                ];

                $ipaUpdate = $this->updateExistingPA($retrievedPA);

                if (!empty($ipaUpdate)) {
                    $report[$retrievedPA['ipa_code']] = $ipaUpdate;
                }

                $indexedPA = $ipaIndex->makeDocument($retrievedPA['ipa_code']);
                $indexedPA->ipa_code->setValue($retrievedPA['ipa_code']);
                $indexedPA->name->setValue($retrievedPA['name']);
                $indexedPA->site->setValue($retrievedPA['site']);
                $indexedPA->pec->setValue($retrievedPA['pec']);
                $indexedPA->city->setValue($retrievedPA['city']);
                $indexedPA->county->setValue($retrievedPA['county']);
                $indexedPA->region->setValue($retrievedPA['region']);
                $indexedPA->type->setValue($retrievedPA['type']);
                $ipaIndex->replace($indexedPA);

                logger()->info('Public Administration ' . $data[1] . ' [' . $data[0] . '] successfully added to index');
            } catch (Exception $exception) {
                logger()->error('Unable to index Public Administration ' . $data[1] . ' [' . $data[0] . ']: ' . $exception->getMessage());
            }
        }

        event(new IPAUpdateCompleted($report ?? []));
    }

    /**
     * Update a registered Public Administration.
     *
     * @param array $retrievedPA the IPA data for a public administration
     *
     * @return array the array containing the list of updated data for the public administration,
     *               empty if none or public administration is not registered
     */
    private function updateExistingPA(array $retrievedPA): array
    {
        $existingPA = PublicAdministration::withTrashed()->where('ipa_code', $retrievedPA['ipa_code'])->first();

        if (empty($existingPA)) {
            return [];
        }

        if ($existingPA->name !== $retrievedPA['name']) {
            $updates['name'] = [
                'old' => $existingPA->name,
                'new' => $retrievedPA['name'],
            ];
            $existingPA->name = $retrievedPA['name'];
        }

        if ($existingPA->city !== $retrievedPA['city']) {
            $updates['city'] = [
                'old' => $existingPA->city,
                'new' => $retrievedPA['city'],
            ];
            $existingPA->city = $retrievedPA['city'];
        }
        if ($existingPA->county !== $retrievedPA['county']) {
            $updates['county'] = [
                'old' => $existingPA->county,
                'new' => $retrievedPA['county'],
            ];
            $existingPA->county = $retrievedPA['county'];
        }
        if ($existingPA->region !== $retrievedPA['region']) {
            $updates['region'] = [
                'old' => $existingPA->region,
                'new' => $retrievedPA['region'],
            ];
            $existingPA->region = $retrievedPA['region'];
        }
        if ($existingPA->type !== $retrievedPA['type']) {
            $updates['type'] = [
                'old' => $existingPA->type,
                'new' => $retrievedPA['type'],
            ];
            $existingPA->type = $retrievedPA['type'];
        }
        if ($existingPA->pec_address !== $retrievedPA['pec']) {
            $updates['type'] = [
                'old' => $existingPA->pec_address,
                'new' => $retrievedPA['pec'],
            ];
            $existingPA->pec_address = $retrievedPA['pec'];
        }

        $primaryWebsite = $existingPA->websites()->where('type', WebsiteType::PRIMARY)->first();
        if (!empty($primaryWebsite) && !empty($retrievedPA['site']) && $primaryWebsite->slug !== Str::slug($retrievedPA['site'])) {
            $updates['site'] = [
                'old' => $primaryWebsite->url,
                'new' => $retrievedPA['site'],
            ];
            event(new PublicAdministrationWebsiteUpdated($existingPA, $primaryWebsite, $retrievedPA['site']));
        }

        if (!empty($updates)) {
            $existingPA->save();
            event(new PublicAdministrationUpdated($existingPA, $updates));
        }

        return $updates ?? [];
    }
}
