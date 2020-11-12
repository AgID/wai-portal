<?php

namespace App\Jobs;

use App\Traits\BuildDatasetForSingleDigitalGatewayAPI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SDGBuildPayloadAndSendStatistics implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use BuildDatasetForSingleDigitalGatewayAPI;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dataset = $this->buildDatasetForSDG();
        $sDGService = app()->make('single-digital-gateway-service');
        $sDGService->sendStatisticsInformation($dataset);
        $sDGService->savePayloadToFilesystem($dataset);
    }
}
