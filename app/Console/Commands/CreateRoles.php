<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Silber\Bouncer\BouncerFacade as Bouncer;

class CreateRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create roles for Web Analytics Italia';

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
        Bouncer::allow('super-admin')->everything();
        Bouncer::allow('super-admin')->to('access-backoffice');
        Bouncer::allow('admin')->to('manage-users');
        Bouncer::allow('admin')->to('manage-sites');
        Bouncer::allow('admin')->to('manage-analytics');
        Bouncer::allow('admin')->to('read-analytics');
        Bouncer::allow('manager')->to('manage-analytics');
        Bouncer::allow('manager')->to('read-analytics');
        Bouncer::allow('reader')->to('read-analytics');
        Bouncer::allow('registered')->to('do-nothing');
        $this->info('Created roles for Web Analytics Italia');
    }
}
