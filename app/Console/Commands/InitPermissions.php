<?php

namespace App\Console\Commands;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use Illuminate\Console\Command;
use Silber\Bouncer\BouncerFacade as Bouncer;

class InitPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize user permissions for Web Analytics Italia';

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
        Bouncer::allow(UserRole::SUPER_ADMIN)->everything();
        Bouncer::allow(UserRole::SUPER_ADMIN)->to(UserPermission::ACCESS_ADMIN_AREA);
        Bouncer::allow(UserRole::ADMIN)->to(UserPermission::MANAGE_USERS);
        Bouncer::allow(UserRole::ADMIN)->to(UserPermission::MANAGE_WEBSITES);
        Bouncer::allow(UserRole::ADMIN)->to(UserPermission::VIEW_LOGS);
        Bouncer::allow(UserRole::DELEGATED)->to(UserPermission::DO_NOTHING);
        Bouncer::allow(UserRole::REGISTERED)->to(UserPermission::DO_NOTHING);
        $this->info('Created roles for Web Analytics Italia');
    }
}
