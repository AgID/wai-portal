<?php

namespace Tests\Feature;

use App\Enums\Logs\EventType;
use Illuminate\Mail\Events\MessageSent;
use Tests\TestCase;

class LogSentMessageTest extends TestCase
{
    public function testLogSentMessage(): void
    {
        $message = (new \Swift_Message('Test subject'))->setTo('fake@example.local', 'Fake receiver');

        $this->expectLogMessage('debug', [
            'Mail message with subject "' . $message->getSubject() . '" sent to ' . implode(', ', array_keys($message->getTo())),
            ['event' => EventType::MAIL_SENT],
        ]);

        event(new MessageSent($message));
    }
}
