<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Core\Common;


use Bitcall\Client\Models\Common\IvrCallParameter;
use Bitcall\Client\Models\Requests\BaseCallRequest;
use Bitcall\Client\Models\Requests\BaseRequest;
use Bitcall\Client\Models\Requests\IvrCallRequest;
use Bitcall\Client\Models\Requests\IvrTaskRequest;
use Bitcall\Client\Models\Requests\StatusRequest;
use Bitcall\Client\Models\Requests\TaskCall;
use Bitcall\Client\Models\Requests\TaskRequest;
use Bitcall\Client\Models\Requests\TextCallRequest;
use Bitcall\Client\Models\Requests\TextTaskRequest;
use stdClass;

class ParamsFactory implements IParamsFactory
{
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

    public function createText(TextCallRequest $textCallRequest)
    {
        $req = $this->createBaseCallRequest($textCallRequest);
        $req->Text = $textCallRequest->getText();
        return $req;
    }

    public function createIvr(IvrCallRequest $ivrCallRequest)
    {
        $req = $this->createBaseCallRequest($ivrCallRequest);
        $req->Context = array();
        foreach ($ivrCallRequest->getContext() as $ivrParameter) {
            $req->Context[] = $this->createIvrParam($ivrParameter);
        }
        return $req;
    }

    public function createTextTask(TextTaskRequest $textTaskRequest)
    {
        $task = $this->createTask($textTaskRequest);
        $task->TaskCalls = array();
        foreach ($textTaskRequest->getTaskCalls() as $taskCall) {
            $reqTaskCall = $this->createTaskCall($taskCall);
            $reqTaskCall->Text = $taskCall->getText();
            $task->TaskCalls[] = $reqTaskCall;
        }
        return $task;
    }

    public function createIvrTask(IvrTaskRequest $ivrTaskRequest)
    {
        $task = $this->createTask($ivrTaskRequest);
        $task->TaskCalls = array();
        foreach ($ivrTaskRequest->getTaskCalls() as $taskCall) {
            $reqTaskCall = $this->createTaskCall($taskCall);
            $reqTaskCall->Context = array();
            foreach ( $taskCall->getContext() as $ivrCallParameter) {
                $reqTaskCall->Context[] = $this->createIvrParam($ivrCallParameter);
            }
            $task->TaskCalls[] = $reqTaskCall;
        }
        return $task;
    }

    public function createStatus(StatusRequest $statusRequest)
    {
        $request = $this->createBaseRequest($statusRequest);
        $request->IsOtherSystemId = $statusRequest->isOtherSystemId();
        return $request;
    }

    private function createIvrParam(IvrCallParameter $ivrCallParameter)
    {
        $reqParam = new stdClass();
        $reqParam->Operator = $ivrCallParameter->getIvrOperator();
        $reqParam->Parameters = $ivrCallParameter->getParameters();
        return $reqParam;
    }

    private function createTaskCall(TaskCall $taskCall)
    {
        $resTaskCall = new stdClass();
        $resTaskCall->Phone = $taskCall->getPhone();
        $resTaskCall->Id = $taskCall->getId();
        return $resTaskCall;
    }

    private function createBaseRequest(BaseRequest $baseRequest)
    {
        $request = new stdClass();
        $request->Id = $baseRequest->getId();
        return $request;
    }

    private function createBaseCallRequest(BaseCallRequest $baseCallRequest)
    {
        $request = $this->createBaseRequest($baseCallRequest);
        $request->Phone = $baseCallRequest->getPhone();
        $request->CallerPhone = $baseCallRequest->getCallerPhone();
        return $request;
    }

    private function createTask(TaskRequest $taskRequest)
    {
        $request = new stdClass();
        $request->MaxRetries = $taskRequest->getMaxRetries();
        $request->RetryTime = $taskRequest->getRetryTime();
        $request->WaitTime = $taskRequest->getWaitTime();
        $request->SendDate = $taskRequest->getSendDate();
        $request->CallerPhone = $taskRequest->getCallerPhone();
        $request->TaskName = $taskRequest->getTaskName();
        return $request;
    }
}