<?php

namespace App\Enums;

use App\Traits\HasEnumLongDescription;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Credential types.
 */
final class CredentialType extends Enum implements LocalizedEnum
{
    use HasEnumLongDescription;

    /**
     * Admin credential.
     */
    public const ADMIN = 'admin';

    /**
     * Analytics credential.
     */
    public const ANALYTICS = 'analytics';
}
