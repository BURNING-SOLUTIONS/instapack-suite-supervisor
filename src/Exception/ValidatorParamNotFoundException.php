<?php
// api/src/Exception/ProductNotFoundException.php

namespace App\Exception;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Throwable;

final class ValidatorParamNotFoundException extends ParameterNotFoundException
{


    /**
     * ValidatorParamNotFoundException constructor.
     * @param string $key
     * @param string|null $sourceId
     * @param string|null $sourceKey
     * @param Throwable|null $previous
     * @param array $alternatives
     * @param string|null $nonNestedAlternative
     */
    public function __construct(string $key, string $sourceId = null, string $sourceKey = null, \Throwable $previous = null, array $alternatives = [], string $nonNestedAlternative = null)
    {
        parent::__construct($key, $sourceId, $sourceKey, $previous, $alternatives, $nonNestedAlternative);
    }

}
