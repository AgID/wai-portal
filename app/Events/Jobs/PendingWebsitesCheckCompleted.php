<?php

namespace App\Events\Jobs;

class PendingWebsitesCheckCompleted
{
    protected $activated;

    protected $purged;

    protected $purging;

    protected $failed;

    public function __construct(array $activated = [], array $purging = [], array $purged = [], $failed = [])
    {
        $this->activated = $activated;
        $this->purging = $purging;
        $this->purged = $purged;
        $this->failed = $failed;
    }

    /**
     * @return array
     */
    public function getActivated(): array
    {
        return $this->activated;
    }

    /**
     * @return array
     */
    public function getPurging(): array
    {
        return $this->purging;
    }

    /**
     * @return array
     */
    public function getPurged(): array
    {
        return $this->purged;
    }

    /**
     * @return mixed
     */
    public function getFailed()
    {
        return $this->failed;
    }
}
