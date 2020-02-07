<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Ing. Juan Ramón Borges de León
 */
class ObjectNotFoundException extends NotFoundHttpException
{
    private $model;

    public function __construct(string $model, string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        $this->model = $model;
        parent::__construct("The " . $model . " with provide data does not exist", $previous, $code, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageKey(): string
    {
        return $this->message;
    }


}