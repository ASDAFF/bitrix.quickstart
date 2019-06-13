<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Ibatele{

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
		
		if(strpos($params['login'],"||")!==false){
			$arPrm = explode("||",$params['login']);
		}else{
			$arPrm = array('lk.ibatele.com',$params['login']);
		}
		
		$c->server = $arPrm[0];
		$c->login = $arPrm[1];
		
		return $c;
		
	}
	
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
	
		$data = new \stdClass();

		$phones = preg_replace("/[^0-9A-Za-z]/", "", $phones);
		
		if(SITE_CHARSET=='windows-1251') {
			$mess = $GLOBALS['APPLICATION']->ConvertCharset($mess, SITE_CHARSET, 'UTF-8');
		}
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<message type="sms">
			<sender>'.$sender.'</sender>
			<text>'.$mess.'</text>
			<abonent phone="'.$phones.'" number_sms="1" time_send=""/>
		</message>
		<security>
		<login value="'.$this->config->login.'" />
		<password value="'.$this->config->passw.'" />
		</security>
		</request>';
		
		$url = 'http://'.$this->config->server.'/xml/';
		//$xml = '';
		$response = $this->openHttp($url, $xml);
		//file_put_contents($_SERVER["DOCUMENT_ROOT"].'/test.txt',print_r($response,true));
		//echo'<pre>';echo'test';print_r($response);echo'</pre>';
		//echo'<pre>';print_r($xml);echo'</pre>';
		
		//die();
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
			if($this->config->charset=='windows-1251') {
				$err = iconv("UTF-8", "CP1251", $matches_err[1][0]);
			}else{
				$err = $matches_err[1][0];
			}
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
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
	
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		//$smsid = '686125109';
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<security>
		<login value="'.$this->config->login.'" />
		<password value="'.$this->config->passw.'" />
		</security>
		<get_state>
			<id_sms>'.$smsid.'</id_sms>
		</get_state>
		</request>';
		
		$url = 'http://'.$this->config->server.'/xml/state.php';
		
		$response = $this->openHttp($url, $xml);
		//$response = $GLOBALS['APPLICATION']->ConvertCharset($response,  'UTF-8', SITE_CHARSET);
		//print_r($response);die();

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
		
		$count_resp = preg_match_all('/<state.*time="(.*?)">(.*)<\/state>/Ui',$response, $matches);
		
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
	
	public function _getAllSender() {
		//$this->_getStatusSms(false);
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<security>
		<login value="'.$this->config->login.'" />
		<password value="'.$this->config->passw.'" />
		</security>
		</request>';
		
		$url = 'http://'.$this->config->server.'/xml/originator.php';
		
		$response = $this->openHttp($url, $xml);
		
		//if(SITE_CHARSET=='windows-1251') {
		//	$response = $GLOBALS['APPLICATION']->ConvertCharset($response,  'UTF-8', SITE_CHARSET);
		//}
		
		//echo'<pre>';print_r($xml);echo'</pre>';
		//print_r($response);
		//die();
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
			//print_r($data);die();
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
	
	public function _getBalance () {

		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<security>
		<login value="'.$this->config->login.'" />
		<password value="'.$this->config->passw.'" />
		</security>
		</request>';

		$url = 'http://'.$this->config->server.'/xml/balance.php';
		
		$response = $this->openHttp($url, $xml);
		//print_r($response);die();
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
	
	private function openHttp($url, $xml) {
	
		if (!function_exists('curl_init')) {
		    die('ERROR: CURL library not found!');
		}

		$ch = curl_init();
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
		
		if($code=='Неправильный логин или пароль') return 2;
		if($code=='Неправильный формат XML документа') return 1;
		if($code=='Ваш аккаунт заблокирован') return 2;
		if($code=='POST данные отсутствуют') return 1;
		if($code=='У нас закончились SMS. Для разрешения проблемы свяжитесь с менеджером.') return 3;
		if($code=='Закончились SMS.') return 3;
		if($code=='Аккаунт заблокирован.') return 2;
		if($code=='Укажите номер телефона.') return 1;
		if($code=='Номер телефона присутствует в стоп-листе.') return 8;
		if($code=='Данное направление закрыто для вас.') return 6;
		if($code=='Данное направление закрыто.') return 6;
		if($code=='Текст SMS отклонен модератором.') return 6;
		if($code=='Нет отправителя.') return 6;
		if($code=='Отправитель не должен превышать 15 символов для цифровых номеров и 11 символов для буквенно-числовых.') return 6;
		if($code=='Номер телефона должен быть меньше 15 символов.') return 7;
		if($code=='Нет текста сообщения.') return 1;
		if($code=='Нет ссылки.') return 1;
		if($code=='Укажите название контакта и хотя бы один параметр для визитной карточки.') return 1;
		if($code=='Такого отправителя нет.') return 6;
		if($code=='Отправитель не прошел модерацию.') return 6;
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
?>