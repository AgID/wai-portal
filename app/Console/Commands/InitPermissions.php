<?php

namespace App\Console\Commands;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use Illuminate\Console\Command;
use Silber\Bouncer\BouncerFacade as Bouncer;

class InitPermissions extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signature = 'app:init-permissions';
        $this->description = 'Initialize user permissions for ' . config('app.name');
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
        Bouncer::forbid(UserRole::DELETED)->everything();
        $this->info('Created roles for ' . config('app.name'));
    }
}
