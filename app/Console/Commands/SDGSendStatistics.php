<?php

namespace App\Console\Commands;

use App\Jobs\SDGSendStatistics as SDGSendStatisticsJob;
use Illuminate\Console\Command;

class SDGSendStatistics extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'sdg:send-information-services';
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
        dispatch(new SDGSendStatisticsJob())->onConnection('sync');
        $this->info('Send statistics Single Digital Gateway completed');
    }
}
