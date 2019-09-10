<?php

namespace App\Console\Commands;

use App\Jobs\ProcessWebsitesIndex;
use Illuminate\Console\Command;

/**
 * Update websites index.
 */
class UpdateWebsitesIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string the command
     */
    protected $signature = 'app:update-websites';

    /**
     * The console command description.
     *
     * @var string the command description
     */
    protected $description = 'Update Websites index for Web Analytics Italia';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Updating Websites index...');
        dispatch(new ProcessWebsitesIndex())->onConnection('sync');
        $this->info('Websites index updated');
    }
}
