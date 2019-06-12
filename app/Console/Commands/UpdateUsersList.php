<?php

namespace App\Console\Commands;

use App\Jobs\ProcessUsersList;
use Illuminate\Console\Command;

/**
 * Update users index.
 */
class UpdateUsersList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string the command
     */
    protected $signature = 'app:update-users';

    /**
     * The console command description.
     *
     * @var string the command description
     */
    protected $description = 'Update Users list for Web Analytics Italia';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Updating Users list...');
        dispatch(new ProcessUsersList())->onConnection('sync');
        $this->info('Users list updated');
    }
}
