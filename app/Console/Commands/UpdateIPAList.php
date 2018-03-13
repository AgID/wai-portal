<?php

namespace App\Console\Commands;

use App\Jobs\ProcessIPAList;
use Illuminate\Console\Command;

class UpdateIPAList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-ipa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update IPA list for Analytics Italia';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ProcessIPAList::dispatch();
        $this->info('IPA list updated');
    }
}
