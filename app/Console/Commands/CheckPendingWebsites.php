<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPendingWebsites;
use Illuminate\Console\Command;

class CheckPendingWebsites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-websites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for pending Websites registered in Web Analytics Italia';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new ProcessPendingWebsites())->onConnection('sync');
        $this->info('Pending websites checked');
    }
}
