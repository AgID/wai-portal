<?php

namespace App\Events\Jobs;

/**
 * Users index update job completed event.
 */
class UserIndexUpdateCompleted
{
    /**
     * The failed users.
     * The array follow this schema:
     * array[]
     *  ['user'].
     *
     * @var array
     */
    protected $failed;

    /**
     * The inserted users.
     * The array follow this schema:
     * array[]
     *  ['user'].
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
     * Get the array of inserted users.
     *
     * @return array the users list
     */
    public function getInserted(): array
    {
        return $this->inserted;
    }

    /**
     * Get the array of failed users.
     *
     * @return array the users list
     */
    public function getFailed(): array
    {
        return $this->failed;
    }
}
