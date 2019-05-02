<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPendingWebsites;
use Illuminate\Console\Command;

/**
 * Check pending websites command.
 */
class CheckPendingWebsites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string the command
     */
    protected $signature = 'app:check-websites';

    /**
     * The console command description.
     *
     * @var string the command description
     */
    protected $description = 'Check for pending Websites registered in Web Analytics Italia';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Checking pending websites...');
        dispatch(new ProcessPendingWebsites())->onConnection('sync');
        $this->info('Pending websites checked');
    }
}
