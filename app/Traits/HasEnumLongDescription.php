<?php

namespace App\Traits;

use Illuminate\Support\Facades\Lang;

/**
 * Enum long description support.
 */
trait HasEnumLongDescription
{
    /**
     * Get the description for an enum value.
     *
     * @param mixed $value
     *
     * @return string
     */
    public static function getLongDescription($value): string
    {
        return
            static::getLongLocalizedDescription($value) ??
            static::getFriendlyKeyName(static::getKey($value));
    }

    /**
     * Get the long localized description of a value.
     *
     * This works only if localization is enabled
     * for the enum and if the key exists in the lang file.
     *
     * @param mixed $value
     *
     * @return string|null
     */
    protected static function getLongLocalizedDescription($value): ?string
    {
        if (static::isLocalizable()) {
            $localizedStringKey = static::getLocalizationKey() . '.' . $value . '.long';
            if (Lang::has($localizedStringKey)) {
                return __($localizedStringKey);
            }
        }

        return null;
    }

    /**
     * Get the default localized description of a value.
     *
     * This works only if localization is enabled
     * for the enum and if the key exists in the lang file.
     *
     * @param mixed $value
     *
     * @return string|null
     */
    protected static function getLocalizedDescription($value): ?string
    {
        if (static::isLocalizable()) {
            $localizedStringKey = static::getLocalizationKey() . '.' . $value . '.short';
            if (Lang::has($localizedStringKey)) {
                return __($localizedStringKey);
            }
        }

        return null;
    }
}
