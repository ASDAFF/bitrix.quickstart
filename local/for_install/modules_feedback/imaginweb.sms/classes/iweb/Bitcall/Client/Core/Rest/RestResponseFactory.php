<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Core\Rest;


use Bitcall\Client\Core\Common\BaseResponseFactory;
use Bitcall\Client\Core\Common\IResponseFactory;
use Bitcall\Client\Models\Responses\BaseCallResponse;
use Bitcall\Client\Models\Responses\StatusResponse;
use Bitcall\Client\Models\Responses\TaskResponse;

class RestResponseFactory extends  BaseResponseFactory implements IResponseFactory {

    private static $instance;
    private function __construct()
    {
    }
    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $response
     * @return BaseCallResponse
     */
    public function createText($response)
    {
        return $this->mapBase($response);
    }

    /**
     * @param $response
     * @return BaseCallResponse
     */
    public function createFastText($response)
    {
        return $this->mapBase($response);
    }

    /**
     * @param $response
     * @return BaseCallResponse
     */
    public function createIvr($response)
    {
        return $this->mapBase($response);
    }

    /**
     * @param $response
     * @return BaseCallResponse
     */
    public function createFastIvr($response)
    {
        return $this->mapBase($response);
    }

    /**
     * @param $response
     * @return TaskResponse
     */
    public function createTextTask($response)
    {
        return $this->mapBaseTask($response, $response->CallResponses);
    }

    /**
     * @param $response
     * @return TaskResponse
     */
    public function createIvrTask($response)
    {
        return $this->mapBaseTask($response, $response->CallResponses);
    }
}