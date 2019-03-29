<?php

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;

return[

    PublicAdministrationStatus::class => [
        PublicAdministrationStatus::PENDING => 'in attesa',
        PublicAdministrationStatus::ACTIVE => 'attiva',
        PublicAdministrationStatus::SUSPENDED => 'sospesa',
    ],

    UserStatus::class => [
        UserStatus::INVITED => 'invitato',
        UserStatus::INACTIVE => 'inattivo',
        UserStatus::PENDING => 'in attesa',
        UserStatus::ACTIVE => 'attivo',
        UserStatus::SUSPENDED => 'sospsso',
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

];
