<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Core\Soap;


use Bitcall\Client\Core\BaseBitcallClient;
use Bitcall\Client\Core\Common\IParamsFactory;
use Bitcall\Client\Core\Common\IResponseFactory;
use Bitcall\Client\Models\Requests\IvrCallRequest;
use Bitcall\Client\Models\Requests\IvrTaskRequest;
use Bitcall\Client\Models\Requests\StatusRequest;
use Bitcall\Client\Models\Requests\TextCallRequest;
use Bitcall\Client\Models\Requests\TextTaskRequest;
use Bitcall\Client\Models\Responses\BaseCallResponse;
use Bitcall\Client\Models\Responses\StatusResponse;
use Bitcall\Client\Models\Responses\TaskResponse;
use Bitcall\Client\Settings\ISettingsManager;
use SoapClient;
use stdClass;

class SoapBitcallClient extends BaseBitcallClient{
    protected $soapClient;
    /**
     * @var ISettingsManager
     */
    private $settingsManager;
    /**
     * @var IParamsFactory
     */
    private $soapParamsFactory;
    /**
     * @var IResponseFactory
     */
    private $soapResponseFactory;

    function __construct($key,
                         ISettingsManager $settingsManager,
                         IParamsFactory $soapParamsFactory,
                         IResponseFactory $soapResponseFactory)
    {
        parent::__construct($key);
        $this->settingsManager = $settingsManager;
        $settings = array();
        if(!$settingsManager->getSoapSettings()->useWsdlCache()){
            $settings['cache_wsdl'] = WSDL_CACHE_NONE;
        } else {
            $settings['cache_wsdl'] = WSDL_CACHE_BOTH;
        }
        $this->soapClient = new SoapClient($settingsManager->getSoapSettings()->getWsdlUrl(), $settings);
        $this->soapParamsFactory = $soapParamsFactory;
        $this->soapResponseFactory = $soapResponseFactory;
    }


    /**
     * @param TextCallRequest $textCallRequest
     * @return BaseCallResponse
     */
    public function text(TextCallRequest $textCallRequest)
    {
        $req = $this->soapParamsFactory->createText($textCallRequest);
        $req->Key = $this->getKey();
        $params = new stdClass();
        $params->request = $req;
        $response = $this->soapClient->Text($params);
        return $this->soapResponseFactory->createText($response);
    }

    /**
     * @param IvrCallRequest $ivrCallRequest
     * @return BaseCallResponse
     */
    public function ivr(IvrCallRequest $ivrCallRequest)
    {
        $req = $this->soapParamsFactory->createIvr($ivrCallRequest);
        $req->Key = $this->getKey();
        $params = new stdClass();
        $params->request = $req;
        $response = $this->soapClient->Ivr($params);
        return $this->soapResponseFactory->createIvr($response);
    }

    /**
     * @param TextCallRequest $textCallRequest
     * @return BaseCallResponse
     */
    public function fastText(TextCallRequest $textCallRequest)
    {
        $req = $this->soapParamsFactory->createText($textCallRequest);
        $req->Key = $this->getKey();
        $params = new stdClass();
        $params->request = $req;
        $response = $this->soapClient->FastText($params);
        return $this->soapResponseFactory->createFastText($response);
    }

    /**
     * @param IvrCallRequest $ivrCallRequest
     * @return BaseCallResponse
     */
    public function fastIvr(IvrCallRequest $ivrCallRequest)
    {
        $req = $this->soapParamsFactory->createIvr($ivrCallRequest);
        $req->Key = $this->getKey();
        $params = new stdClass();
        $params->request = $req;
        $response = $this->soapClient->FastIvr($params);
        return $this->soapResponseFactory->createFastIvr($response);
    }

    /**
     * @param TextTaskRequest $textTaskRequest
     * @return TaskResponse
     */
    public function textTask(TextTaskRequest $textTaskRequest)
    {
        $req = $this->soapParamsFactory->createTextTask($textTaskRequest);
        $req->Key = $this->getKey();
        $params = new stdClass();
        $params->request = $req;
        $response = $this->soapClient->TextTask($params);
        return $this->soapResponseFactory->createTextTask($response);
    }

    /**
     * @param IvrTaskRequest $ivrTaskRequest
     * @return TaskResponse
     */
    public function ivrTask(IvrTaskRequest $ivrTaskRequest)
    {
        $req = $this->soapParamsFactory->createIvrTask($ivrTaskRequest);
        $req->Key = $this->getKey();
        $params = new stdClass();
        $params->request = $req;
        $response = $this->soapClient->IvrTask($params);
        return $this->soapResponseFactory->createIvrTask($response);
    }

    /**
     * @param StatusRequest $statusRequest
     * @return StatusResponse
     */
    public function status(StatusRequest $statusRequest)
    {
        $req = $this->soapParamsFactory->createStatus($statusRequest);
        $req->Key = $this->getKey();
        $params = new stdClass();
        $params->request = $req;
        $response = $this->soapClient->Status($params);
        return $this->soapResponseFactory->createStatus($response);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "SOAP";
    }
}