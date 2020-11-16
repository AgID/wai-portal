<?php

namespace App\Console\Commands;

use App\Jobs\SDGValidatePayloadFile as SDGValidatePayloadFileJob;
use Illuminate\Console\Command;

class SDGValidatePayloadFile extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'sdg:validate-payload-file';
        $this->description = 'Validate Payload against provided json scheme';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Validate payload file for Single Digital Gateway start');
        dispatch(new SDGValidatePayloadFileJob())->onConnection('sync');
        $this->info('Validate payload file for Single Digital Gateway completed');
    }
}
