<?php

namespace App\Utils\Email;

use App\Exception\AppSendEmailException;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use \Symfony\Component\Mailer\Mailer;


class SymfonyMailerEmailManager implements EmailSenderInterface
{
    private $mailer;


    public function __construct()
    {
        $this->mailer = new Mailer(
            new GmailSmtpTransport($_ENV['API_EMAIL_USER'], $_ENV['API_EMAIL_PASSWORD'])
        );
    }

    public function sendEmail($email): void
    {
        try {
            $this->mailer->send($email);
        } catch (\Exception $exception) {
            throw new AppSendEmailException("Ha ocurrido un error al enviar el correo electrónico, puede informar al administrador de este problema");
        }
    }


}
