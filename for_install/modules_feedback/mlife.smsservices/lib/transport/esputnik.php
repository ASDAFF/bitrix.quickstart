<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Esputnik{

	private $config;
	public $countsms = 0;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		$arr = array();
		
		$url =  'https://esputnik.com/api/v1/interfaces/sms';
		$response = $this->openHttp($url);
		
		try{
			$response = json_decode($response);
		}
		catch(\Exception $ex){
			
		}
		
		//print_r($response);die();
		
		if(is_array($response)){
			foreach($response as $val){
				$ob = new \stdClass();
				if($val->type=='sms'){
					$ob->sender = $val->value;
					$arr[] = $ob;
				}
			}
		}
		return $arr;
		
	}
	
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
		$phones = preg_replace("/[^0-9]/", "", $phones);
		$this->countsms = $this->countsms + 1;
		
		$timeold = $time;
		
		if($time!=0 && $time>time()) {
			$time = '0'.$time;
		}
		else{
			$time = 0;
		}
		
		$phones = urlencode($phones);
		$charset = $this->config->charset;
		
		if(SITE_CHARSET=='windows-1251') {
			$charset = 'cp1251';
			$mess = iconv("CP1251", "UTF-8", $mess);
		}
		
		//$mess = urlencode($mess);
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$prior = 1;
		if($this->countsms>3) $prior = 0;
		
		$url =  'https://esputnik.com/api/v1/message/sms';
		
		$json_value = new \stdClass();
		$json_value->text = $mess;
		$json_value->from = $sender;
		$json_value->phoneNumbers = array($phones);
		
		$response = $this->openHttp($url, true, json_encode($json_value));
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		try{
			$response = json_decode($response);
			//echo'<pre>';print_r($response);echo'</pre>';die();
			if($response->results->message){
				$data->error = $response->results->message;
				if(SITE_CHARSET=='windows-1251') {
					$data->error = $GLOBALS['APPLICATION']->ConvertCharset($data->error, 'UTF-8',  SITE_CHARSET);
				}
				$data->error_code = '9998';
			}else{
				$data->id = $response->results->id ? $response->results->id : $response->results->requestId;
				$data->cnt = '';
				$data->cost = '';
				$data->balance = '';
			}
			
			
			
		}
		catch(\Exception $ex){
			$data->error_code = $this->chechErrorCode($response);
			$data->error = 'Error code 9998';
		}
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$url = 'https://esputnik.com/api/v1/message/sms/status?ids='.$smsid;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		try{
			$response = json_decode($response);
			
			$data->status = $this->_checkStatus($response->results->status);
			$data->last_timestamp = time();
			
		}
		catch(\Exception $ex){
			$data->error_code = $this->chechErrorCode($response);
			$data->error = 'Error code 9998';
			return $data;
		}
		
			
		return $data;
		
	}
	
	public function _getBalance () {
	
		$url =  'https://esputnik.com/api/v1/balance';
		$response = $this->openHttp($url);
		//print_r($response);die();
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		try{
			$response = json_decode($response);
		}
		catch(\Exception $ex){
			$data->error_code = $this->chechErrorCode($response);
			$data->error = 'Error code 9998';
			return $data;
		}
		
		$data->balance = $response->currentBalance;
		
		return $data;
		
	}
	
	private function chechErrorCode($code) {
		
		if(strpos($code,"Authentication error")!==false) return 2;
		//if(strpos($code,"NO_MESSAGE")!==false) return 1;
		//if(strpos($code,"NO_PHONE")!==false) return 1;
		
		return 9999;
	
	}
	
	private function _checkStatus($code) {
		$code = toUpper($code);
		
		if($code=='MESSAGE_NOT_FOUND') return 12;
		
		if($code=='IN_QUEUE') return 2;
		if($code=='PENDING') return 3;
		if($code=='EXPIRED') return 5;
		if($code=='DELIVERED') return 4;
		if($code=='READ') return 4;
		if($code=='NO_MONEY') return 10;
		if($code=='UNKNOWN_INTERFACE') return 12;
		if($code=='ERROR') return 12;
		if($code=='FAILED') return 7;
		if($code=='BLACKLISTED') return 9;
		if($code=='NOT_CONFIRMED') return 9;
		if($code=='CANNOT_FIND_CHANNEL') return 9;
		if($code=='UNSUBSCRIBED') return 9;
		if($code=='UNDELIVERED') return 7;
		return 12;
	}
	
	private function getConfig($params) {
		
		$c = new \stdClass();
		$c->login = $params['login'];
		$c->passw = $params['passw'];
		$c->sender = $params['sender'];
		$c->charset = $params['charset'];
		
		return $c;
		
	}
	
	private function openHttp($url, $method = false, $params = null) {
		
		if (!function_exists('curl_init')) {
		    die('ERROR: CURL library not found!');
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, $method);
		if ($method == true && isset($params)) {
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		    
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
		    
		}else{
			curl_setopt($ch,  CURLOPT_HTTPHEADER, array(
			    'Content-Length: '.strlen($params),
			    'Cache-Control: no-store, no-cache, must-revalidate',
			    "Expires: " . date("r")
			));
		}
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch,CURLOPT_USERPWD, $this->config->login.':'.$this->config->passw);
		
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
			
	}

}