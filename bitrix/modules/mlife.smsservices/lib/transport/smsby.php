<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Smsby{
	
	private $config;
	
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		return array();
	}
	
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
		
		$phones = preg_replace('/([^0-9])/','',$phones);
		$charset = $this->config->charset;
		//$sender = 103;
		$mess_old = $mess;
		
		if($charset=='windows-1251') {
			$mess = $GLOBALS['APPLICATION']->ConvertCharset($mess, $charset, 'UTF-8');
		}
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		if($sender){
		$url = 'http://sms.unisender.by/api/v1/createSmsMessage?token='.$this->config->passw.'&message='.$mess.'&alphaname_id='.$sender;
		}else{
		$url = 'http://sms.unisender.by/api/v1/createSmsMessage?token='.$this->config->passw.'&message='.$mess;
		}
		$response = $this->openHttp($url);
		//print_r($response);
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$response = json_decode($response);
		
		if($this->config->debug){
		\CEventLog::Add(array(
			"SEVERITY" => "SECURITY",
			"AUDIT_TYPE_ID" => "MY_IMPORT",
			"MODULE_ID" => "mlife.smsservices",
			"ITEM_ID" => 1,
			"DESCRIPTION" => $mess_old.' - '.$phones.' - '.print_r($response,true),
		));
		}
		
		//print_r($response);
		if(is_array($response)) $response = $response[0];
		if($response->error && !$response->message_id){
			$data = new \stdClass();
			$data->error = $response->error;
			$data->error_code = '9998';
			return $data;
		}
		
		if(is_array($response)) $response = $response[0];
		if($response->message_id){
			$messId = $response->message_id;
			
			$url = 'http://sms.unisender.by/api/v1/sendSms?token='.$this->config->passw.'&message_id='.$messId.'&phone='.$phones;
			$response = $this->openHttp($url);
			//print_r($response);
			
			if(!$response){
				$data = new \stdClass();
				$data->error = 'Service is not available';
				$data->error_code = '9998';
				return $data;
			}
			
			$response = json_decode($response);
			if(is_array($response)) $response = $response[0];
			if($this->config->debug){
			\CEventLog::Add(array(
				"SEVERITY" => "SECURITY",
				"AUDIT_TYPE_ID" => "MY_IMPORT",
				"MODULE_ID" => "mlife.smsservices",
				"ITEM_ID" => 1,
				"DESCRIPTION" => $mess_old.' - '.$phones.' - '.print_r($response,true),
			));
			}
			
			if($response->error){
				$data = new \stdClass();
				$data->error = $response->error;
				$data->error_code = '9998';
				return $data;
			}
			
			if($response->sms_id){
				$data = new \stdClass();
				$data->id = $response->sms_id;
				return $data;
			}else{
				$data = new \stdClass();
				$data->error = 'Service is not available';
				$data->error_code = '9998';
				return $data;
			}
			
			
		}else{
			
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
			
		}
		
		
	}
	
	public function _getBalance() {
		
		$url = 'http://sms.unisender.by/api/v1/getLimit?token='.$this->config->passw;
		$response = $this->openHttp($url);

		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$response = json_decode($response);
		if($response->error){
			$data = new \stdClass();
			$data->error = $response->error;
			$data->error_code = '9998';
			return $data;
		}
		
		$data = new \stdClass();
		$data->balance = $response->limit;
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$url = 'http://sms.unisender.by/api/v1/checkSMS?token='.$this->config->passw.'&sms_id='.$smsid;
		
		$response = $this->openHttp($url);

		if(!$response){
			$data = new stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$response = json_decode($response);
		if(is_array($response)) $response = $response[0];
		if($this->config->debug){
		\CEventLog::Add(array(
			"SEVERITY" => "SECURITY",
			"AUDIT_TYPE_ID" => "MY_IMPORT",
			"MODULE_ID" => "mlife.smsservices",
			"ITEM_ID" => 1,
			"DESCRIPTION" => $smsid.' - '.print_r($response,true),
		));
		}
		if($response->error){
			$data = new \stdClass();
			$data->error = $response->error;
			$data->error_code = '9998';
			return $data;
		}
		
		if($response->delivered){
			$res = new \stdClass();
			$res->last_timestamp = $response->delivered;
			$res->status = 4;
			return $res;
		}else{
			$res = new \stdClass();
			$res->last_timestamp = time();
			$res->status = 12;
			return $res;
		}
		
	}
	
	private function getConfig($params) {
		
		$c = new \stdClass();
		$c->login = $params['login'];
		$c->passw = $params['passw'];
		$c->sender = $params['sender'];
		$c->charset = $params['charset'];
		$c->debug = true;
		
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
?>