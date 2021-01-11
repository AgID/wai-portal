<?php

namespace App\Traits;

/**
 * Parse URLs.
 */
trait ParseUrls
{
    /**
     * Return the FQDN of the provided URL.
     *
     * @param string $url the url
     *
     * @return string the FQDN
     */
    public function getFqdnFromUrl(string $url): string
    {
        $urlParts = parse_url($url);

        return $urlParts['scheme'] . '://' . $urlParts['host'];
    }
}
