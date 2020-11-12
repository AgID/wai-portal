<?php

namespace App\Console\Commands;

use App\Jobs\SDGSendStatisticsFromFile as SDGSendStatisticsFromFileJob;
use Illuminate\Console\Command;

class SDGSendStatisticsFromFile extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'sdg:send-statistics-from-file';
        $this->description = 'Send a dataset (from file) to Single Digital Gateway API for the Statistics on Information Services';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Send statistics Single Digital Gateway start');
        dispatch(new SDGSendStatisticsFromFileJob())->onConnection('sync');
        $this->info('Send statistics Single Digital Gateway completed');
    }
}
