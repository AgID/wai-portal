<?php

namespace App\Console\Commands;

use App\Jobs\UpdateClosedBetaWhitelist;
use Illuminate\Console\Command;

/**
 * Update closed beta whitelist command.
 */
class InitClosedBetaWhitelist extends Command
{
    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        $this->signature = 'app:init-closed-beta-whitelist';
        $this->description = 'Initialize the closed beta whitelist';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Downloading closed beta whitelist...');
        dispatch(new UpdateClosedBetaWhitelist())->onConnection('sync');
        $this->info('Closed beta whitelist downloaded');
    }
}
