<?php

namespace App\Events\Jobs;

/**
 * Monitor websites activity check completed event.
 */
class WebsitesMonitoringCheckCompleted
{
    /**
     * The list of websites that will be archived.
     * The array follows this schema:
     * array[]
     *  ['website'].
     *
     * @var array the near-to-be archived websites list
     */
    protected $archiving;

    /**
     * Archived websites list.
     * The array follows this schema:
     * array[]
     *  ['website'].
     *
     * @var array the archived websites list
     */
    protected $archived;

    /**
     * The list of websites for which the check failed.
     * The array follows this schema:
     * array[]
     *  array[]
     *      ['website']
     *      ['reason'].
     *
     * @var array the failed to check websites list
     */
    protected $failed;

    /**
     * Event constructor.
     *
     * @param array $archived the archived websites array
     * @param array $archiving the near-to-be-archived websites array
     * @param array $failed the check-failed websites array
     */
    public function __construct(array $archived, array $archiving, array $failed)
    {
        $this->archived = $archived;
        $this->archiving = $archiving;
        $this->failed = $failed;
    }

    /**
     * Get the array of archived websites.
     *
     * @return array the websites list
     */
    public function getArchived(): array
    {
        return $this->archived;
    }

    /**
     * Get the array of near-to-be archived websites.
     *
     * @return array the websites list
     */
    public function getArchiving(): array
    {
        return $this->archiving;
    }

    /**
     * Get the array of check-failed websites.
     *
     * @return array the websites list
     */
    public function getFailed(): array
    {
        return $this->failed;
    }
}
