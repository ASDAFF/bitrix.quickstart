<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.14
  */

namespace Bitcall\Client\Core;
use Bitcall\Client\Models\Requests\TextTaskRequest;
use Bitcall\Client\Models\Requests\IvrTaskRequest;
use Bitcall\Client\Models\Requests\TextCallRequest;
use Bitcall\Client\Models\Requests\IvrCallRequest;
use Bitcall\Client\Models\Requests\StatusRequest;
use Bitcall\Client\Models\Responses\BaseCallResponse;
use Bitcall\Client\Models\Responses\StatusResponse;
use Bitcall\Client\Models\Responses\TaskResponse;

/**
 * Interface IBitcallClient
 * @package Bitcall\Client\Core
 */
interface IBitcallClient {

    /**
     * @param TextCallRequest $textCallRequest
     * @return BaseCallResponse
     */
    public function text(TextCallRequest $textCallRequest);

    /**
     * @param IvrCallRequest $ivrCallRequest
     * @return BaseCallResponse
     */
    public function ivr(IvrCallRequest $ivrCallRequest);

    /**
     * @param TextCallRequest $textCallRequest
     * @return BaseCallResponse
     */
    public function fastText(TextCallRequest $textCallRequest);

    /**
     * @param IvrCallRequest $ivrCallRequest
     * @return BaseCallResponse
     */
    public function fastIvr(IvrCallRequest $ivrCallRequest);

    /**
     * @param TextTaskRequest $textTaskRequest
     * @return TaskResponse
     */
    public function textTask(TextTaskRequest $textTaskRequest);

    /**
     * @param IvrTaskRequest $ivrTaskRequest
     * @return TaskResponse
     */
    public function ivrTask(IvrTaskRequest $ivrTaskRequest);

    /**
     * @param StatusRequest $statusRequest
     * @return StatusResponse
     */
    public function status(StatusRequest $statusRequest);


    /**
     * @return string
     */
    public function getName();
}