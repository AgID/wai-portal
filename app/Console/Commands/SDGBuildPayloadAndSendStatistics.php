<?php

namespace App\Console\Commands;

use App\Jobs\SDGBuildPayloadAndSendStatistics as SDGBuildPayloadAndSendStatisticsJob;
use Illuminate\Console\Command;

class SDGBuildPayloadAndSendStatistics extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'sdg:build-and-send-to-information-services';
        $this->description = 'Send a dataset to Single Digital Gateway API for the Statistics on Information Services';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Send statistics Single Digital Gateway start');
        dispatch(new SDGBuildPayloadAndSendStatisticsJob())->onConnection('sync');
        $this->info('Send statistics Single Digital Gateway completed');
    }
}
