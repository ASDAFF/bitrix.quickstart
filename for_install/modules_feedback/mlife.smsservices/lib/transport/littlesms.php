<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Littlesms{

	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		$charset = $this->config->charset;
		if($charset=='windows-1251') $charset = 'cp1251';
		
		$url = 'https://littlesms.ru/api/sender/list?user='.$this->config->login.'&apikey='.$this->config->passw.'&encoding='.$charset;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
			$respApi = json_decode($response);
			
			if($respApi->status=='error'){
				$data->error_code = $this->chechErrorCode($respApi->error);
				$data->error = $respApi->message;
			}
			else{
				foreach($respApi->list as $sender){
					$ob = new \stdClass();
					$ob->sender = $sender->name;
					$arr[] = $ob;
				}
				$data = $arr;
			}
		
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
		
		//$mess = urlencode($mess);
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$pp = '';
		$data = new \stdClass();
		
		if($time==0){
		
			$url =  'https://littlesms.ru/api/message/send?user='.$this->config->login.'&apikey='.$this->config->passw.'&recipients='.$phones.'&message='.urlencode($mess).
					'&encoding='.$charset.'&sender='.$sender.'&pp='.$pp.'&is_active=true';
			$response = $this->openHttp($url);
			
			if(!$response){
				$data->error = 'Service is not available';
				$data->error_code = '9998';
				return $data;
			}
			
			$respApi = json_decode($response);
			
			if($respApi->status=='error'){
				$data->error_code = $this->chechErrorCode($respApi->error);
				$data->error = $respApi->message;
			}
			else{
				$data->id = $respApi->messages_id[0];
				$data->cnt = $respApi->count;
				$data->cost = $respApi->price;
				$data->balance = $respApi->balance;
			}
			
		}
		else{
			$timeold = gmdate('d.m.Y H:i:s',$timeold);
			
			$url =  'https://littlesms.ru/api/task/create?user='.$this->config->login.'&apikey='.$this->config->passw.'&recipient='.$phones.'&message='.urlencode($mess).
					'&sender='.$sender.'&pp='.$pp.'&starts_at='.urlencode($timeold).'&name='.urlencode($timeold).'&is_active=true';
			$response = $this->openHttp($url);
			
			if(!$response){
				$data->error = 'Service is not available';
				$data->error_code = '9998';
				return $data;
			}
			
			$respApi = json_decode($response);
			
			if($respApi->status=='error'){
				$data->error_code = $this->chechErrorCode($respApi->error);
				$data->error = $respApi->message;
			}
			else{
				$data->id = $respApi->id; //TODO возвращает ид задачи
				$data->cnt = '';
				$data->cost = '';
				$data->balance = '';
			}
		}
		
		
		return $data;
		
	}
	
	public function _getBalance () {
	
		$url = 'https://littlesms.ru/api/user/balance?user='.$this->config->login.'&apikey='.$this->config->passw;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$respApi = json_decode($response);
		
		if($respApi->status=='error'){
			$data->error_code = $this->chechErrorCode($respApi->error);
			$data->error = $respApi->message;
		}
		else{
			$data->balance = $respApi->balance;
		}
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {

		$url= 'https://littlesms.ru/api/message/status?user='.$this->config->login.'&apikey='.$this->config->passw.'&messages_id='.$smsid;
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$respApi = json_decode($response);
		
		if($respApi->status=='error'){
			$data->error_code = $this->chechErrorCode($respApi->error);
			$data->error = $respApi->message;
		}
		else{
			$data->status = $this->_checkStatus($respApi->messages->$smsid);
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
	
		if($code==1 || $code==2) return 2;
		if($code==10 || $code==11 || $code==3 || $code==33 || $code==4 || $code==44) return 1;
		if($code==6) return 4;
		if($code==5) return 3;
		if($code==7 || $code==9) return 6;
		
		return 9999;
	
	}
	
	private function _checkStatus($code) {

		if($code=='enqueued') return 6;
		if($code=='accepted') return 3;
		if($code=='delivered') return 4;
		if($code=='expired') return 5;
		if($code=='undeliverable') return 7;
		if($code=='rejected') return 9;
		if($code=='deleted') return 9;
		
	}
	
}
?>