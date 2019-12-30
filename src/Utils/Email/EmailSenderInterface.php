<?php

namespace App\Utils\Email;

interface EmailSenderInterface
{

    public function sendEmail($email): void;

}