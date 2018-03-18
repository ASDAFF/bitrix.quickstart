<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Devinotele{

	private $config;
	public $session = false;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		return array();
	}
	
	public function _getBalance () {
		
		$data = new \stdClass();
		
		$obAutparam = $this->getSessionKey();
		if($obAutparam->error_code) return $obAutparam;
		
		$url = 'https://integrationapi.net/rest/User/Balance?SessionID='.$obAutparam->sessid;
		$response = $this->openHttp($url);

		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		if(strpos($response,'{')!==false){
			try{
				$error = \Bitrix\Main\Web\Json::decode($response);
				$data->error = 'Error: '.$error['Desc'];
				$data->error_code = $this->chechErrorCode($error['Code']);
			}
			catch(\Exception $ex){
				$data->error = 'Service is not available';
				$data->error_code = '9998';
			}
			return $data;
		}
		
		$data->balance = $response;
		
		return $data;
		
	}
	
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
		
		$data = new \stdClass();
		
		$obAutparam = $this->getSessionKey();
		if($obAutparam->error_code) return $obAutparam;
		
		$charset = $this->config->charset;

		if($charset=='windows-1251') {
			$mess = $GLOBALS['APPLICATION']->ConvertCharset($mess, $charset, 'UTF-8');
		}
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$params = array(
			'SessionId'=>$obAutparam->sessid,
			'DestinationAddress'=>$phones,
			'SourceAddress'=>$sender,
			'Data'=>$mess,
			'Validity'=>0
		);
		$url = 'https://integrationapi.net/rest/Sms/Send';
		
		$response = $this->openHttp($url,true,$params);

		if(!$response){
			
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		if(strpos($response,'{')!==false){
			try{
				$error = \Bitrix\Main\Web\Json::decode($response);
				$data->error = 'Error: '.$error['Desc'];
				$data->error_code = $this->chechErrorCode($error['Code']);
			}
			catch(\Exception $ex){
				$data->error = 'Service is not available';
				$data->error_code = '9998';
			}
			return $data;
		}else if(substr($response,0,1)=='['){
			$d = explode(',',$response);
			$d[0] = str_replace(array('[',']','"'),"",$d[0]);
			$data->id = $d[0];
		}else{
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		return $data;
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$data = new \stdClass();
		
		$obAutparam = $this->getSessionKey();
		if($obAutparam->error_code) return $obAutparam;
		
		$url = 'https://integrationapi.net/rest/Sms/State?sessionId='.$obAutparam->sessid.'&messageId='.$smsid;
		$response = $this->openHttp($url);

		if(!$response){
			
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		try{
			$state = \Bitrix\Main\Web\Json::decode($response);
			if($error['Code']){
				$data->error = 'Error: '.$error['Desc'];
				$data->error_code = $this->chechErrorCode($error['Code']);
				return $data;
			}else{
				$data->status = $this->_checkStatus($state['State']);
			}
		}
		catch(\Exception $ex){
			$data->status = 12;
		}
		
		
		$data->last_timestamp = time();
			
		return $data;
		
	}
	
	private function getConfig($params) {
		
		$c = new \stdClass();
		$c->login = $params['login'];
		$c->passw = $params['passw'];
		$c->sender = $params['sender'];
		$c->charset = $params['charset'];
		
		return $c;
		
	}
	
	private function openHttp($url, $method_ = false, $params = null) {
		
		if($method_ === false) {
			$httpClient = new \Bitrix\Main\Web\HttpClient();
			$result = $httpClient->get($url);
		}else{
			$httpClient = new \Bitrix\Main\Web\HttpClient(array('charset'=>'utf-8'));
			$httpClient->setHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8', true);
			$result = $httpClient->post($url,$params);
		}
		
		return $result;
		
	}
	
	//открываем сессию
	private function getSessionKey() {
		
		if($this->session) return $this->session;
		
		$url = 'https://integrationapi.net/rest/user/sessionid?login='.$this->config->login.'&password='.urlencode($this->config->passw);
		$response = $this->openHttp($url);
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			$session = $data;
			return $data;
		}
		
		if(strpos($response,'{')!==false){
			$error = \Bitrix\Main\Web\Json::decode($response);
			
			$data->error = 'Error: '.$error['Desc'];
			$data->error_code = $this->chechErrorCode($error['Code']);
			
			$this->session = $data;
			return $data;
		}
		$data->sessid = str_replace('"',"",$response);
		$this->session = $data;
		return $data;
	
	}
	
	private function chechErrorCode($code) {
		
		if($code==1 || $code==2) return 1;
		if($code==3 || $code==4) return 2;
		if($code==6 || $code==7 || $code==8 || $code==9) return 9998;
		if($code==5) return 3;
		return 9999;
	
	}
	
	private function _checkStatus($code) {

		if($code==-1) return 3;
		if($code==-2) return 6;
		if($code==47) return 7;
		if($code==-98) return 7;
		if($code==0) return 4;
		if($code==10) return 9;
		if($code==11) return 9;
		if($code==41) return 9;
		if($code==42) return 9;
		if($code==48) return 9;
		if($code==46) return 5;
		if($code==69) return 9;
		if($code==99) return 9;
		if($code==255) return 12;
		
		return 12;
		
	}
	
}