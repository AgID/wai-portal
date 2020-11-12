<?php

namespace App\Console\Commands;

use App\Jobs\SDGBuildAndSavePayload as SDGBuildAndSavePayloadJob;
use Illuminate\Console\Command;

class SDGBuildAndSavePayload extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'sdg:build-and-save-payload';
        $this->description = 'Prepare payload to send to the Single Digital Gateway and print it to the stdout';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Build Payload start');
        dispatch(new SDGBuildAndSavePayloadJob())->onConnection('sync');
        $this->info('Build Payload completed');
    }
}
