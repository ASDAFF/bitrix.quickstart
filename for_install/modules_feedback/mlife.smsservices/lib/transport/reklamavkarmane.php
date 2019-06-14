<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Reklamavkarmane{
	
	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	private function getConfig($params) {
		
		$c = new \stdClass();
		$c->login = $params['login'];
		$c->passw = $params['passw'];
		$c->sender = $params['sender'];
		$c->charset = $params['charset'];
		
		return $c;
		
	}
	
	public function _getAllSender() {
		
		$url = 'http://api.user.reklamavkarmane.ru/senders.get';
		$request_params = array();
		$request_params['pid'] = $this->config->login[1];
		$sig = $this->sig($request_params, 'senders.get');
		$params = array(
		    'uid='.$this->config->login[0],
		    'pid='.$this->config->login[1],
		    'sig='.$sig,
		);
		$params = implode('&', $params);
		$response = $this->openHttp($url,true,$params);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$rest = json_decode($response);
		if(isset($rest->error_type) && $rest->error_type) {
			$data->error_code = $this->chechErrorCode($rest->error_type);
			$data->error = $rest->error_message;
			return $data;
		}
		elseif (is_array($rest) && count($rest)>0){
			$arr = array();
			foreach($rest as $sender) {
				$ob = new \stdClass();
				$ob->sender = $sender->sender;
				if($sender->status==1) $arr[] = $ob;
			}
			return $arr;
			
		}
			
			$data->error_code = $this->chechErrorCode(false);
			$data->error = 'senders not found';
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
	
		$charset = $this->config->charset;
		if($charset=='windows-1251') {
		$charset = 'cp1251';
			$mess = iconv("CP1251", "UTF-8", $mess);
		}
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$pp = '';
		$url = 'http://api.user.reklamavkarmane.ru/delivery.sendSms';
		$request_params = array();
		$request_params['pid'] = $this->config->login[1];
		$request_params['sender'] = $sender;
		$request_params['to'] = $phones;
		$request_params['text'] = $mess;
		$sig = $this->sig($request_params, 'delivery.sendSms');
		$params = array(
		    'uid='.$this->config->login[0],
		    'pid='.$this->config->login[1],
			'sender='.$sender,
			'to='.$phones,
			'text='.$mess,
		    'sig='.$sig,
		);
		$params = implode('&', $params);
		$response = $this->openHttp($url,true,$params);
		
		$data = new \stdClass();
		
		$rest = json_decode($response);
		if(isset($rest->error_type) && $rest->error_type) {
			$data->error_code = $this->chechErrorCode($rest->error_type);
			$data->error = $rest->error_message;
			return $data;
		}else{
			$data->id = $rest->tid;
			$data->cnt = $rest->to_count;
			$data->cost = $rest->cost;
			$data->balance = '';
			return $data;
		}
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {

		$pp = '';
		$url = 'http://api.user.reklamavkarmane.ru/delivery.report';
		$request_params = array();
		$request_params['pid'] = $this->config->login[1];
		$request_params['tids'] = $smsid;
		$request_params['detail'] = '1';
		//$request_params['period'] = '2013-01-01 00:00:00;2020-01-30 23:59:59';
		$sig = $this->sig($request_params, 'delivery.report');
		$params = array(
		    'uid='.$this->config->login[0],
		    'pid='.$this->config->login[1],
			'tids='.$smsid,
			'detail=1',
		    'sig='.$sig,
		);
		$params = implode('&', $params);
		$response = $this->openHttp($url,true,$params);
		
		$data = new \stdClass();
		
		$rest = json_decode($response);
		
		if(isset($rest->error_type) && $rest->error_type) {
			$data->error_code = $this->chechErrorCode($rest->error_type);
			$data->error = $rest->error_message;
			return $data;
		}else{
			$data->status = $this->_checkStatus($rest[0]->send_status->code);
			$data->last_timestamp = strtotime($rest[0]->update_time);
		}

		return $data;
		
	}
	
	public function _getBalance () {
	/*
	отправите запрос с uid и sig на метод projects.getInfo и параметр ids который равен ID-смс-сервиса
	*/
		$url = 'http://api.user.reklamavkarmane.ru/projects.getInfo';
		$request_params = array();
		$request_params['ids'] = $this->config->login[1];
		$sig = $this->sig($request_params, 'projects.getInfo');
		$params = array(
		    'uid='.$this->config->login[0],
		    'ids='.$this->config->login[1],
		    'sig='.$sig,
		);
		$params = implode('&', $params);
		$response = $this->openHttp($url,true,$params);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$rest = json_decode($response);
		if(isset($rest->error_type) && $rest->error_type) {
			$data->error_code = $this->chechErrorCode($rest->error_type);
			$data->error = $rest->error_message;
			return $data;
		}else{
			$data->balance = $rest[0]->send_balance;
			return $data;
		}
	
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
	
	/**
	 * Подпись параметров для передачи в АПИ
	 * 
	 * @param array $api_params
	 * @param string $api_method
	 */
	private function sig ($api_params, $api_method) {
	  $params = $this->getSigParamsString($api_params);
	  return md5($this->config->login[0] . $api_method . $params . $this->config->passw);
	}
	/**
	 * Сбор параметров в строку
	 * 
	 * @param array $api_params
	 * @param mixed $parent_key - родительский ключ для рекурсии
	 */
	private function getSigParamsString($api_params, $parent_key=null) {
		$params = '';
		ksort($api_params, SORT_STRING);
		
		foreach ($api_params as $key => $value) {
	  		if ($parent_key !== null || ($key != 'uid' && $key != 'method' && $key != 'sig')) {
	  			if (is_array($value)) {
	  				if ($parent_key) {
	  					$key = $parent_key."[$key]";
	  				}
	  				$params .= $this->getSigParamsString($value, $key);
	  			} else {
	  				if ($parent_key) {
	  					$params .= $parent_key."[$key]=$value";
	  				} else {
	    				$params .= "$key=$value";
	  				}
	  			}
			}
		}
		
		return $params;
	}
	
	private function chechErrorCode($code) {
	
		if($code=='access_validation') return 1;
		return 9999;
	
	}
	
	private function _checkStatus($code) {
		//queued, wait, accepted, delivered, failed и not_delivered
		
		if($code=='queued') return 3;
		if($code=='wait') return 6;
		if($code=='accepted') return 2;
		if($code=='delivered') return 4;
		if($code=='failed') return 7;
		if($code=='not_delivered') return 7;
		
	}
	
}