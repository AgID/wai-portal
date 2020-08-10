<?php

namespace App\Events\Jobs;

class EnvironmentResetCompleted
{
    protected $completed;

    protected $failed;

    public function __construct(array $completed = [], array $failed = [])
    {
        $this->completed = $completed;
        $this->failed = $failed;
    }

    public function getCompleted(): array
    {
        return $this->completed;
    }

    public function getFailed(): array
    {
        return $this->failed;
    }
}
