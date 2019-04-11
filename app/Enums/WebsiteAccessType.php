<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * The website access type for the Analytics Service.
 */
final class WebsiteAccessType extends Enum implements LocalizedEnum
{
    /**
     * No access constant.
     */
    public const NO_ACCESS = 0;

    /**
     * Read-only access constant.
     */
    public const VIEW = 1;

    /**
     * Manage analytics access constant.
     */
    public const WRITE = 2;

    /**
     * Website admin access constant.
     */
    public const ADMIN = 3;
}
