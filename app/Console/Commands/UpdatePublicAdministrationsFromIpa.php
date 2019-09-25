<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPublicAdministrationsUpdateFromIpa;
use Illuminate\Console\Command;

/**
 * Update Public Administrations from IPA command.
 */
class UpdatePublicAdministrationsFromIpa extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'app:update-ipa';
        $this->description = 'Update Public Administrations from IPA index for ' . config('app.name');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Updating Public Administrations index...');
        dispatch(new ProcessPublicAdministrationsUpdateFromIpa())->onConnection('sync');
        $this->info('Public Administrations updated');
    }
}
