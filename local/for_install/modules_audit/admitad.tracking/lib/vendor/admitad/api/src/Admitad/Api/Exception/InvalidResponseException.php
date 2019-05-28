<?php

namespace Admitad\Api\Exception;

class InvalidResponseException extends Exception
{
    public function __construct($content)
    {
        parent::__construct(sprintf('Invalid json string: %s', $content));
    }
}
