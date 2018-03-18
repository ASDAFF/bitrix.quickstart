<?php
namespace Mlife\Smsservices\Transport;

class Qtelecomru{
	
	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		return array();
	}
	
	public function _getBalance () {
		
		$url = 'https://go.qtelecom.ru/public/http/';
		
		$params = array(
			'user' => $this->config->login,
			'pass' => $this->config->passw,
			'action' => 'balance'
		);
		
		$response = $this->openHttp($url, true, $params);
		
		$data = new \stdClass();
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$error = $this->checkError($response);
		if($error) {
			$data->error = 'CODE '.$error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		$count_resp = preg_match_all('/<AGT_BALANCE>(.*)<\/AGT_BALANCE>/Ui',$response, $matches);
		
		if($count_resp>0) {
			$data->balance = $matches[1][0];
		}else{
			$data->error = $error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		return $data;
		
	}
	
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
		
		$charset = $this->config->charset;

		if($charset=='windows-1251') {
			$mess = $GLOBALS['APPLICATION']->ConvertCharset($mess, $charset, 'UTF-8');
		}
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$url = 'https://go.qtelecom.ru/public/http/';
		
		$params = array(
			'user' => $this->config->login,
			'pass' => $this->config->passw,
			'action' => 'post_sms',
			'message' => $mess,
			'target' => $phones,
			'sender' => $sender
		);
		
		$response = $this->openHttp($url, true, $params);
		
		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$error = $this->checkError($response);
		if($error) {
			$data->error = 'CODE '.$error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		$count_resp = preg_match_all('/<MESSAGE SMS_ID="([0-9A-z]+)"/Ui',$response, $matches);
		
		if($count_resp>0) {
			$data->id = $matches[1][0];
		}else{
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$url = 'https://go.qtelecom.ru/public/http/';
		
		$params = array(
			'user' => $this->config->login,
			'pass' => $this->config->passw,
			'action' => 'status',
			'sms_id' => $smsid
		);
		
		$response = $this->openHttp($url, true, $params);
		
		$data = new \stdClass();
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$error = $this->checkError($response);
		if($error) {
			$data->error = 'CODE '.$error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		$count_resp = preg_match_all('/<SMSSTC_CODE>(.*)<\/SMSSTC_CODE>/Ui',$response, $matches);
		
		if($count_resp>0) {
			$data->status = $this->_checkStatus($matches[1][0]);
		}else{
			$data->error = $error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		return $data;
		
	}
	
	private function openHttp($url, $post = false, $params = null) {
		
		if($post === false) {
			$httpClient = new \Bitrix\Main\Web\HttpClient();
			$result = $httpClient->get($url);
		}else{
			$httpClient = new \Bitrix\Main\Web\HttpClient(array('charset'=>'utf-8'));
			$httpClient->setHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8', true);
			$result = $httpClient->post($url,$params);
		}
		
		return $result;
		
	}
	
	private function getConfig($params) {
		
		$c = new \stdClass();
		$c->login = $params['login'];
		$c->passw = $params['passw'];
		$c->sender = $params['sender'];
		$c->charset = $params['charset'];
		
		return $c;
		
	}
	
	private function checkError($resp) {
		$count = preg_match_all('/<error code="([0-9-]+)"/Ui',$resp, $matches);
		if($count>0) {
			$error = $matches[1][0];
			return $error;
		}
		return false;
	}
	
	private function chechErrorCode($code) {
		
		if($code=='-20170') return 7;
		if($code=='-20107') return 2;
		if($code=='-20170') return 1;
		if($code=='-20171') return 1;
		if($code=='-20158') return 8;
		if($code=='-20167') return 1;
		if($code=='-20144') return 6;
		if($code=='-20147') return 6;
		if($code=='-20174') return 6;
		if($code=='-20154') return 6;
		if($code=='-20148') return 6;
		return 9999;
	
	}
	
	private function _checkStatus($code) {
	
		if($code=='queued') return 6;
		if($code=='wait') return 3;
		if($code=='accepted') return 6;
		if($code=='delivered') return 4;
		if($code=='not_delivered') return 7;
		if($code=='failed') return 7;
		
		return false;
		
	}
	
}