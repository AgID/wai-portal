<?php

namespace App\Console\Commands;

use App\Jobs\ProcessWebsitesList;
use Illuminate\Console\Command;

class UpdateWebsitesList extends Command
{
    protected $signature = 'app:update-websites';

    protected $description = 'Update Websites list for Web Analytics Italia';

    public function handle()
    {
        $this->info('Updating Websites list...');
        dispatch(new ProcessWebsitesList())->onConnection('sync');
        $this->info('Websites list updated');
    }
}
