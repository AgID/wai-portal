<?php

namespace App\Console\Commands;

use App\Jobs\ProcessWebsitesMonitoring;
use Illuminate\Console\Command;

class MonitorWebsiteTracking extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:monitor-activity';

    /**
     * @var string
     */
    protected $description = 'Check that websites registered in Web Analytics Italia have reported activity within the last two(2) months';

    public function handle(): void
    {
        dispatch(new ProcessWebsitesMonitoring())->onConnection('sync');
        $this->info('Websites activity checked');
    }
}
