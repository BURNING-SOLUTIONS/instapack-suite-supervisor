<?php

namespace App\Utils\Email;

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use \Symfony\Component\Mailer\Mailer;


class SymfonyMailerEmailManager implements EmailSenderInterface
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new Mailer(new GmailSmtpTransport('comercialm@instapack.es', 'ICS28028'));
    }

    public function sendEmail($email): void
    {
        $this->mailer->send($email);
    }


}
