<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Smsasistentru{

	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		$ob = new \stdClass();
		$ob->sender = "CMC";
		$arr[] = $ob;
		
		return $arr;
		
	}
	
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
		
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
		
		$url =  'https://sys.sms-assistent.ru/api/v1/send_sms/plain?user='.$this->config->login.'&password='.$this->config->passw.'&recipient='.$phones.'&message='.$mess.'&sender='.$sender;
		
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		if(strpos($response,"-")!==false){
			$data->error_code = $this->chechErrorCode($response);
			$data->error = 'Error code '.$response;
		}else{
			$data->id = intval($response);
			$data->cnt = '';
			$data->cost = '';
			$data->balance = '';
		}
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$url = 'https://sys.sms-assistent.ru/api/v1/statuses/plain?user='.$this->config->login.'&password='.$this->config->passw.'&id='.$smsid;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		if(strpos($response,"-")!==false){
			$data->error_code = $this->chechErrorCode($response);
			$data->error = 'Error code '.$response;
		}else{
			$data->status = $this->_checkStatus($response);
			$data->last_timestamp = time();
		}
			
		return $data;
		
	}
	
	public function _getBalance () {
	
		$url = 'https://sys.sms-assistent.ru/api/v1/credits/plain?user='.$this->config->login.'&password='.$this->config->passw;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		if(strpos($response,"-")!==false){
			$data->error_code = $this->chechErrorCode($response);
			$data->error = 'Error code '.$response;
		}
		
		$data->balance = $response;
		
		return $data;
		
	}
	
	private function chechErrorCode($code) {
	
		if($code=="-1") return 3;
		if($code=="-2") return 2;
		if($code=="-3") return 1;
		if($code=="-4") return 1;
		if($code=="-5") return 1;
		if($code=="-6") return 1;
		if($code=="-7") return 1;
		
		return 9999;
	
	}
	
	private function _checkStatus($code) {

		if($code=='Queued') return 6;
		if($code=='Sent') return 3;
		if($code=='Delivered') return 4;
		if($code=='Expired') return 5;
		if($code=='Rejected') return 9;
		if($code=='Unknown') return 12;
		if($code=='Failed') return 7;
		
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