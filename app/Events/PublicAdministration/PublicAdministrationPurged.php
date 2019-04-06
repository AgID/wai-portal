<?php

namespace App\Events\PublicAdministration;

class PublicAdministrationPurged
{
    protected $publicAdministration;

    public function __construct(string $publicAdministration)
    {
        $this->publicAdministration = $publicAdministration;
    }

    /**
     * @return string
     */
    public function getPublicAdministration(): string
    {
        return $this->publicAdministration;
    }
}
