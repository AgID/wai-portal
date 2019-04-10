<?php

namespace App\Events\Jobs;

class WebsitesMonitoringCheckCompleted
{
    /**
     * @var array
     */
    protected $archiving;

    /**
     * @var array
     */
    protected $archived;

    /**
     * @var array
     */
    protected $failed;

    /**
     * WebsitesMonitoringCheckCompleted constructor.
     *
     * @param array $archived
     * @param array $archiving
     * @param array $failed
     */
    public function __construct(array $archived, array $archiving, array $failed)
    {
        $this->archived = $archived;
        $this->archiving = $archiving;
        $this->failed = $failed;
    }

    /**
     * @return array
     */
    public function getArchived(): array
    {
        return $this->archived;
    }

    /**
     * @return array
     */
    public function getArchiving(): array
    {
        return $this->archiving;
    }

    /**
     * @return array
     */
    public function getFailed(): array
    {
        return $this->failed;
    }
}
