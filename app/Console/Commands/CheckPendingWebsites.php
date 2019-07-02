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
    protected $signature = 'app:check-websites {--D|execute-purge-check : Whether the websites purge check should be executed}';

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
        $this->info('Checking pending websites...' . ($this->option('execute-purge-check') ? ' [executing purge check]' : ''));
        dispatch(new ProcessPendingWebsites($this->option('execute-purge-check')))->onConnection('sync');
        $this->info('Pending websites checked');
    }
}
