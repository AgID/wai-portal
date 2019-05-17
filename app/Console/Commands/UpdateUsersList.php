<?php

namespace App\Console\Commands;

use App\Jobs\ProcessUsersList;
use Illuminate\Console\Command;

class UpdateUsersList extends Command
{
    protected $signature = 'app:update-users';

    protected $description = 'Update Users list for Web Analytics Italia';

    public function handle()
    {
        $this->info('Updating Users list...');
        dispatch(new ProcessUsersList())->onConnection('sync');
        $this->info('Users list updated');
    }
}
