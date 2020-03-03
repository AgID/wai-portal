<?php

namespace App\Console\Commands;

use App\Jobs\ProcessUsersIndex;
use Illuminate\Console\Command;

/**
 * Update users index.
 */
class UpdateUsersIndex extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'app:update-users';
        $this->description = 'Update Users index for ' . config('app.name');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Updating Users index...');
        dispatch(new ProcessUsersIndex())->onConnection('sync');
        $this->info('Users index updated');
    }
}
