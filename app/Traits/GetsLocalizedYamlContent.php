<?php

namespace App\Traits;

use Symfony\Component\Yaml\Yaml;

/**
 * Get localized content from yaml files in resources/data directory.
 */
trait GetsLocalizedYamlContent
{
    /**
     * Get localized content from a specified yaml file in resources/data directory.
     *
     * @param string $yaml the name of the yaml file to use
     *
     * @return mixed the localized content
     */
    public function getLocalizedYamlContent(string $yaml)
    {
        $allContent = Yaml::parseFile(resource_path("data/$yaml.yml"));
        $currentLocale = app()->getLocale();
        $contentLocale = array_key_exists($currentLocale, $allContent) ? $currentLocale : config('app.fallback_locale');

        return $allContent[$contentLocale];
    }
}
