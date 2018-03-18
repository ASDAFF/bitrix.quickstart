<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Core\Common;


use Bitcall\Client\Models\Common\ResponseError;
use Bitcall\Client\Models\Responses\BaseCallResponse;
use Bitcall\Client\Models\Responses\StatusResponse;
use Bitcall\Client\Models\Responses\TaskResponse;

abstract class BaseResponseFactory implements IResponseFactory {
    /**
     * @param $error
     * @return ResponseError
     */
    protected function mapError($error)
    {
        //$errorCode, $errorId, $errorMessage, $errorString
        if($error === null){
            return null;
        }
        return new ResponseError($error->ErrorCode, $error->ErrorId, $error->ErrorMessage, $error->ErrorString);
    }

    /**
     * @param $base
     * @return BaseCallResponse
     */
    protected function mapBase($base)
    {
        $error = $this->mapError($base->Error);
        return new BaseCallResponse($base->Id, $error, $base->HasError);
    }


    /**
     * @param $baseTask
     * @param $callResponses
     * @return TaskResponse
     */
    protected function mapBaseTask($baseTask, $callResponses)
    {
        $error = $this->mapError($baseTask->Error);
        if(!is_array($callResponses)){
            $callResponses = array($callResponses);
        }
        $resCallResponses = array();
        for ($i=0; $i < count($callResponses); $i++) {
            $resCallResponses[] = $this->mapBase($callResponses[$i]);
        }
        if(count($resCallResponses) === 0){
            $res = null;
        } else {
            $res = $resCallResponses;
        }
        return new TaskResponse($res, $baseTask->Id, $error, $baseTask->HasError);
    }

    /**
     * @param $response
     * @return StatusResponse
     */
    public function createStatus($response)
    {
        $digits = $response->Digits;
        $duration = $response->Duration;
        $price = $response->Price;
        $status = $response->Status;
        $statusString = $response->StatusString;
        $id = $response->Id;
        $error = $this->mapError($response->Error);
        $hasError = $response->HasError;
        return new StatusResponse($digits, $duration, $price, $status, $statusString, $id, $error, $hasError);
    }
} 