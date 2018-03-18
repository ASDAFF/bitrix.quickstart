<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Rocketsmsby{

	private $config;
	public $countsms = 0;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		$arr = array();
		
		$url =  'http://api.rocketsms.by/simple/senders?username='.$this->config->login.'&password='.md5($this->config->passw);
		$response = $this->openHttp($url);
		$response = json_decode($response);
		
		if(is_array($response)){
			foreach($response as $val){
				$ob = new \stdClass();
				if($val->verified==true){
					$ob->sender = $val->sender;
					$arr[] = $ob;
				}
			}
		}
		return $arr;
		
	}
	
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
		
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
		
		if($charset=='windows-1251') {
			$charset = 'cp1251';
			$mess = iconv("CP1251", "UTF-8", $mess);
		}
		
		$mess = urlencode($mess);
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$prior = 1;
		if($this->countsms>3) $prior = 0;
		
		$url =  'http://api.rocketsms.by/simple/send?username='.$this->config->login.'&password='.md5($this->config->passw).'&phone='.$phones.'&text='.$mess.'&sender='.$sender.'&priority='.$prior;
		
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$response = json_decode($response);
		
		if($response->error){
			$data->error_code = $this->chechErrorCode($response->error);
			$data->error = 'Error code '.$response->error;
		}else{
			$data->id = $response->id;
			$data->cnt = $response->cost->credits;
			$data->cost = $response->cost->money;
			$data->balance = '';
		}
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$url = 'http://api.rocketsms.by/simple/status?username='.$this->config->login.'&password='.md5($this->config->passw).'&id='.$smsid;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$response = json_decode($response);
		if($response->error){
			$data->error_code = $this->chechErrorCode($response->error);
			$data->error = 'Error code '.$response->error;
		}else{
			$data->status = $this->_checkStatus($response->status);
			$data->last_timestamp = time();
		}
			
		return $data;
		
	}
	
	public function _getBalance () {
	
		$url = 'http://api.rocketsms.by/simple/balance?username='.$this->config->login.'&password='.md5($this->config->passw);
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$response = json_decode($response);
		
		if($response->error){
			$data->error_code = $this->chechErrorCode($response->error);
			$data->error = 'Error code '.$response->error;
		}
		
		$data->balance = $response->balance;
		
		return $data;
		
	}
	
	private function chechErrorCode($code) {
		
		if(strpos($code,"WRONG_AUTH")!==false) return 2;
		if(strpos($code,"NO_MESSAGE")!==false) return 1;
		if(strpos($code,"NO_PHONE")!==false) return 1;
		
		return 9999;
	
	}
	
	private function _checkStatus($code) {
		$code = toUpper($code);
		if($code=='QUEUED') return 6;
		if($code=='SENT') return 3;
		if($code=='DELIVERED') return 4;
		if($code=='EXPIRED') return 5;
		if($code=='REJECTED') return 9;
		if($code=='UNKNOWN') return 12;
		if($code=='FAILED') return 7;
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

}