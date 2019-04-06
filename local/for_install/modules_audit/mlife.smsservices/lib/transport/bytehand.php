<?php
namespace Mlife\Smsservices\Transport;

class Bytehand{
	
	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		$url = 'http://bytehand.com:3800/signatures?id='.$this->config->login.'&key='.$this->config->passw;
		$response = $this->openHttp($url);
		//print_r($response);
		$arr = array();
		try{
			$response = json_decode($response);
		}
		catch(\Exception $ex){
			
		}
		
		if(is_array($response)){
			foreach($response as $val){
				$ob = new \stdClass();
				
				$ob->sender = $val->text;
				$arr[] = $ob;
				
			}
		}
		return $arr;
	}
	
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
		
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
		
		$pp = '327698';

		$url =  'http://bytehand.com:3800/send?id='.$this->config->login.'&key='.$this->config->passw.'&to='.$phones.'&from='.$sender.'&text='.$mess;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		try{
			$response = json_decode($response);
			//echo'<pre>';print_r($response);echo'</pre>';die();
			if($response->status != 0){
				if(SITE_CHARSET=='windows-1251') {
					$data->error = $response->description;
					if(SITE_CHARSET=='windows-1251') {
						$data->error = $GLOBALS['APPLICATION']->ConvertCharset($data->error, 'UTF-8',  SITE_CHARSET);
					}
				}
				$data->error_code = $this->chechErrorCode($response->status);
			}else{
				$data->id = $response->description;
				$data->cnt = '';
				$data->cost = '';
				$data->balance = '';
			}
			
			
			
		}
		catch(\Exception $ex){
			$data->error_code = 'Service is not available';
			$data->error = 'Error code 9998';
		}
		
		return $data;
		
	}
	
	private function chechErrorCode($code) {
		
		return 9999;
	
	}
	
	
	public function _getBalance () {
	
		$url = 'http://bytehand.com:3800/balance?id='.$this->config->login.'&key='.$this->config->passw;
		$response = $this->openHttp($url);

		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		try{
			$response = json_decode($response);
			if($response->status == 0) {
				$data->balance = round($response->description,2);
			}else{
				$data->error = $response->description;
				$data->error_code = $this->chechErrorCode($response->status);
				return $data;
			}
		}
		catch(\Exception $ex){
			$data->error_code = 'Service is not available';
			$data->error = 'Error code 9998';
			return $data;
		}
		
		
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$url = 'http://bytehand.com:3800/status?id='.$this->config->login.'&key='.$this->config->passw.'&message='.$smsid;
		
		$response = $this->openHttp($url);

		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		try{
			$response = json_decode($response);
			
			if($response->status == 0) {
				$data->status = $this->_checkStatus($response->description);
				$data->last_timestamp = time();
			}else{
				$data->error = $response->description;
				$data->error_code = $this->chechErrorCode($response->status);
			}
			
		}
		catch(\Exception $ex){
			$data->error_code = 'Service is not available';
			$data->error = 'Error code 9998';
			return $data;
		}
		
			
		return $data;
		
	}
	
	/**
	 * получаем данные доступа к шлюзу из настроек модуля
	 */
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
	
	private function _checkStatus($code) {
		$code = toUpper($code);
		
		if($code=='MESSAGE_NOT_FOUND') return 12;
		
		if($code=='IN_QUEUE') return 2;
		if($code=='PENDING') return 3;
		if($code=='ACCEPTED') return 3;
		if($code=='NEW') return 3;
		if($code=='EXPIRED') return 5;
		if($code=='DELIVERED') return 4;
		if($code=='READ') return 4;
		if($code=='NO_MONEY') return 10;
		if($code=='UNKNOWN') return 12;
		if($code=='ERROR') return 12;
		if($code=='FAILED') return 7;
		if($code=='DELETED') return 9;
		if($code=='BLACKLISTED') return 9;
		if($code=='NOT_CONFIRMED') return 9;
		if($code=='CANNOT_FIND_CHANNEL') return 9;
		if($code=='UNSUBSCRIBED') return 9;
		if($code=='UNDELIVERED') return 7;
		if($code=='UNDELIVERABLE') return 7;
		return 12;
	}
	
}
?>