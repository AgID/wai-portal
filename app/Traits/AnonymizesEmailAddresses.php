<?php

namespace App\Traits;

/**
 * Get anonymized email addresses.
 */
trait AnonymizesEmailAddresses
{
    /**
     * Get an anonymized email address from a plain one.
     *
     * @param string $emailAddress the email address to be anonymized
     *
     * @return string the anonymized address
     */
    public function anonymizeEmailAddress(string $emailAddress): string
    {
        return preg_replace('/(?<=.).(?=.*.@)/', '*', $emailAddress);
    }
}
