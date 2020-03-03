<?php

namespace App\Enums;

use App\Traits\HasEnumLongDescription;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Website status.
 */
final class WebsiteStatus extends Enum implements LocalizedEnum
{
    use HasEnumLongDescription;

    /**
     * Website status pending constant.
     */
    public const PENDING = 0;

    /**
     * Website status active constant.
     */
    public const ACTIVE = 1;

    /**
     * Website status archived constant.
     */
    public const ARCHIVED = 2;
}
