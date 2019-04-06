<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class WebsiteAccessType extends Enum implements LocalizedEnum
{
    public const NO_ACCESS = 'noaccess';

    public const VIEW = 'view';

    public const WRITE = 'write';

    public const ADMIN = 'admin';
}
