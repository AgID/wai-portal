<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class RTDMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $recipient;

    public function __construct(PublicAdministration $recipient)
    {
        $this->recipient = $recipient;
    }
}
