<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Core\Rest;


use Bitcall\Client\Core\BaseBitcallClient;
use Bitcall\Client\Core\Common\IParamsFactory;
use Bitcall\Client\Core\Common\IResponseFactory;
use Bitcall\Client\Models\Requests\IvrCallRequest;
use Bitcall\Client\Models\Requests\IvrTaskRequest;
use Bitcall\Client\Models\Requests\StatusRequest;
use Bitcall\Client\Models\Requests\TextCallRequest;
use Bitcall\Client\Models\Requests\TextTaskRequest;
use Bitcall\Client\Settings\ISettingsManager;

class RestBitcallClient extends BaseBitcallClient
{
    /**
     * @var \Bitcall\Client\Settings\ISettingsManager
     */
    private $settingsManager;
    /**
     * @var \Bitcall\Client\Core\Common\IParamsFactory
     */
    private $paramsFactory;
    /**
     * @var \Bitcall\Client\Core\Common\IResponseFactory
     */
    private $responseFactory;

    /**
     * @param $key
     * @param ISettingsManager $settingsManager
     * @param \Bitcall\Client\Core\Common\IParamsFactory $paramsFactory
     * @param \Bitcall\Client\Core\Common\IResponseFactory $responseFactory
     */
    function __construct($key, ISettingsManager $settingsManager, IParamsFactory $paramsFactory, IResponseFactory $responseFactory)
    {
        parent::__construct($key);
        $this->settingsManager = $settingsManager;
        $this->paramsFactory = $paramsFactory;
        $this->responseFactory = $responseFactory;
    }


    public function text(TextCallRequest $textCallRequest)
    {
        $request = $this->paramsFactory->createText($textCallRequest);
        $request->Key = $this->getKey();
        $json = json_encode($request);
        $url = $this->settingsManager->getRestSettings()->getText();
        $res = $this->exec($url, $json);
        return $this->responseFactory->createText($res);
    }

    /**
     * @param IvrCallRequest $ivrCallRequest
     * @return mixed
     */
    public function ivr(IvrCallRequest $ivrCallRequest)
    {
        $request = $this->paramsFactory->createIvr($ivrCallRequest);
        $request->Key = $this->getKey();
        $json = json_encode($request);
        $url = $this->settingsManager->getRestSettings()->getIvr();
        $res = $this->exec($url, $json);
        return $this->responseFactory->createIvr($res);
    }

    /**
     * @param TextCallRequest $textCallRequest
     * @return mixed
     */
    public function fastText(TextCallRequest $textCallRequest)
    {
        $request = $this->paramsFactory->createText($textCallRequest);
        $request->Key = $this->getKey();
        $json = json_encode($request);
        $url = $this->settingsManager->getRestSettings()->getFastText();
        $res = $this->exec($url, $json);
        return $this->responseFactory->createFastText($res);
    }

    /**
     * @param IvrCallRequest $ivrCallRequest
     * @return mixed
     */
    public function fastIvr(IvrCallRequest $ivrCallRequest)
    {
        $request = $this->paramsFactory->createIvr($ivrCallRequest);
        $request->Key = $this->getKey();
        $json = json_encode($request);
        $url = $this->settingsManager->getRestSettings()->getFastIvr();
        $res = $this->exec($url, $json);
        return $this->responseFactory->createFastIvr($res);
    }

    /**
     * @param TextTaskRequest $textTaskRequest
     * @return mixed
     */
    public function textTask(TextTaskRequest $textTaskRequest)
    {
        $request = $this->paramsFactory->createTextTask($textTaskRequest);
        $request->Key = $this->getKey();
        $json = json_encode($request);
        $url = $this->settingsManager->getRestSettings()->getTextTask();
        $res = $this->exec($url, $json);
        return $this->responseFactory->createTextTask($res);
    }

    /**
     * @param IvrTaskRequest $ivrTaskRequest
     * @return mixed
     */
    public function ivrTask(IvrTaskRequest $ivrTaskRequest)
    {
        $request = $this->paramsFactory->createIvrTask($ivrTaskRequest);
        $request->Key = $this->getKey();
        $json = json_encode($request);
        $url = $this->settingsManager->getRestSettings()->getIvrTask();
        $res = $this->exec($url, $json);
        return $this->responseFactory->createIvrTask($res);
    }

    /**
     * @param StatusRequest $statusRequest
     * @return mixed
     */
    public function status(StatusRequest $statusRequest)
    {
        $request = $this->paramsFactory->createStatus($statusRequest);
        $request->Key = $this->getKey();
        $json = json_encode($request);
        $url = $this->settingsManager->getRestSettings()->getStatus();
        $res = $this->exec($url, $json);
        return $this->responseFactory->createStatus($res);
    }

    private function exec($url, $json)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $res = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($res);
        return $decoded;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "REST";
    }
}