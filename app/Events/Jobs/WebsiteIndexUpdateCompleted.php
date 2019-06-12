<?php

namespace App\Events\Jobs;

/**
 * Websites index update job completed event.
 */
class WebsiteIndexUpdateCompleted
{
    /**
     * The failed websites.
     * The array follow this schema:
     * array[]
     *  ['website'].
     *
     * @var array
     */
    protected $failed;

    /**
     * The inserted websites.
     * The array follow this schema:
     * array[]
     *  ['website'].
     *
     * @var array
     */
    protected $inserted;

    /**
     * Event constructor.
     *
     * @param array $inserted the inserted list
     * @param array $failed the failed list
     */
    public function __construct(array $inserted = [], array $failed = [])
    {
        $this->inserted = $inserted;
        $this->failed = $failed;
    }

    /**
     * Get the array of inserted websites.
     *
     * @return array the websites list
     */
    public function getInserted(): array
    {
        return $this->inserted;
    }

    /**
     * Get the array of failed websites.
     *
     * @return array the websites list
     */
    public function getFailed(): array
    {
        return $this->failed;
    }
}
