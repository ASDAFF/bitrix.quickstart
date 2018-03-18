<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Smspilot{

	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		$url = 'http://smspilot.ru/api.php?apikey='.$this->config->passw;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		if(substr($response, 0, 5)=='ERROR'){
			$response = explode(': ',$response);
			$data->error = $response[1];
			$response[0] = explode('=',$response[0]);
			$data->error_code = $this->chechErrorCode($response[0][1]);
			$response = $data;
			return $data;
		}
		
		$response = explode(PHP_EOL,$response);
		$senders = explode(',',str_replace('senders=','',$response[8]));
		
		foreach($senders as $value) {
			$ob = new \stdClass();
			$ob->sender = $value;
			$arr[] = $ob;
		}
		
		$data = $arr;
		
		return $data;
	}
	
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
		
		$phones = preg_replace("/[^0-9A-Za-z]/", "", $phones);
		$timeold = $time;
		
		if($time!=0 && $time>time()) {
			$time = '0'.$time;
		}
		else{
			$time = 0;
		}
		
		$phones = urlencode($phones);
		$mess = urlencode($mess);
		$charset = $this->config->charset;
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$timeold = gmdate('Y-m-d H:i:s',$timeold);
		
		$pp = '7193';
		$url =  'http://smspilot.ru/api.php?apikey='.$this->config->passw.'&to='.$phones.'&send='.$mess.
				'&charset='.$charset.'&from='.$sender.'&r='.$pp;
		if($time!=0) {
			$url .= '&send_datetime='.$timeold;
		}	
		
		$response = $this->openHttp($url);

		//$response = "SUCCESS=SMS SENT 1/8\n34674241,79211696184,1,0";
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		if(substr($response, 0, 5)=='ERROR'){
			$response = explode(': ',$response);
			$data->error = $response[1];
			$response[0] = explode('=',$response[0]);
			$data->error_code = $this->chechErrorCode($response[0][1]);
			$response = $data;
			return $data;
		}else{
			$response = explode(PHP_EOL,$response);
			
			foreach($response as $key=>$value){
				if($key==0){
					$response[$key] = explode(" ",$response[$key]);
					$response[$key][2] = explode("/",$response[$key][2]);
				}
				else{
					$response[$key] = explode(",",$response[$key]);
				}
			}
			
			$data->balance = $response[0][2][1];
			$data->cost = $response[0][2][0];
			$data->cnt = $response[0][2][0];
			$data->id = $response[1][0];
			
		}
		
		return $data;
		
	}
	
	public function _getBalance () {
	
		$url = 'http://smspilot.ru/api.php?balance=sms&apikey='.$this->config->passw;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		if(substr($response, 0, 5)=='ERROR'){
			$response = explode(': ',$response);
			$data->error = $response[1];
			$response[0] = explode('=',$response[0]);
			$data->error_code = $this->chechErrorCode($response[0][1]);
			$response = $data;
			return $data;
		}
		
		$data->balance = $response;
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$url = 'http://smspilot.ru/api.php?check='.$smsid.'&apikey='.$this->config->passw;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		if(substr($response, 0, 5)=='ERROR'){
			$response = explode(': ',$response);
			$data->error = $response[1];
			$response[0] = explode('=',$response[0]);
			$data->error_code = $this->chechErrorCode($response[0][1]);
			$response = $data;
			return $data;
		}
		
		$response = explode(',',$response);
		$data->status = $this->_checkStatus($response[3]);
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
	
	private function openHttp($url, $method = false, $params = null) {
	
		if (!function_exists('curl_init')) {
		    die('ERROR: CURL library not found!');
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, $method);
		if ($method == true && isset($params)) {
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}
		curl_setopt($ch,  CURLOPT_HTTPHEADER, array(
		    'Content-Length: '.strlen($params),
		    'Cache-Control: no-store, no-cache, must-revalidate',
		    "Expires: " . date("r")
		));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
		
	}
	
	private function chechErrorCode($code) {
	
		if($code==100 || $code==101 || $code==102 || $code==400 || $code==401) return 2;
		if($code==106 || $code==113 || $code==243) return 4;
		if($code==107 || $code==108 || $code==109 || $code==110 || $code==111 || $code==250) return 1;
		if($code==112) return 3;
		if($code==115 || $code==116) return 6;
		
		return 9999;
	
	}
	
	private function _checkStatus($code) {

		if($code==-2 ||$code==-1) return 7;
		if($code==0) return 6;
		if($code==1) return 3;
		if($code==2) return 4;
		
	}
	
}
?>