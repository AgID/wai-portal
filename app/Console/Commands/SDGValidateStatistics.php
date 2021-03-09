<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SDGValidateStatistics extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'sdg:validate-statistics
            {statisticsFile : The file containing the statistics to be validated (can be specified with absolute path or relative to the application root)}';
        $this->description = 'Validate statistics data against provided json scheme';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $statisticsFileArgument = $this->argument('statisticsFile');
        $statisticsFilePath = realpath($statisticsFileArgument);

        if (false === $statisticsFilePath) {
            throw new SDGServiceException($statisticsFileArgument . ' statistics file not found.');
        }

        $statisticsData = file_get_contents($statisticsFilePath);
        $statisticsDataObject = json_decode($statisticsData);

        if (is_null($statisticsDataObject)) {
            throw new SDGServiceException('Invalid statistics data: incorrect JSON format.');
        }

        $sdgService = app()->make('single-digital-gateway-service');

        $sdgService->validatePayload($statisticsDataObject);

        $this->info('The statistics data is valid.');
    }
}
