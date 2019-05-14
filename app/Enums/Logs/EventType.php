<?php

namespace App\Enums\Logs;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class EventType extends Enum implements LocalizedEnum
{
    public const EXCEPTION = 0;

    public const ANALYTICS_LOGIN = 1;

    public const PENDING_WEBSITES_CHECK_COMPLETED = 2;

    public const TRACKING_WEBSITES_CHECK_COMPLETED = 3;

    public const IPA_UPDATE_COMPLETED = 4;

    public const PUBLIC_ADMINISTRATION_REGISTERED = 5;

    public const PUBLIC_ADMINISTRATION_ACTIVATED = 6;

    public const PUBLIC_ADMINISTRATION_ACTIVATION_FAILED = 7;

    public const PUBLIC_ADMINISTRATION_UPDATED = 8;

    public const PUBLIC_ADMINISTRATION_PRIMARY_WEBSITE_CHANGED = 9;

    public const PUBLIC_ADMINISTRATION_PURGED = 10;

    public const USER_SPID_LOGIN = 11;

    public const USER_SPID_LOGOUT = 12;

    public const USER_REGISTERED = 13;

    public const USER_INVITED = 14;

    public const USER_VERIFIED = 15;

    public const USER_ACTIVATED = 16;

    public const USER_WEBSITE_ACCESS_CHANGED = 17;

    public const WEBSITE_ADDED = 18;

    public const WEBSITE_ACTIVATED = 19;

    public const WEBSITE_ARCHIVING = 20;

    public const WEBSITE_ARCHIVED = 21;

    public const WEBSITE_UNARCHIVED = 22;

    public const WEBSITE_PURGING = 23;

    public const WEBSITE_PURGED = 24;
}
