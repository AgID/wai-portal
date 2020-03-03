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
     * Institutional Public Administration site.
     */
    public const INSTITUTIONAL = 0;

    /**
     * Informational Public Administration site.
     */
    public const INFORMATIONAL = 1;

    /**
     * Digital Services Public Administration site.
     */
    public const SERVICE = 2;

    /**
     * Mobile application.
     */
    public const MOBILE = 3;
}
