<?php

namespace App\Events\Jobs;

/**
 * IPA update job failed event.
 */
class IPAUpdateFailed
{
    /**
     * The fail reason.
     *
     * @var string the message
     */
    protected $message;

    /**
     * Event constructor.
     *
     * @param string $message the error message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Get the fail reason.
     *
     * @return string the error message
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
