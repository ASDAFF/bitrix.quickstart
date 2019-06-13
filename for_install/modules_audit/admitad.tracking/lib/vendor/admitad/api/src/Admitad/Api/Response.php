<?php

namespace Admitad\Api;

use Admitad\Api\Exception\InvalidResponseException;

class Response extends \Buzz\Message\Response
{
    private $arrayResult;
    private $result;

    public function getResult($field = null)
    {

        if (null === $this->result) {
            $this->result = new Object($this->getArrayResult());
        }

        if (null !== $field) {
            if (null !== $this->result && isset($this->result[$field])) {
                return $this->result[$field];
            }
            return null;
        }

        return $this->result;
    }

    public function getArrayResult($field = null)
    {
        if (null == $this->arrayResult) {
            $this->arrayResult = json_decode($this->getContent(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidResponseException($this->getContent());
            }
        }

        if (null !== $field) {
            if (null !== $this->arrayResult && isset($this->arrayResult[$field])) {
                return $this->arrayResult[$field];
            }
            return null;
        }

        return $this->arrayResult;
    }

    public function getError()
    {
        return $this->getResult('error');
    }

    public function getErrorDescription()
    {
        return $this->getResult('error_description');
    }

    public function getErrorCode()
    {
        return $this->getResult('error_code');
    }
}
