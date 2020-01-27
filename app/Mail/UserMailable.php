<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class UserMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $recipient;

    public function __construct(User $recipient)
    {
        $this->recipient = $recipient;
    }
}
