<?php

namespace App\Console\Commands;

use App\Jobs\ResetEnvironment as ResetEnvironmentJob;
use Illuminate\Console\Command;

class ResetEnvironment extends Command
{
    public function __construct()
    {
        $this->signature = 'app:reset-environment';
        $this->description = 'Reset current portal environment';
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Environment "' . config('app.env') . '" reset started...');
        dispatch(new ResetEnvironmentJob())->onConnection('sync');
        $this->info('Environment "' . config('app.env') . '" reset completed');
    }
}
