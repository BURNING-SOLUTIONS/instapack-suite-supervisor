<?php
// api/src/Exception/ProductNotFoundException.php

namespace App\Exception;
;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Throwable;

final class AppEntityValidationException extends \Exception
{

    /**
     * ApplicationIdNotFoundException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageKey(): string
    {
        return $this->message;
    }
}
