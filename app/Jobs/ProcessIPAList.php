<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PhpRedisAdapter;
use Carbon\Carbon;
use Exception;

class ProcessIPAList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $IPACSVUrl = 'http://www.indicepa.gov.it/public-services/opendata-read-service.php?dstype=FS&filename=amministrazioni.txt';
        $fileName = 'ipa_csv/ipa_csv_'.Carbon::now()->toDateString().'.csv';

        if (!Storage::exists($fileName)) {
            try {
                $IPAResource = fopen($IPACSVUrl, "rb");
            } catch (Exception $e) {
                logger()->error('Download of IPA CSV list failed at '.Carbon::now());
                return;
            }

            Storage::put($fileName, $IPAResource);
        }

        $IPAIndex = new Index((new PhpRedisAdapter)->connect(config('database.redis.ipaindex.host'), config('database.redis.ipaindex.port'), config('database.redis.ipaindex.database')), 'IPAIndex');

        try {
            $IPAIndex->addTextField('ipa_code', 1.0, true)
                ->addTextField('name', 1.0, true)
                ->addTextField('site')
                ->addTextField('pec')
                ->addTextField('city', 0.5)
                ->addTextField('county')
                ->addTextField('region')
                ->addTextField('type')
                ->create();
        } catch (Exception $e) {
            // Index already exists, it's ok!
        }

        $handle = fopen(storage_path('app/'.$fileName), "r");
        // Drop header row
        fgetcsv($handle, 0, "\t");
        while (($data = fgetcsv($handle, 0, "\t")) !== false) {
            try {
                $amministrazione = $IPAIndex->makeDocument($data[0]);
                $amministrazione->ipa_code->setValue($data[0]);
                $amministrazione->name->setValue($data[1]);
                $amministrazione->site->setValue($data[8]);
                $amministrazione->pec->setValue(null);
                $amministrazione->city->setValue($data[2]);
                $amministrazione->county->setValue($data[6]);
                $amministrazione->region->setValue($data[7]);
                $amministrazione->type->setValue($data[11]);
                for ($i = 16; $i <= 24; $i = $i+2) {
                    if (strtolower($data[$i+1]) == 'pec') {
                        $amministrazione->pec->setValue($data[$i]);
                        break;
                    }
                }
                $IPAIndex->replace($amministrazione);
            } catch (Exception $e) {}
        }
        logger()->info('Completed import of IPA list from http://www.indicepa.gov.it');
    }
}
