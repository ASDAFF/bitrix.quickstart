<?php
namespace Mlife\Smsservices\Transport;

class P1smsru{

	private $config;
	
	//�����������, �������� ������ ������� � �����
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getBalance () {
		
		$url = 'http://95.213.129.83/xml/balance.php';
		
		$xml = '<?xml  version="1.0" encoding="utf-8" ?><request><security><login value="'.$this->config->login.'" /><password value="'.$this->config->passw.'" /></security></request>';
		
		$response = $this->openHttp($url, true, $xml);
		
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
		
		if($count_resp>0) {
			$data->balance = $matches[1][0];
		}else{
			$data->error = $error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		return $data;
		
	}
	
	public function _getAllSender() {
		
		$xml = '<?xml version="1.0" encoding="utf-8" ?><request><security><login value="'.$this->config->login.'" /><password value="'.$this->config->passw.'" /></security></request>';
		
		$url = 'http://95.213.129.83/xml/originator.php';
		
		$response = $this->openHttp($url, true, $xml);
		
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
		
		$count_resp = preg_match_all('/<originator state="completed">(.*)<\/originator>/Ui',$response, $matches);
		
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
			$charset = 'cp1251';
			$mess = iconv("CP1251", "UTF-8", $mess);
		}
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<security><login value="'.$this->config->login.'" /><password value="'.$this->config->passw.'" /></security>
		<message type="sms">
			<sender>'.$sender.'</sender>
			<text>'.$mess.'</text>
			<abonent phone="'.$phones.'"/>
		</message>
		</request>';
		
		$url = 'http://95.213.129.83/xml/';
		
		$response = $this->openHttp($url, true, $xml);
		
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
		
		$count_resp_err = preg_match_all('/<information number_sms="">(.*)<\/information>/Ui',$response, $matches_err);
		if($count_resp_err>0) {
			$err = iconv("UTF-8", "CP1251", $matches_err[1][0]);
			$data->error = $err;
			$data->error_code = $this->chechErrorCode($err);
			return $data;
		}
		
		$count_resp = preg_match_all('/<information.*id_sms="(.*)".*parts="(.*)">(.*)<\/information>/Ui',$response, $matches);
		
		if($count_resp>0){
			$data->id = $matches[1][0];
			$data->cnt = $matches[2][0];
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
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<security><login value="'.$this->config->login.'" /><password value="'.$this->config->passw.'" /></security>
		<get_state>
			<id_sms>'.$smsid.'</id_sms>
		</get_state>
		</request>';
		
		$url = 'http://95.213.129.83/xml/state.php';
		
		$response = $this->openHttp($url, true, $xml);

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
		
		$count_resp = preg_match_all('/<state.*time="(.*)".*>(.*)<\/state>/Ui',$response, $matches);
		
		if($count_resp>0){
			if($this->_checkStatus($matches[2][0])){
			$data->last_timestamp = strtotime($matches[1][0]);
			$data->status = $this->_checkStatus($matches[2][0]);
			return $data;
			}
		}
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
	}	
	
	private function openHttp($url, $post = false, $params = null) {
		
		if($post === false) {
			$httpClient = new \Bitrix\Main\Web\HttpClient();
			$result = $httpClient->get($url);
		}else{
			$httpClient = new \Bitrix\Main\Web\HttpClient(array('charset'=>'utf-8'));
			$httpClient->setHeader('Content-Type', 'text/xml', true);
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
		$count = preg_match_all('/<error>(.*)<\/error>/Ui',$resp, $matches);
		if($count>0) {
			if($this->config->charset=='windows-1251') {
				$error = iconv("UTF-8", "CP1251", $matches[1][0]);
			}else{
				$error = $matches[1][0];
			}
			return $error;
		}
		return false;
	}
	
	private function chechErrorCode($code) {
		
		if($code=='������������ ����� ��� ������') return 2;
		if($code=='������������ ������ XML ���������') return 1;
		if($code=='��� ������� ������������') return 2;
		if($code=='POST ������ �����������') return 1;
		if($code=='� ��� ����������� SMS. ��� ���������� �������� ��������� � ����������.') return 3;
		if($code=='����������� SMS.') return 3;
		if($code=='������� ������������.') return 2;
		if($code=='������� ����� ��������.') return 1;
		if($code=='����� �������� ������������ � ����-�����.') return 8;
		if($code=='������ ����������� ������� ��� ���.') return 6;
		if($code=='������ ����������� �������.') return 6;
		if($code=='����� SMS �������� �����������.') return 6;
		if($code=='��� �����������.') return 6;
		if($code=='����������� �� ������ ��������� 15 �������� ��� �������� ������� � 11 �������� ��� ��������-��������.') return 6;
		if($code=='����� �������� ������ ���� ������ 15 ��������.') return 7;
		if($code=='��� ������ ���������.') return 1;
		if($code=='��� ������.') return 1;
		if($code=='������� �������� �������� � ���� �� ���� �������� ��� �������� ��������.') return 1;
		if($code=='������ ����������� ���.') return 6;
		if($code=='����������� �� ������ ���������.') return 6;
		return 9999;
	
	}
	
	private function _checkStatus($code) {
	
		if($code=='send') return 3;
		if($code=='not_deliver') return 7;
		if($code=='expired') return 5;
		if($code=='deliver') return 4;
		if($code=='partly_deliver') return false;
		return false;
		
	}
	
}