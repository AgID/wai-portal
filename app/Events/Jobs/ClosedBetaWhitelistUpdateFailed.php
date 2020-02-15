<?php

namespace App\Events\Jobs;

/**
 * Closed beta whitelist update failed event.
 */
class ClosedBetaWhitelistUpdateFailed
{
    /**
     * The HTTP status code.
     *
     * @var int the code
     */
    private $code;

    /**
     * The server message.
     *
     * @var string the message
     */
    private $phrase;

    /**
     * Default constructor.
     *
     * @param int $code the status code
     * @param string $phrase the status message
     */
    public function __construct(int $code, string $phrase)
    {
        $this->code = $code;
        $this->phrase = $phrase;
    }

    /**
     * Get the HTTP status code.
     *
     * @return int the code
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Get the HTTP message.
     *
     * @return string the message
     */
    public function getPhrase(): string
    {
        return $this->phrase;
    }
}
