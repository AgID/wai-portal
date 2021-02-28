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
            {--date= : The reference date (in YYYY-MM-DD format) for the month to consider for the statistics data }';
        $this->description = 'Build and outputs statistics data for the Single Digital Gateway statistics information endpoint.';
        $this->description .= 'If no --date option is provided, defaults to the previous month.';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $dateOption = $this->option('date');
        $dataset = $this->buildDatasetForSDG($dateOption);

        echo json_encode($dataset, JSON_PRETTY_PRINT) . PHP_EOL;
    }
}
