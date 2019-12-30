<?php

namespace App\Utils\Email;

class AppEmailManager
{
    private $emailSender;

    public function __construct(string $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function getCurrentEmailSender(): EmailSenderInterface
    {
        $class = $this->emailSender;
        return (new $class());
    }


}
