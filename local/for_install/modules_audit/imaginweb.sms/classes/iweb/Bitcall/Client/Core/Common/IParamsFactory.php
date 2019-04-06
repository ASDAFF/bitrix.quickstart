<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.14
  */

namespace Bitcall\Client\Core\Common;


use Bitcall\Client\Models\Requests\IvrCallRequest;
use Bitcall\Client\Models\Requests\IvrTaskRequest;
use Bitcall\Client\Models\Requests\StatusRequest;
use Bitcall\Client\Models\Requests\TextCallRequest;
use Bitcall\Client\Models\Requests\TextTaskRequest;

interface IParamsFactory {

    /**
     * @param TextCallRequest $textCallRequest
     * @return mixed
     */
    public function createText(TextCallRequest $textCallRequest);

    /**
     * @param IvrCallRequest $ivrCallRequest
     * @return mixed
     */
    public function createIvr(IvrCallRequest $ivrCallRequest);

    /**
     * @param TextTaskRequest $textTaskRequest
     * @return mixed
     */
    public function createTextTask(TextTaskRequest $textTaskRequest);

    /**
     * @param IvrTaskRequest $ivrTaskRequest
     * @return mixed
     */
    public function createIvrTask(IvrTaskRequest $ivrTaskRequest);

    /**
     * @param StatusRequest $statusRequest
     * @return mixed
     */
    public function createStatus(StatusRequest $statusRequest);
} 