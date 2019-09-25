<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use Illuminate\Mail\Events\MessageSent;

class LogSentMessage
{
    /**
     * Log the MessageSent event.
     *
     * @param MessageSent $event
     */
    public function handle(MessageSent $event)
    {
        logger()->debug(
            'Mail message with subject "' . $event->message->getSubject() . '" sent to ' . implode(', ', array_keys($event->message->getTo())),
            [
                'event' => EventType::MAIL_SENT,
            ]
        );
    }
}
