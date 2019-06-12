<?php

namespace App\Console\Commands;

use App\Jobs\ProcessWebsitesList;
use Illuminate\Console\Command;

/**
 * Update websites index.
 */
class UpdateWebsitesList extends Command
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
    protected $description = 'Update Websites list for Web Analytics Italia';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Updating Websites list...');
        dispatch(new ProcessWebsitesList())->onConnection('sync');
        $this->info('Websites list updated');
    }
}
