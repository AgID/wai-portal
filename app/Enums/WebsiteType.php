<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Website types.
 */
final class WebsiteType extends Enum implements LocalizedEnum
{
    /**
     * Primary Public Administration site.
     */
    public const PRIMARY = 0;

    /**
     * Generic addition site.
     */
    public const SECONDARY = 1;

    /**
     * Web application site.
     */
    public const WEBAPP = 2;

    /**
     * Testing site.
     */
    public const TESTING = 3;
}
