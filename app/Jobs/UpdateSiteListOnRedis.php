<?php

namespace App\Jobs;

use App\Models\PublicAdministration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

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

                        $url = $url['url'];

                        $hasProtocol = Str::startsWith($url, 'http');

                        return $hasProtocol ? $url : 'http://' . $url . ' ' . 'https://' . $url;
                    })->values()->toArray();

                $id = $publicAdministration['id'];

                $listToString = implode(' ', $list);

                logger()->notice(
                    'Caching websites for public administrations',
                    [
                        'pa' => $id,
                        'list' => $listToString,
                    ]
                );

                $pipe->set('websiteList-' . $id, $listToString);
            }
        });
    }
}
