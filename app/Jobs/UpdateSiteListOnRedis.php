<?php

namespace App\Jobs;

use App\Models\PublicAdministration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class UpdateSiteListOnRedis implements ShouldQueue
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
        $publicAdministrationsList = PublicAdministration::all();

        Redis::connection(env('CACHE_CONNECTION'))->client()->pipeline(function ($pipe) use ($publicAdministrationsList) {
            foreach ($publicAdministrationsList as &$publicAdministration) {
                $list = $publicAdministration->websites()->get()
                ->map(function ($website) {
                    $url = collect($website->toArray())
                    ->only(['url'])
                    ->all();

                    return $url['url'];
                })->values()->toArray();

                $id = $publicAdministration['id'];

                logger()->notice(
                    'Caching websites for public administrations',
                    [
                        'pa' => $id,
                        'list' => json_encode($list),
                    ]
                );

                $pipe->set('websiteList-' . $id, json_encode($list));
            }
        });
    }
}
