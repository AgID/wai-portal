<?php

namespace App\Jobs;

use App\Traits\BuildDatasetForSingleDigitalGatewayAPI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SDGSendStatistics implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use BuildDatasetForSingleDigitalGatewayAPI;

    /**
     * The provided statistics data.
     *
     * @var string|null Statistics data
     */
    protected $statisticsData;

    /**
     * Create a new job instance.
     *
     * @param string|null $statisticsData
     *
     * @return void
     */
    public function __construct($statisticsData)
    {
        $this->statisticsData = $statisticsData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (is_null($this->statisticsData)) {
            $this->statisticsData = $this->buildDatasetForSDG();
        }

        $sdgService = app()->make('single-digital-gateway-service');

        $sdgService->sendStatisticsInformation($this->statisticsData);
    }
}
