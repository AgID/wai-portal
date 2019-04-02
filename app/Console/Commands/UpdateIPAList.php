<?php

namespace App\Console\Commands;

use App\Jobs\ProcessIPAList;
use Illuminate\Console\Command;

/**
 * Update IPA command.
 */
class UpdateIPAList extends Command
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
    protected $description = 'Update IPA list for Web Analytics Italia';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new ProcessIPAList())->onConnection('sync');
        $this->info('IPA list updated');
    }
}
