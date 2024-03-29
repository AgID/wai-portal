<?php

namespace App\Console\Commands;

use App\Jobs\MonitorWebsitesTracking;
use Illuminate\Console\Command;

/**
 * Monitor websites tracking activity command.
 */
class MonitorWebsiteTracking extends Command
{
    /**
     * Command constructor.
     */
    public function __construct()
    {
        $this->signature = 'app:monitor-activity';
        $this->description = 'Check that websites registered in ' . config('app.name') . ' have reported activity within the last ' . config('wai.archive_expire') . ' days';
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Checking websites activity...');
        dispatch(new MonitorWebsitesTracking())->onConnection('sync');
        $this->info('Websites activity checked');
    }
}
