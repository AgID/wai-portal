<?php

namespace App\Events\Jobs;

/**
 * Pending website check completed event.
 */
class PendingWebsitesCheckCompleted
{
    /**
     * Activated websites list.
     * The array follows this schema:
     * array[]
     *  ['website'].
     *
     * @var array the activated websites list
     */
    protected $activated;

    /**
     * Purged websites list.
     * The array follows this schema:
     * array[]
     *  array[]
     *      ['website'].
     *
     * @var array the purged websites list
     */
    protected $purged;

    /**
     * .The list of websites that will be purged in the next days.
     * The array follows this schema:
     * array[]
     *  array[]
     *      ['website'].
     *
     * @var array the near-to-be-purged websites list
     */
    protected $purging;

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
     * @param array $activated the activated websites array
     * @param array $purging the near-to-be-purged websites array
     * @param array $purged the purged websites array
     * @param array $failed the check-failed websites array
     */
    public function __construct(array $activated = [], array $purging = [], array $purged = [], $failed = [])
    {
        $this->activated = $activated;
        $this->purging = $purging;
        $this->purged = $purged;
        $this->failed = $failed;
    }

    /**
     * Get the array of activated websites.
     *
     * @return array the websites list
     */
    public function getActivated(): array
    {
        return $this->activated;
    }

    /**
     * Get the array of near-to-be-purged websites.
     *
     * @return array the websites list
     */
    public function getPurging(): array
    {
        return $this->purging;
    }

    /**
     * Get the array of purged websites.
     *
     * @return array the websites list
     */
    public function getPurged(): array
    {
        return $this->purged;
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
