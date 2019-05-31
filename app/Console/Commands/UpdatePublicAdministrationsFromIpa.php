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
     * The name and signature of the console command.
     *
     * @var string the command
     */
    protected $signature = 'app:update-ipa';

    /**
     * The console command description.
     *
     * @var string the command description
     */
    protected $description = 'Update Public Administrations from IPA list for Web Analytics Italia';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Updating Public Administrations list...');
        dispatch(new ProcessPublicAdministrationsUpdateFromIpa())->onConnection('sync');
        $this->info('Public Administrations updated');
    }
}
