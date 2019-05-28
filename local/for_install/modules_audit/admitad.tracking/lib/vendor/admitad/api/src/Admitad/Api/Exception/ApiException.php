<?php

namespace Admitad\Api\Exception;

use Admitad\Api\Request;
use Admitad\Api\Response;

class ApiException extends Exception
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Request
     */
    protected $request;

    public function __construct($message, Request $request = null, Response $response = null)
    {
        parent::__construct($message);
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}
