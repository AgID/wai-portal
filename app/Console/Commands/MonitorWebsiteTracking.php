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
     * Command constructor.
     */
    public function __construct()
    {
        $this->description = 'Check that websites registered in Web Analytics Italia have reported activity within the last ' . config('wai.archive_expire') . ' days';
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Checking websites activity...');
        dispatch(new ProcessWebsitesMonitoring())->onConnection('sync');
        $this->info('Websites activity checked');
    }
}
