<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Targetsmsru{
	
	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<security>
			<login value="'.$this->config->login.'" />
			<password value="'.$this->config->passw.'" />
		</security>
		</request>';
		
		$url = 'https://'.$this->config->server.'/xml/originator.php';
		
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
		
		$count_resp = preg_match_all('/<originator state="[A-z]+">(.*)<\/originator>/Ui',$response, $matches);
		
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
		<security>
			<login value="'.$this->config->login.'" />
			<password value="'.$this->config->passw.'" />
		</security>
		<message type="sms">
			<sender>'.$sender.'</sender>
			<text>'.$mess.'</text>
			<abonent phone="'.$phones.'"/>
		</message>
		</request>';
		
		$url = 'https://'.$this->config->server.'/xml/';
		
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
		
		$count_resp_err = preg_match_all('/<information number_sms="">(.*)<\/information>/Ui',$response, $matches_err);
		if($count_resp_err>0) {
			$err = $GLOBALS['APPLICATION']->ConvertCharset($matches_err[1][0], 'UTF-8', SITE_CHARSET);
			$data->error = $err;
			$data->error_code = $this->chechErrorCode($matches_err[1][0]);
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
	
	public function _getBalance () {
	
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
		<request>
		<security>
			<login value="'.$this->config->login.'" />
			<password value="'.$this->config->passw.'" />
		</security>
		</request>';

		$url = 'https://'.$this->config->server.'/xml/balance.php';
		
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
	
	public function _getStatusSms($smsid,$phone=false) {
		
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
		
		$url = 'https://'.$this->config->server.'/xml/state.php';
		
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
	
	private function getConfig($params) {
		
		$c = new \stdClass();
		if(strpos($params['login'],"||")!==false){
			$arPrm = explode("||",$params['login']);
		}else{
			$arPrm = array('sms.targetsms.ru',$params['login']);
		}
		$c->login = $arPrm[1];
		$c->server = $arPrm[0];
		$c->passw = $params['passw'];
		$c->sender = $params['sender'];
		$c->charset = $params['charset'];
		
		return $c;
		
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
				$error = $GLOBALS['APPLICATION']->ConvertCharset($matches[1][0], 'UTF-8', SITE_CHARSET);
			}else{
				$error = $matches[1][0];
			}
			return $error;
		}
		return false;
	}
	
	private function chechErrorCode($code) {
		
		if(strpos($code,$GLOBALS['APPLICATION']->ConvertCharset('логин','UTF-8',SITE_CHARSET))!==false) return 2;
		if(strpos($code,'XML')!==false) return 1;
		if(strpos($code,'POST')!==false) return 1;
		
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