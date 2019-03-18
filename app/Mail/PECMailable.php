<?php

namespace App\Mail;

use Illuminate\Container\Container;
use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Mail\Mailable;
use Swift_Mailer;
use Swift_SmtpTransport;

class PECMailable extends Mailable
{
    /**
     * Override Mailable functionality to support customized mail settings.
     *
     * @param MailerContract $mailer
     *
     * @return void
     */
    public function send(MailerContract $mailer)
    {
        $host = env('PEC_HOST');
        $port = env('PEC_PORT', 587);
        $security = env('PEC_ENCRYPTION', 'tls');
        $username = env('PEC_USERNAME');
        $password = env('PEC_PASSWORD');

        $transport = new Swift_SmtpTransport($host, $port, $security);
        $transport->setUsername($username);
        $transport->setPassword($password);
        $mailer->setSwiftMailer(new Swift_Mailer($transport));

        Container::getInstance()->call([$this, 'build']);
        $mailer->send($this->buildView(), $this->buildViewData(), function ($message) {
            $this->buildFrom($message)
                ->buildRecipients($message)
                ->buildSubject($message)
                ->buildAttachments($message)
                ->runCallbacks($message);
        });
    }
}
