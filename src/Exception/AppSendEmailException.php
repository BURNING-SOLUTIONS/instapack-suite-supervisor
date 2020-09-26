<?php


namespace App\Exception;

use Throwable;

final class AppSendEmailException extends \Exception
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