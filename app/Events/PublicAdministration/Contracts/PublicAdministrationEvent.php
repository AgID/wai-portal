<?php

namespace App\Events\PublicAdministration\Contracts;

use App\Models\PublicAdministration;
use Illuminate\Queue\SerializesModels;

abstract class PublicAdministrationEvent
{
    use SerializesModels;

    /**
     * The changed public administration.
     *
     * @var PublicAdministration the public administration reference
     */
    protected $publicAdministration;

    /**
     * PublicAdministrationEvent constructor.
     *
     * @param PublicAdministration $publicAdministration
     */
    public function __construct(PublicAdministration $publicAdministration)
    {
        $this->publicAdministration = $publicAdministration;
    }

    /**
     * Get the changed Public Administration.
     *
     * @return PublicAdministration
     */
    public function getPublicAdministration(): PublicAdministration
    {
        return $this->publicAdministration;
    }
}
