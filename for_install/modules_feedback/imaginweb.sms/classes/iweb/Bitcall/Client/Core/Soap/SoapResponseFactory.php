<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Core\Soap;


use Bitcall\Client\Core\Common\BaseResponseFactory;
use Bitcall\Client\Core\Common\IResponseFactory;
use Bitcall\Client\Models\Responses\BaseCallResponse;
use Bitcall\Client\Models\Responses\StatusResponse;
use Bitcall\Client\Models\Responses\TaskResponse;

class SoapResponseFactory extends  BaseResponseFactory implements IResponseFactory
{

    private static $instance;
    private function __construct()
    {
    }
    private function __clone()
    {
    }


    /**
     * @return SoapResponseFactory
     */
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
        return $this->mapBase($response->TextResult);
    }

    /**
     * @param $response
     * @return BaseCallResponse
     */
    public function createFastText($response)
    {
        return $this->mapBase($response->FastTextResult);
    }

    /**
     * @param $response
     * @return BaseCallResponse
     */
    public function createIvr($response)
    {
        return $this->mapBase($response->IvrResult);
    }

    /**
     * @param $response
     * @return BaseCallResponse
     */
    public function createFastIvr($response)
    {
        return $this->mapBase($response->FastIvrResult);
    }

    /**
     * @param $response
     * @return TaskResponse
     */
    public function createTextTask($response)
    {
        return $this->mapBaseTask($response->TextTaskResult, $response->TextTaskResult->CallResponses->BaseCallResponse);
    }

    /**
     * @param $response
     * @return TaskResponse
     */
    public function createIvrTask($response)
    {
        return $this->mapBaseTask($response->IvrTaskResult, $response->IvrTaskResult->CallResponses->BaseCallResponse);
    }

    public function createStatus($response)
    {
        return parent::createStatus($response->StatusResult);
    }


}