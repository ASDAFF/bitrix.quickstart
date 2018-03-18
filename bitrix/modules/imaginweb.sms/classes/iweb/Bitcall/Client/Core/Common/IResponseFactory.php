<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.14
  */

namespace Bitcall\Client\Core\Common;


use Bitcall\Client\Models\Responses\BaseCallResponse;
use Bitcall\Client\Models\Responses\StatusResponse;
use Bitcall\Client\Models\Responses\TaskResponse;

interface IResponseFactory {

    /**
     * @param $response
     * @return BaseCallResponse
     */
    public function createText($response);

    /**
     * @param $response
     * @return BaseCallResponse
     */
    public function createFastText($response);

    /**
     * @param $response
     * @return BaseCallResponse
     */
    public function createIvr($response);

    /**
     * @param $response
     * @return BaseCallResponse
     */
    public function createFastIvr($response);

    /**
     * @param $response
     * @return TaskResponse
     */
    public function createTextTask($response);

    /**
     * @param $response
     * @return TaskResponse
     */
    public function createIvrTask($response);

    /**
     * @param $response
     * @return StatusResponse
     */
    public function createStatus($response);
} 