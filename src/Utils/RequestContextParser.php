<?php

namespace App\Utils;

use Symfony\Component\Serializer\Encoder\JsonDecode;
use \Symfony\Component\HttpFoundation\RequestStack;

class RequestContextParser
{

    private $decoder;
    private $request;

    public function __construct(RequestStack $request)
    {
        $this->decoder = new JsonDecode();
        $this->request = $request;

    }

    /**
     * @return array
     */
    protected function parseRequestContent(): array
    {
        return get_object_vars($this->decoder->decode($this->getRequestContext(), 'array'));
    }

    /**
     * @return string
     */
    public function getRequestContext(): string
    {
        return $this->request->getCurrentRequest()->getContent();
    }


    /**
     * @param $key
     * @return mixed
     */
    public function getRequestValue(string $key)
    {
        $value = '';
        $arrContext = $this->parseRequestContent();

        if (array_key_exists($key, $arrContext)) {
            $value = $arrContext[$key];
        }
        return $value;
    }

}
