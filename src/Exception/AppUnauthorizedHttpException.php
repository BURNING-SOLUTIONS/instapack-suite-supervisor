<?php
// api/src/Exception/ProductNotFoundException.php

namespace App\Exception;
;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Throwable;

final class AppUnauthorizedHttpException extends UnauthorizedHttpException
{

    public function __construct(string $challenge, string $message = null, \Throwable $previous = null, ?int $code = 0, array $headers = [])
    {
        parent::__construct($challenge, $message, $previous, $code, $headers);
    }


    /**
     * {@inheritdoc}
     */
    public function getMessageKey(): string
    {
        return $this->message;
    }
}
