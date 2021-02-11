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
        $this->signature = 'sdg:build-statistics';
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
        $dataset = $this->buildDatasetForSDG();

        echo json_encode($dataset, JSON_PRETTY_PRINT) . PHP_EOL;
    }
}
