<?php

namespace App\Enums\Logs;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class ExceptionType extends Enum implements LocalizedEnum
{
    public const GENERIC = 0;

    public const ANALYTICS_ACCOUNT = 1;

    public const ANALYTICS_SERVICE = 2;

    public const ANALYTICS_COMMAND = 3;

    public const UNAUTHORIZED_ACCESS = 4;

    public const TENANT_SELECTION = 5;

    public const IPA_INDEX_SEARCH = 6;

    public const WEBSITE_INDEX_SEARCH = 7;

    public const USER_INDEX_SEARCH = 8;

    public const INVALID_WEBSITE_STATUS = 9;

    public const INVALID_OPERATION = 10;
}
