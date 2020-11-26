<?php

namespace App\Console\Commands;

use App\Exceptions\SDGServiceException;
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
        $this->signature = 'sdg:send-statistics
            {--from-file= : The file containing the statistics to be sent (can be speciefed with absolute path or relative to the application root)}';
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

        $statisticsFileOption = $this->option('from-file');
        $statisticsFilePath = realpath($statisticsFileOption);

        if (!is_null($statisticsFileOption) && (false === $statisticsFilePath)) {
            throw new SDGServiceException($statisticsFileOption . ' statistics file not found.');
        }

        $statisticsData = !is_null($statisticsFileOption) ? file_get_contents($statisticsFilePath) : null;

        dispatch(new SDGSendStatisticsJob($statisticsData))->onConnection('sync');

        $this->info('Send statistics Single Digital Gateway completed');
    }
}
