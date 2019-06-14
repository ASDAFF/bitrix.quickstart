<?php

namespace Admitad\Api\Exception;

class InvalidSignedRequestException extends Exception
{
    public function __construct($signedRequest)
    {
        parent::__construct(sprintf('Invalid signed request: %s', $signedRequest));
    }
}
