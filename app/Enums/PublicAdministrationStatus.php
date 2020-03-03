<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Public Administrations status.
 */
final class PublicAdministrationStatus extends Enum implements LocalizedEnum
{
    /**
     * Public Administration pending status constant.
     */
    public const PENDING = 0;

    /**
     * Public Administration active status constant.
     */
    public const ACTIVE = 1;

    /**
     * Public Administration archived status constant.
     */
    public const SUSPENDED = 2;
}
