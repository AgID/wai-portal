<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;

/**
 * Mail sent events listener.
 */
class LogSentMessage implements ShouldQueue
{
    /**
     * Log the MessageSent event.
     *
     * @param MessageSent $event
     */
    public function handle(MessageSent $event): void
    {
        $anonymizedMailAddresses = collect($event->message->getTo())->keys()->map(function ($address) {
            return preg_replace('/(?<=.).(?=.*.@)/', '*', $address);
        })->toArray();

        logger()->debug(
            'Mail message with subject "' . $event->message->getSubject() . '" sent to ' . implode(', ', $anonymizedMailAddresses),
            [
                'event' => EventType::MAIL_SENT,
            ]
        );
    }
}
