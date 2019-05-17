<?php

namespace App\Events\Jobs;

class WebsiteIndexUpdateCompleted
{
    protected $failed;

    protected $inserted;

    public function __construct(array $inserted = [], array $failed = [])
    {
        $this->inserted = $inserted;
        $this->failed = $failed;
    }

    /**
     * @return array
     */
    public function getInserted(): array
    {
        return $this->inserted;
    }

    /**
     * @return array
     */
    public function getFailed(): array
    {
        return $this->failed;
    }
}
