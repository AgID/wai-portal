<?php

namespace App\Console\Commands;

use App\Jobs\PurgeOrphanedEntities;
use Illuminate\Console\Command;

/**
 * Purge orphaned public entities command.
 */
class Sanitize extends Command
{
    /**
     * Command constructor.
     */
    public function __construct()
    {
        $this->signature = 'app:sanitize';
        $this->description = 'Perform some sanitization tasks on ' . config('app.name');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Purging orphaned entities ...');
        dispatch(new PurgeOrphanedEntities())->onConnection('sync');
        $this->info('Orphaned entities purged');
    }
}
