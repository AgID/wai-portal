<?php

namespace App\Console\Commands;

use App\Traits\BuildsDatasetForSingleDigitalGatewayAPI;
use Illuminate\Console\Command;

class SDGBuildStatistics extends Command
{
    use BuildsDatasetForSingleDigitalGatewayAPI;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'sdg:build-statistics
            {--period= : The reference period for the statistics data in YYYY-MM-DD,YYYY-MM-DD format (e.g. 2021-01-01,2021-01-31)}';
        $this->description = 'Build and outputs statistics data for the Single Digital Gateway statistics information endpoint.';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $periodOption = $this->option('period');
        $dataset = $this->buildDatasetForSDG($periodOption);

        echo json_encode($dataset, JSON_PRETTY_PRINT) . PHP_EOL;
    }
}
