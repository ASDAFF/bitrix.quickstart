<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Smsgkru{
	
	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		$url = 'https://api.smsgk.ru/api/get_senderlist/?username='.$this->config->login.'&password='.$this->config->passw;
		
		$response = $this->openHttp($url);
		
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
		
		$dAr = explode("\n",$response);
		if(count($dAr)>0){
			foreach($dAr as $sender) {
				$senderAr = explode(":",$sender);
				if($senderAr[1]=='ACCEPTED'){
				$ob = new \stdClass();
				$ob->sender = $senderAr[0];
				$arr[] = $ob;
				}
			}
			$data = $arr;
		}else{
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
		
		$url = 'https://api.smsgk.ru/api/send_sms/?username='.$this->config->login.'&password='.$this->config->passw.'&from='.$sender.'&to='.$phones.'&text='.$mess;
		
		$response = $this->openHttp($url);
		
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
		
		$dAr = explode("\n",$response);
		
		if(count($dAr)>0){
			foreach($dAr as $sms){
				$arSms = explode(":",$sms);
				$data->id = $arSms[1];
				$data->cnt = 1;
				$data->cost = '';
				$data->balance = '';
				break;
			}
			return $data;
		}else{
			$data->error = $error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
	
	}
	
	public function _getBalance () {

		$url = 'https://api.smsgk.ru/api/get_balance/?username='.$this->config->login.'&password='.$this->config->passw;
		
		$response = $this->openHttp($url);
		
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
		
		$count_resp = preg_match_all('/<money.*>(.*)<\/money>/Ui',$response, $matches);
		
		if($response) {
			$data->balance = $response;
		}
		
		return $data;
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		
		$url = 'https://api.smsgk.ru/api/get_smsstatus/?username='.$this->config->login.'&password='.$this->config->passw.'&smsid='.$smsid;
		
		$response = $this->openHttp($url);

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
		
		$dAr = explode("\n",$response);
		if(count($dAr)>0){
			foreach($dAr as $sms) {
				if($this->_checkStatus($smsAr[1])){
					$smsAr = explode(":",$sms);
					$data->last_timestamp = time();
					$data->status = $this->_checkStatus($smsAr[1]);
					return $data;
					break;
				}
			}
		}
		
		$data->error = 'Error';
		$data->error_code = '9999';
		return $data;
		
		
	}
	
	private function getConfig($params) {
		
		$c = new \stdClass();
		if(strpos($params['login'],"||")!==false){
			$arPrm = explode("||",$params['login']);
		}else{
			$arPrm = array('my5.t-sms.ru',$params['login']);
		}
		$c->login = $arPrm[1];
		$c->server = $arPrm[0];
		$c->passw = $params['passw'];
		$c->sender = $params['sender'];
		$c->charset = $params['charset'];
		
		return $c;
		
	}
	
	private function openHttp($url) {
	
		$httpClient = new \Bitrix\Main\Web\HttpClient();
		$httpClient->setHeader('Content-Type', 'text/xml; charset=utf-8', true);
		$result = $httpClient->get($url);
		
		return $result;
		
	}
	
	private function checkError($resp) {
		if(strpos($resp,"ERROR")!==false){
			$error = str_replace("ERROR=","",$resp);
			return $error;
		}
		return false;
	}
	
	private function chechErrorCode($code) {
		
		$arCode = explode(':',$code);
		if(count($arCode) != 2) return 9999;
		
		if($arCode[0] == 101) return 2;
		if($arCode[0] == 102) return 1;
		if($arCode[0] == 103) return 1;
		if($arCode[0] == 104) return 1;
		if($arCode[0] == 201) return 1;
		if($arCode[0] == 202) return 1;
		if($arCode[0] == 203) return 1;
		if($arCode[0] == 204) return 1;
		if($arCode[0] == 301) return 1;
		if($arCode[0] == 302) return 1;
		if($arCode[0] == 801) return 2;
		if($arCode[0] == 802) return 1;
		if($arCode[0] == 803) return 1;
		if($arCode[0] == 804) return 1;
		
		return 9999;
	
	}
	
	private function _checkStatus($code) {
	
		if($code=='NOT DELIVERED') return 7;
		if($code=='EXPIRED') return 5;
		if($code=='DELIVERED') return 4;
		if($code=='PARTLY DELIVERED') return false;
		return false;
		
	}
	
}
?>