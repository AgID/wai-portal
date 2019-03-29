<?php

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;

return[

    PublicAdministrationStatus::class => [
        PublicAdministrationStatus::PENDING => 'pending',
        PublicAdministrationStatus::ACTIVE => 'active',
        PublicAdministrationStatus::SUSPENDED => 'suspended',
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
        WebsiteType::TESTING => 'testing o staging',
    ],

];
