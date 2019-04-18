<?php

namespace App\Console\Commands;

use App\Jobs\ProcessWebsitesMonitoring;
use Illuminate\Console\Command;

/**
 * Monitor websites tracking activity command.
 */
class MonitorWebsiteTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string the command
     */
    protected $signature = 'app:monitor-activity';

    /**
     * The console command description.
     *
     * @var string the command description
     */
    protected $description = 'Check that websites registered in Web Analytics Italia have reported activity within the last two(2) months';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        dispatch(new ProcessWebsitesMonitoring())->onConnection('sync');
        $this->info('Websites activity checked');
    }
}
