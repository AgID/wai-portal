<?php

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Enums\Logs\JobType;
use App\Enums\PublicAdministrationStatus;
use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WebsiteAccessType;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;

return [

    PublicAdministrationStatus::class => [
        PublicAdministrationStatus::PENDING => 'pending',
        PublicAdministrationStatus::ACTIVE => 'active',
        PublicAdministrationStatus::SUSPENDED => 'suspended',
    ],

    UserPermission::class => [
        UserPermission::ACCESS_ADMIN_AREA => 'Access to admin area',
        UserPermission::MANAGE_USERS => 'Manage users',
        UserPermission::MANAGE_WEBSITES => 'Manage websites',
        UserPermission::VIEW_LOGS => 'View logs',
        UserPermission::MANAGE_ANALYTICS => 'Manage analytics',
        UserPermission::READ_ANALYTICS => 'Read analytics',
        UserPermission::DO_NOTHING => 'No permissions',
    ],

    UserRole::class => [
        UserRole::SUPER_ADMIN => 'Super admin of ' . config('app.name'),
        UserRole::ADMIN => 'Admin of own Public Administation',
        UserRole::DELEGATED => 'Delegated user',
        UserRole::REGISTERED => 'Registered user',
        UserRole::REMOVED => 'Suspended user',
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

    EventType::class => [
        EventType::EXCEPTION => 'Error',
        EventType::ANALYTICS_LOGIN => 'Analytics Service Login',
        EventType::PENDING_WEBSITES_CHECK_COMPLETED => 'Pending websites check completed',
        EventType::TRACKING_WEBSITES_CHECK_COMPLETED => 'Website tracking check completed',
        EventType::IPA_UPDATE_COMPLETED => 'IPA update completed',
        EventType::PUBLIC_ADMINISTRATION_REGISTERED => 'Public Administration registered',
        EventType::PUBLIC_ADMINISTRATION_ACTIVATED => 'Public Administration activated',
        EventType::PUBLIC_ADMINISTRATION_ACTIVATION_FAILED => 'Public Administration activation failed',
        EventType::PUBLIC_ADMINISTRATION_UPDATED => 'Public Administration updated',
        EventType::PUBLIC_ADMINISTRATION_PRIMARY_WEBSITE_CHANGED => 'Primary website changed',
        EventType::PUBLIC_ADMINISTRATION_PURGED => 'Public Administration removed',
        EventType::USER_LOGIN => 'User logged in',
        EventType::USER_LOGOUT => 'User logged out',
        EventType::USER_REGISTERED => 'User registered',
        EventType::USER_INVITED => 'User invited',
        EventType::USER_VERIFIED => 'User email verified',
        EventType::USER_ACTIVATED => 'User activated',
        EventType::USER_EMAIL_CHANGED => 'User email changed',
        EventType::USER_STATUS_CHANGED => 'User status changed',
        EventType::USER_DELETED => 'User deleted',
        EventType::USER_RESTORED => 'User restored',
        EventType::USER_WEBSITE_ACCESS_CHANGED => 'User access level to website changed',
        EventType::WEBSITE_ADDED => 'Website added',
        EventType::WEBSITE_URL_CHANGED => 'Website URL changed',
        EventType::WEBSITE_ACTIVATED => 'Website activated',
        EventType::WEBSITE_STATUS_CHANGED => 'Website status updated',
        EventType::WEBSITE_ARCHIVING => 'Website scheduled for archiving',
        EventType::WEBSITE_ARCHIVED => 'Website archived',
        EventType::WEBSITE_PURGING => 'Website scheduled for removing',
        EventType::WEBSITE_PURGED => 'Website removed',
        EventType::WEBSITE_DELETED => 'Website manually deleted',
        EventType::WEBSITE_RESTORED => 'Website restored',
        EventType::USERS_INDEXING_COMPLETED => 'Users index update completed',
        EventType::WEBSITES_INDEXING_COMPLETED => 'Websites index update completed',
    ],

    ExceptionType::class => [
        ExceptionType::GENERIC => 'Not specified error',
        ExceptionType::ANALYTICS_ACCOUNT => 'Analytics Service authentication error',
        ExceptionType::ANALYTICS_SERVICE => 'Analytics Service error',
        ExceptionType::ANALYTICS_COMMAND => 'Analytics Service command error',
        ExceptionType::HTTP_CLIENT_ERROR => 'Client http error (4xx)',
        ExceptionType::SERVER_ERROR => 'Internal server error',
        ExceptionType::TENANT_SELECTION => 'Missing P.A. selection error',
        ExceptionType::IPA_INDEX_SEARCH => 'I.P.A. index search error',
        ExceptionType::WEBSITE_INDEX_SEARCH => 'Website index search error',
        ExceptionType::USER_INDEX_SEARCH => 'User index search error',
        ExceptionType::INVALID_WEBSITE_STATUS => 'Invalid website status error',
        ExceptionType::INVALID_OPERATION => 'Invalid operation error',
        ExceptionType::INVALID_USER_STATUS => 'Invalid user status error',
    ],

    JobType::class => [
        JobType::CLEAR_PASSWORD_TOKEN => 'Clear expired password token',
        JobType::UPDATE_IPA => 'Update I.P.A. index',
        JobType::SEND_RESET_PASSWORD_TOKEN => 'Send password reset token',
        JobType::SEND_EMAIL_VERIFICATION_TOKEN => 'Send email verification token',
    ],

];
