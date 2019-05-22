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
        PublicAdministrationStatus::PENDING => 'in attesa',
        PublicAdministrationStatus::ACTIVE => 'attiva',
        PublicAdministrationStatus::SUSPENDED => 'sospesa',
    ],

    UserPermission::class => [
        UserPermission::ACCESS_ADMIN_AREA => "Accesso all'area amministrativa",
        UserPermission::MANAGE_USERS => 'Gestione utenti',
        UserPermission::MANAGE_WEBSITES => 'Gestione siti',
        UserPermission::MANAGE_ANALYTICS => 'Gestione analytics',
        UserPermission::READ_ANALYTICS => 'Lettura analytics',
        UserPermission::DO_NOTHING => 'Nessun permesso',
    ],

    UserRole::class => [
        UserRole::SUPER_ADMIN => 'Super amministatore di ' . config('app.name'),
        UserRole::ADMIN => 'Amministarore della propria PA',
        UserRole::DELEGATED => 'Incaricato della propria PA',
        UserRole::REGISTERED => 'Utente registrato',
    ],

    UserStatus::class => [
        UserStatus::INVITED => 'invitato',
        UserStatus::INACTIVE => 'inattivo',
        UserStatus::PENDING => 'in attesa',
        UserStatus::ACTIVE => 'attivo',
        UserStatus::SUSPENDED => 'sospeso',
    ],

    WebsiteStatus::class => [
        WebsiteStatus::PENDING => 'in attesa',
        WebsiteStatus::ACTIVE => 'attivo',
        WebsiteStatus::ARCHIVED => 'archiviato',
    ],

    WebsiteType::class => [
        WebsiteType::PRIMARY => 'sito istituzionale',
        WebsiteType::SECONDARY => 'informativo o tematico',
        WebsiteType::WEBAPP => 'interattivo o web application',
        WebsiteType::TESTING => 'testing o staging',
    ],

    WebsiteAccessType::class => [
        WebsiteAccessType::NO_ACCESS => 'nessun accesso',
        WebsiteAccessType::VIEW => 'sola lettura',
        WebsiteAccessType::WRITE => 'gestione analytics',
        WebsiteAccessType::ADMIN => 'amministratore',
    ],

];
