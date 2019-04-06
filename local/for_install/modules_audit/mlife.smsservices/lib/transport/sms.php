<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Sms{

	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		$url = 'http://sms.ru/my/senders?api_id='.$this->config->passw;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$response = explode(PHP_EOL,$response);
		
		if(count($response)>0){
			foreach ($response as $key=>$value) {
				if($key==0 && $value!='100'){
					$data->error_code = $this->chechErrorCode($value);
					$data->error = 'Error code '.$value;
				}
				else if($key>0 && $value){
					$ob = new \stdClass();
					$ob->sender = $value;
					$arr[] = $ob;
				}
			}
		}
		else{
			$data->error_code = '9999';
			$data->error = 'Error code 9999';
		}
		
		if(!$data->error) $data = $arr;
		
		return $data;
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
			if($time!=0 && strtotime($timeold)>time()) {
				$mess = iconv("CP1251", "UTF-8", $mess);
			}
		}
		
		$mess = urlencode($mess);
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$pp = '20782';
		$url =  'http://sms.ru/sms/send?api_id='.$this->config->passw.'&to='.$phones.'&text='.$mess.'&partner_id='.$pp.'&from='.$sender;
		if($time!=0) {
			//$url .= '&time='.$timeold;
		}
		
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$response = explode(PHP_EOL,$response);
		
		if(count($response)>0){
			foreach ($response as $key=>$value) {
				if($key==0 && $value!='100'){
					$data->error_code = $this->chechErrorCode($value);
					$data->error = 'Error code '.$value;
				}
				else if($key==1 && $value){
					$data->id = $value;
					
					$url2 = 'http://sms.ru/sms/cost?api_id='.$this->config->passw.'&to='.$phones.'&text='.$mess;
					$response2 = $this->openHttp($url2);
					$response2 = explode(PHP_EOL,$response2);
					if($response2[0]=='100'){
						$data->cnt = $response2[2];
						$data->cost = $response2[1];
					}else{
						$data->cnt = '';
						$data->cost = '';
					}
				}
				else if($key==(count($response)-1)) {
					$data->balance = str_replace('balance=','',$value);
				}
			}
		}
		else{
			$data->error_code = '9999';
			$data->error = 'Error code 9999';
		}
		
		return $data;
		
	}
	
	public function _getBalance () {
	
		$url = 'http://sms.ru/my/balance?api_id='.$this->config->passw;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$response = explode(PHP_EOL,$response);
		
		if(count($response)>0){
			foreach ($response as $key=>$value) {
				if($key==0 && $value!='100'){
					$data->error_code = $this->chechErrorCode($value);
					$data->error = 'Error code '.$value;
				}
				else if($key>0){
					$data->balance = $value;
				}
			}
		}
		else{
			$data->error_code = '9999';
			$data->error = 'Error code 9999';
		}
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$url = 'http://sms.ru/sms/status?api_id='.$this->config->passw.'&id='.$smsid;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
			if($response>108){
					$data->error_code = $this->chechErrorCode($response);
					$data->error = 'Error code '.$response;
			}
			else if($response>0){
					$data->status = $this->_checkStatus($response);
					$data->last_timestamp = time();
			}
			
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
	
		if($code==200 || $code==300 || $code==301 || $code==302) return 2;
		if($code==210 || $code==211 || $code==202 || $code==203 || $code==205 || $code==208 || $code==210 || $code==212) return 1;
		if($code==220) return 4;
		if($code==201 || $code==206) return 3;
		if($code==204) return 6;
		if($code==207 || $code==209 || $code==230) return 8;
		
		return 9999;
	
	}
	
	private function _checkStatus($code) {

		if($code==100) return 6;
		if($code==101 || $code==102) return 3;
		if($code==103) return 4;
		if($code==104) return 5;
		if($code==105 || $code==108) return 9;
		if($code==106 || $code==107) return 7;
		
	}
	
}
?>