<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Devonoteleviberapp{
	
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
		
		//$obAutparam = $this->getSessionKey();
		//if($obAutparam->error_code) return $obAutparam;
		
		$charset = $this->config->charset;

		if($charset=='windows-1251') {
			$mess = $GLOBALS['APPLICATION']->ConvertCharset($mess, $charset, 'UTF-8');
		}
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$params = (object)array(
			//'SessionId'=>$obAutparam->sessid,
			//'DestinationAddress'=>$phones,
			//'SourceAddress'=>$sender,
			//'Data'=>$mess,
			//'Validity'=>0
			'resendSms'=>'false',
			'messages'=>array(
				(object)array(
					'subject'=>$sender,
					'priority' => 'realtime',
					"validityPeriodSec" => intval(\Bitrix\Main\Config\Option::get("mlife.smsservices","limittimesms",600,"")),
					'type'=>'viber',
					'contentType'=>'text',
					'content'=>(object)array(
						'text'=>$mess
					),
					'address'=>$phones
				)
			)
		);
		$url = 'https://viber.devinotele.com:444/send';
		//$this->_getStatusSms('3223503951873703938');die();
		$response = $this->openHttp($url,true,$params,base64_encode($this->config->login.':'.$this->config->passw),true);
		

		if(!$response){
			
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		if(strpos($response,'{')!==false){
			try{
				$error = \Bitrix\Main\Web\Json::decode($response);
				//echo'<pre>';print_r(array($error));echo'</pre>';die();
				if($error['error']){
					$data->error = 'Error: '.$error['message'];
					$data->error_code = $this->chechErrorCode($error['error']);
				}elseif($error['messages'][0]['code']!='ok'){
					$data->error = 'Error: '.$error['messages'][0]['code'];
					$data->error_code = $this->chechErrorCode($error['messages'][0]['code']);
				}elseif($error['messages'][0]['code']=='ok'){
					$r = $error;
					$data->id = $error['messages'][0]['providerId'];
					
				}else{
					$data->error = 'Unknown';
					$data->error_code = '9998';
				}
			}
			catch(\Exception $ex){
				$data->error = 'Service is not available';
				$data->error_code = '9998';
			}
			return $data;
		}else if(false && substr($response,0,1)=='['){
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
		
		$url = 'https://viber.devinotele.com:444/status';
		
		$params = (object)array(
			'messages'=>array($smsid)
		);
		
		$response = $this->openHttp($url,true,$params,base64_encode($this->config->login.':'.$this->config->passw),true);
		
		//echo'<pre>';print_r(array($response));echo'</pre>';

		if(!$response){
			
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		try{
			$state = \Bitrix\Main\Web\Json::decode($response);
			$data->resp = $state;
			if($state['messages'][0]['status']) if($this->_checkStatus($state['messages'][0]['status'])!=12) $state['messages'][0]['error'] = '';
			if(empty($state['messages'])){
					$data->error = 'Error: '.$state['status'];
					$data->error_code = $this->chechErrorCode($state['status']);
			}elseif($state['error']){
					$data->error = 'Error: '.$state['message'];
					$data->error_code = $this->chechErrorCode($state['error']);
			}elseif($state['messages'][0]['error'] && $state['messages'][0]['status']!='undelivered' && $state['messages'][0]['status']!='vp_expired'){
					$data->error = 'Error: '.$state['messages'][0]['error'];
					$data->error_code = $this->chechErrorCode($state['messages'][0]['error']);
			}else{
				if($state['messages'][0]['error']){
					$data->status = $this->_checkStatus($state['messages'][0]['status'].'-'.$state['messages'][0]['error']);
				}else{
					$data->status = $this->_checkStatus($state['messages'][0]['status']);
				}
				$data->last_timestamp = strtotime($state['messages'][0]['statusAt']);
			}
			
		}
		catch(\Exception $ex){
			$data->status = 12;
		}
		
		
		//if(!$data->last_timestamp) 
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
	
	private function openHttp($url, $method_ = false, $params = null, $aut=false, $json=false) {
		
		if($method_ === false) {
			$httpClient = new \Bitrix\Main\Web\HttpClient();
			if($aut) $httpClient->setHeader('Authorization', 'Basic '.$aut, true);
			$result = $httpClient->get($url);
		}else{
			$httpClient = new \Bitrix\Main\Web\HttpClient(array('charset'=>'utf-8'));
			
			if($aut) $httpClient->setHeader('Authorization', 'Basic '.$aut, true);
			if($json){
				$httpClient->setHeader('Content-Type', 'application/json; charset=utf-8', true);
				$result = $httpClient->post($url,\Bitrix\Main\Web\Json::encode($params));
			}else{
				$httpClient->setHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8', true);
				$result = $httpClient->post($url,$params);
			}
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
		
		if(strpos($code,'Unauthorized')!==false) return 2;
		if(strpos($code,'error-syntax')!==false) return 1;
		if(strpos($code,'error-auth')!==false) return 2;
		if(strpos($code,'error-account-locked')!==false) return 4;
		if(strpos($code,'error-instant-message-typeformat')!==false) return 1;
		if(strpos($code,'error-instant-message-content-type-format')!==false) return 1;
		if(strpos($code,'error-instant-message-content-image-id-format')!==false) return 1;
		if(strpos($code,'error-priority-format')!==false) return 1;
		if(strpos($code,'error-instant-message-client-id-not-unique')!==false) return 1;
		if(strpos($code,'error-subject-format')!==false) return 1;
		if(strpos($code,'error-subject-unknown')!==false) return 1;
		if(strpos($code,'error-subject-not-specified')!==false) return 1;
		if(strpos($code,'error-address-format')!==false) return 1;
		if(strpos($code,'error-address-unknown')!==false) return 1;
		if(strpos($code,'error-address-not-specified')!==false) return 1;
		if(strpos($code,'error-priority-format')!==false) return 1;
		if(strpos($code,'error-comment-format')!==false) return 1;
		if(strpos($code,'error-instant-message-type-format')!==false) return 1;
		if(strpos($code,'error-instant-message-type-not-specified')!==false) return 1;
		if(strpos($code,'error-content-type-format')!==false) return 1;
		if(strpos($code,'error-content-not-specified')!==false) return 1;
		if(strpos($code,'error-validity-period-seconds-format')!==false) return 1;
		if(strpos($code,'error-instant-message-provider-id-format')!==false) return 1;
		if(strpos($code,'error-instant-message-provider-id-duplicate')!==false) return 1;
		if(strpos($code,'error-instant-message-provider-id-unknown')!==false) return 1;
		if(strpos($code,'error-resend-sms-error')!==false) return 1;
		if(strpos($code,'error-resend-sms-validity-period-error')!==false) return 1;
		

		return 9999;
	
	}
	
	private function _checkStatus($code) {
	
		if(strpos($code,'undelivered')!==false && strpos($code,'not-viber-user')!==false) return 11;
		if(strpos($code,'undelivered')!==false && strpos($code,'user-blocked')!==false) return 9;
		if(strpos($code,'undelivered')!==false) return 7;
		if(strpos($code,'read')!==false) return 14;
		if(strpos($code,'delivered')!==false) return 4;
		if(strpos($code,'enqueued')!==false) return 6;
		if(strpos($code,'sent')!==false) return 3;
		if(strpos($code,'vp_expired')!==false) return 5;
		
		if(strpos($code,'failed')!==false) return 7;
		if(strpos($code,'cancelled')!==false) return 7;
		if(strpos($code,'unknown-error')!==false) return 12;
		
		return 12;
		
	}
	
}
?>