<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Sms4b{

	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		//получаем данные сессии
		$obAutparam = $this->getSessionKey();
		if($obAutparam->error_code) return $obAutparam;
		
		//запрос на параметры профиля
		$url = 'https://sms4b.ru/ws/sms.asmx/ParamSMS';
		$params = array(
		    'SessionId='.$obAutparam->sessid,
		);
		$params = implode('&', $params);
		$response = $this->openHttp($url,true,$params);
		
		//убиваем сессию
		$this->closeSession($obAutparam->sessid);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		try{
			$response = new \SimpleXMLElement($response);
		}
		catch(Exception $ex)
		{
			$data->error = 'Error';
			$data->error_code = '9999';
			return $data;
		}

		if(substr((string)$response->Result,0,1)=='-') {
			$data->error = 'Error: '.(string)$response->Result;
			$data->error_code = $this->chechErrorCode(substr((string)$response->Result,1,5));
			return $data;
		}
		
		$senders = explode(PHP_EOL,(string)$response->Addresses); //TODO если ошибка при множестве поправить разделитель
			foreach($senders as $sender) {
				$ob = new \stdClass();
				$ob->sender = $sender;
				$arr[] = $ob;
			}
			$data = $arr;
		
		return $data;
	}
	
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
		
		$data = new \stdClass();
		
		$timeold = $time;
		
		if($time!=0 && $time>time()) {
		//TODO пока нет поддержки отправки по рассписанию
		$this->sendSms($phones, $mess, 0, $sender);
		return;
		//
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
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		if($time=='0'){
		
			$url = 'https://sms4b.ru/ws/sms.asmx/SendSMS';
			$params = array(
			    'Login='.$this->config->login,
			    'Password='.$this->config->passw,
			    'Source='.$sender,
			    'Phone='.$phones,
			    'Text='.urlencode($mess)
			);
			$params = implode('&', $params);
			$response = $this->openHttp($url,true,$params);
			
			if(!$response){
				$data->error = 'Service is not available';
				$data->error_code = '9998';
				return $data;
			}
			
			try{
				$response = new \SimpleXMLElement($response);
			}
			catch(Exception $ex)
			{
				$data->error = 'Error';
				$data->error_code = '9999';
				return $data;
			}
			
			$responsestr = (string)$response[0];
			if(substr($responsestr,0,1)=='-') {
				$data->error = 'Error: '.$responsestr;
				$data->error_code = $this->chechErrorCode(substr($responsestr,1,5));
				return $data;
			}
			
			$data->id = $responsestr;
			$data->cnt = '';
			$data->cost = '';
			$data->balance = '';
			
		}
		else {
		//TODO включить поддержку отложенной отправки
			$url = 'https://sms4b.ru/ws/sms.asmx/GroupSMS';
		}
		
		return $data;
		
	}
	
	public function _getBalance () {
	
		//получаем данные сессии
		$obAutparam = $this->getSessionKey();
		if($obAutparam->error_code) return $obAutparam;
		
		//запрос на параметры профиля
		$url = 'https://sms4b.ru/ws/sms.asmx/ParamSMS';
		$params = array(
		    'SessionId='.$obAutparam->sessid,
		);
		$params = implode('&', $params);
		$response = $this->openHttp($url,true,$params);
		
		//убиваем сессию
		$this->closeSession($obAutparam->sessid);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		try{
			$response = new \SimpleXMLElement($response);
		}
		catch(Exception $ex)
		{
			$data->error = 'Error';
			$data->error_code = '9999';
			return $data;
		}

		if(substr((string)$response->Result,0,1)=='-') {
			$data->error = 'Error: '.(string)$response->Result;
			$data->error_code = $this->chechErrorCode(substr((string)$response->Result,1,5));
			return $data;
		}
		
		$data->balance = (string)$response->Rest;
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$obAutparam = $this->getSessionKey();
		if($obAutparam->error_code) return $obAutparam;
		
		
		
		$url = 'https://sms4b.ru/ws/sms.asmx/CheckSMS';
		$params = array(
		    'SessionId='.$obAutparam->sessid,
		    'Guids='.$smsid
		);
		$params = implode('&', $params);
		$response = $this->openHttp($url,true,$params);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		try{
			$response = new \SimpleXMLElement($response);
		}
		catch(Exception $ex)
		{
			$data->error = 'Error';
			$data->error_code = '9999';
			return $data;
		}
		
		if(substr((string)$response->Result,0,1)=='-') {
			$data->error = 'Error: '.(string)$response->Result;
			$data->error_code = $this->chechErrorCode(substr((string)$response->Result,1,5));
			return $data;
		}
		else{
			$data->status = $this->_checkStatus((string)$response->List->CheckSMSList->R);
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
	
	//открываем сессию
	private function getSessionKey() {
		
		$url = 'https://sms4b.ru/ws/sms.asmx/StartSession';
		$params = array(
		    'Login='.$this->config->login,
		    'Password='.$this->config->passw,
		    'Gmt=0'
		);
		$params = implode('&', $params);
		$response = $this->openHttp($url,true,$params);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		try{
			$response = new \SimpleXMLElement($response);
		}
		catch(Exception $ex)
		{
			$data->error = 'Error';
			$data->error_code = '9999';
			return $data;
		}
		
		$responsestr = (string)$response[0];
		
		if(substr($responsestr,0,1)=='-') {
			$data->error = 'Error: '.$responsestr;
			$data->error_code = $this->chechErrorCode(substr($responsestr,1,5));
		}
		else{
			$data->sessid = $responsestr;
		}
		return $data;
	
	}
	
	private function closeSession($sid) {
		$url = 'https://sms4b.ru/ws/sms.asmx/CloseSession';
		$params = array(
		    'SessionID='.$sid
		);
		$params = implode('&', $params);
		$response = $this->openHttp($url,true,$params);
	}
	
	private function chechErrorCode($code) {
		
		if($code==1 || $code==2) return 2;
		return 9999;
	
	}
	
	private function _checkStatus($code) {

		if($code<0) return 9;
		$code = decbin($code);
		
		if(substr($code,16,1)==0 && substr($code,8,8)=='00000000') return 6;
		if(substr($code,16,1)==0 && substr($code,8,8)!='00000000') return 3;
		if(substr($code,16,1)==1 && substr($code,0,8)==substr($code,8,8)) return 4;
		if(substr($code,16,1)==1 && substr($code,0,8)!=substr($code,8,8)) return 7;
		
	}
	
}
?>