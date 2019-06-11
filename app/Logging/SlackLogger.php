<?php

namespace App\Logging;

use Illuminate\Log\Logger;
use Monolog\Handler\SlackWebhookHandler;

/**
 * Slack channel logger.
 */
class SlackLogger
{
    /**
     * Customize the logger instance.
     *
     * @param \Illuminate\Log\Logger $logger the logger
     */
    public function __invoke(Logger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof SlackWebhookHandler) {
                $handler->pushProcessor(new SlackProcessor());
            }
        }
    }
}
