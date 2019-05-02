<?php

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WebsiteAccessType;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;

return[

    PublicAdministrationStatus::class => [
        PublicAdministrationStatus::PENDING => 'pending',
        PublicAdministrationStatus::ACTIVE => 'active',
        PublicAdministrationStatus::SUSPENDED => 'suspended',
    ],

    UserPermission::class => [
        UserPermission::ACCESS_ADMIN_AREA => 'Access to admin area',
        UserPermission::MANAGE_USERS => 'Manage users',
        UserPermission::MANAGE_WEBSITES => 'Manage websites',
        UserPermission::MANAGE_ANALYTICS => 'Manage analytics',
        UserPermission::READ_ANALYTICS => 'Read analytics',
        UserPermission::DO_NOTHING => 'No permissions',
    ],

    UserRole::class => [
        UserRole::SUPER_ADMIN => 'Super admin of ' . config('app.name'),
        UserRole::ADMIN => 'Admin of own Public Administation',
        UserRole::REGISTERED => 'Registered user',
    ],

    UserStatus::class => [
        UserStatus::INVITED => 'invited',
        UserStatus::INACTIVE => 'inactive',
        UserStatus::PENDING => 'pending',
        UserStatus::ACTIVE => 'active',
        UserStatus::SUSPENDED => 'suspended',
    ],

    WebsiteStatus::class => [
        WebsiteStatus::PENDING => 'pending',
        WebsiteStatus::ACTIVE => 'active',
        WebsiteStatus::ARCHIVED => 'archived',
    ],

    WebsiteType::class => [
        WebsiteType::PRIMARY => 'institutional site',
        WebsiteType::SECONDARY => 'informative or thematic',
        WebsiteType::WEBAPP => 'interactive or web application',
        WebsiteType::TESTING => 'testing or staging',
    ],

    WebsiteAccessType::class => [
        WebsiteAccessType::NO_ACCESS => 'no access',
        WebsiteAccessType::VIEW => 'read-only access',
        WebsiteAccessType::WRITE => 'manage analytics access',
        WebsiteAccessType::ADMIN => 'admin access',
    ],

];
