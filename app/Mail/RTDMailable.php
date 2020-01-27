<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * RTD mailable template.
 */
abstract class RTDMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The mail recipient.
     *
     * @var PublicAdministration the public administration
     */
    protected $recipient;

    /**
     * Default constructor.
     *
     * @param PublicAdministration $recipient the mail recipient
     */
    public function __construct(PublicAdministration $recipient)
    {
        $this->recipient = $recipient;
    }
}
