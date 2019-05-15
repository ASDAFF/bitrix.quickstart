<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Smsflyua{
	
	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<operation>MANAGEALFANAME</operation><command id="GETALFANAMESLIST"/>
		</request>';
		
		$url = 'http://sms-fly.com/api/api.php';
		
		$response = $this->openHttp($url, $xml);
		
		$data = new \stdClass();
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$error = $this->checkError($response);
		if($error) {
			$data->error = $error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		$count_resp = preg_match_all('/<state alfaname="([A-z0-9]+)".*\/>/Ui',$response, $matches);
		
		if($count_resp>0 && is_array($matches[1])) {
			foreach($matches[1] as $sender) {
				$ob = new \stdClass();
				$ob->sender = $sender;
				$arr[] = $ob;
			}
			$data = $arr;
		}
		else{
			$data->error = 'Error';
			$data->error_code = '9999';
			return $data;
		}
		
		return $data;
	}
	
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
	
		$data = new \stdClass();

		$phones = preg_replace("/[^0-9A-Za-z]/", "", $phones);
		$charset = $this->config->charset;
		if($charset=='windows-1251') {
			$mess = $GLOBALS['APPLICATION']->ConvertCharset($mess, SITE_CHARSET, 'UTF-8');
		}
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<operation>SENDSMS</operation>
		<message start_time="AUTO" end_time="AUTO" lifetime="24" rate="120" desc="" source="'.$sender.'">
		<body>'.$mess.'</body> 
		<recipient>'.$phones.'</recipient>
		</message>
		</request>';
		
		$url = 'http://sms-fly.com/api/api.php';
		
		$response = $this->openHttp($url, $xml);
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$error = $this->checkError($response);
		if($error) {
			$data->error = $error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		$count_resp = preg_match_all('/<state code="ACCEPT" campaignID="([0-9]+)".*<\/state>/Ui',$response, $matches);
		
		if($count_resp>0){
			$data->id = $matches[1][0];
			$data->cnt = '';
			$data->cost = '';
			$data->balance = '';
			return $data;
		}
		else{
			$data->error = $error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
	
	}
	
	public function _getBalance () {
	
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<operation>GETBALANCE</operation>
		</request>';

		$url = 'http://sms-fly.com/api/api.php';
		
		$response = $this->openHttp($url, $xml);
		
		$data = new \stdClass();
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$error = $this->checkError($response);
		//print_r($response);die();
		if($error) {
			$data->error = $error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		$count_resp = preg_match_all('/<balance>(.*)<\/balance>/Ui',$response, $matches);
		
		if($count_resp>0) {
			$data->balance = $matches[1][0];
		}else{
			$data->error = $error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<operation>GETCAMPAIGNDETAIL</operation>
		<message campaignID="'.$smsid.'"/>
		</request>';
		
		$url = 'http://sms-fly.com/api/api.php';
		
		$response = $this->openHttp($url, $xml);

		$data = new \stdClass();
		
		if(!$response){
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$error = $this->checkError($response);
		
		if($error) {
			$data->error = $error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		$count_resp = preg_match_all('/<message.*status="([0-9A-z]+)".*modifyDateTime="(.*)"><\/message>/Ui',$response, $matches);
		
		if($count_resp>0){
			if($this->_checkStatus($matches[1][0])){
			$data->last_timestamp = strtotime($matches[2][0]);
			$data->status = $this->_checkStatus($matches[1][0]);
			return $data;
			}
		}
			$data->error = 'Service is not available';
			$data->error_code = '9998';
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
	
	private function openHttp($url, $xml) {
		$user = $this->config->login;
		$password = $this->config->passw;
		if (!function_exists('curl_init')) {
		    die('ERROR: CURL library not found!');
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERPWD , $user.':'.$password);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/xml; charset=utf-8' ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_CRLF, true );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );

		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
		
	}
	
	private function checkError($resp) {
		if(strpos($resp,'Access denied') !== false) return 'Access denied';
		$count = preg_match_all("/<state code=\"([A-z]+)\".*<\/state>/Ui",$resp, $matches);
		if($count>0) {
			$error = $matches[1][0];
			if($error != 'ACCEPT'){
				return $error;
			}
		}
		return false;
	}
	
	private function chechErrorCode($code) {
		$code = trim($code);
		//if(strpos($code,$GLOBALS['APPLICATION']->ConvertCharset('логин','UTF-8',SITE_CHARSET))!==false) return 2;
		//if(strpos($code,'XML')!==false) return 1;
		//if(strpos($code,'POST')!==false) return 1;
		//print_r($code);die();
		if($code=='Access denied') return 2;
		if($code=='XMLERROR') return 1;
		if($code=='ERRPHONES') return 1;
		if($code=='ERRSTARTTIME') return 1;
		if($code=='ERRENDTIME') return 1;
		if($code=='ERRLIFETIME') return 1;
		if($code=='ERRSPEED') return 1;
		if($code=='ERRALFANAME') return 6;
		if($code=='ERRTEXT') return 1;
		if($code=='INSUFFICIENTFUNDS') return 3;
		
		return 9999;
	
	}
	
	private function _checkStatus($code) {
	
		if($code=='PENDING') return 2;
		if($code=='SENT') return 3;
		if($code=='DELIVERED') return 4;
		if($code=='EXPIRED') return 5;
		if($code=='UNDELIV') return 7;
		if($code=='STOPED') return 10;
		if($code=='ERROR') return 12;
		if($code=='USERSTOPED') return 12;
		return false;
		
	}
	
}
?>