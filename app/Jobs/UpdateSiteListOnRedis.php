<?php

namespace App\Jobs;

use App\Contracts\AnalyticsService;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class UpdateSiteListOnRedis implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Analytics Service.
     *
     * @var AnalyticsService
     */
    protected $analyticsService;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->analyticsService = app()->make('analytics-service');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $websiteList = Website::all();
        $defaultHostList = "api.webanalytics.italia.it webanalytics.italia.it www.webanalytics.italia.it";

        foreach ($websiteList as $website) {
            $id = $website->analytics_id;
            $list = $this->analyticsService->getSiteUrlsFromId($id);

            $hostList = array_map(function ($url) { return parse_url($url, PHP_URL_HOST); }, $list);

            $hostListToString = implode(' ', $hostList);

            Cache::store('csp')->connection()->set($id, $hostListToString ?: $defaultHostList);
        }

        logger()->info(
            'Websites URLs cache refreshed'
        );
    }
}
