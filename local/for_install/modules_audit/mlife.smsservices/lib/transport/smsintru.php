<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Smsintru{
	
	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	public function _getAllSender() {
		
		
		$url = 'https://'.$this->config->server.'/lcabApi/info.php?login='.$this->config->login.'&password='.$this->config->passw;
		
		$response = $this->openHttp($url);
		
		$data = new \stdClass();
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$response = json_decode($response);
		
		if($response->code!=1) {
			$data->error = $response->code;
			$data->error_code = $this->chechErrorCode($response->code);
			return $data;
		}
		
		if(!empty($response->source)){
			$data = array();
			foreach($response->source as $sender){
				$ob = new \stdClass();
				$ob->sender = $sender;
				$data[] = $ob;
			}
		}
		//print_r($data);die();
		
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
		<data>
		<login>'.$this->config->login.'</login>
		<password>'.$this->config->passw.'</password>
		<action>send</action>
		<text>'.$mess.'</text>
		<source>'.$sender.'</source>
		<to number="'.$phones.'"></to>
		</data>';
		
		$url = 'https://'.$this->config->server.'/API/XML/send.php';
		
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
		
		$count_resp = preg_match_all('/<smsid>(.*)<\/smsid>/Ui',$response, $matches);
		
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
		<data>
			<login>'.$this->config->login.'</login>
			<password>'.$this->config->passw.'</password>
		</data>';

		$url = 'https://'.$this->config->server.'/API/XML/balance.php';
		
		$response = $this->openHttp($url, $xml);
		
		$data = new \stdClass();
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$error = $this->checkError($response);
		//print_r($error);die();
		if($error) {
			$data->error = $error;
			$data->error_code = $this->chechErrorCode($error);
			return $data;
		}
		
		$count_resp = preg_match_all('/<account>(.*)<\/account>/Ui',$response, $matches);
		
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
		<data>
		<login>'.$this->config->login.'</login>
		<password>'.$this->config->passw.'</password>
		<smsid>'.$smsid.'</smsid>
		</data>';
		
		$url = 'https://'.$this->config->server.'/API/XML/report.php';
		
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
		
		$response = str_replace("\n",'',$response);
		
		//print_r($response);
		//die();
		if($this->_checkStatus($response)){
			$data->last_timestamp = time();
			$data->status = $this->_checkStatus($response);
			return $data;
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
			$arPrm = array('lcab.smsint.ru',$params['login']);
		}
		$c->login = $arPrm[1];
		$c->server = $arPrm[0];
		$c->passw = $params['passw'];
		$c->sender = $params['sender'];
		$c->charset = $params['charset'];
		
		return $c;
		
	}
	
	private function openHttp($url, $xml='', $method = false, $params = null) {
	
		if (!function_exists('curl_init')) {
		    die('ERROR: CURL library not found!');
		}
		
		if(!$xml) {
			$httpClient = new \Bitrix\Main\Web\HttpClient();
			return $httpClient->get($url);
		}else{
		
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/xml; charset=utf-8' ) );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_CRLF, true );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, true );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
		
		}

		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
		
	}
	
	private function checkError($resp) {
		$count = preg_match_all('/<code>(.*)<\/code>/Ui',$resp, $matches);
		if($count>0) {
			if($this->config->charset=='windows-1251') {
				$error = $GLOBALS['APPLICATION']->ConvertCharset($matches[1][0], 'UTF-8', SITE_CHARSET);
			}else{
				$error = $matches[1][0];
			}
			if($error != 1) return $error;
		}
		return false;
	}
	
	/*
	500	Недостаточно переданных параметров (связана обычно с тем, что не передан какой-то параметр, обязательный для рассылки
	501	Не удалось авторизоваться
	502	Недопустимое значение Адреса отправителя (связана обычно с тем, что задан не GSM формат подписи, либо подпись не одобрена в личном кабинете)
	510	Отсутствуют получатели (связана обычно с тем, что не передан ни один получатель)
	511	Ваша учетная запись заблокирована
	512	Модуль отправки СМС не подключен
	516	Недостаточно средств для выполнения рассылки.
	517	СМС не будет доставлена ни одному получателю (связана обычно с тем, что номера не из мобильной сети, либо направление отключено в тарифном плане)
	518	Недопустимое значение даты/времени
	519	Использование цифровой подписи на дорогом канале недопустимо.
	7хх	Ошибка парсера XML (возникает, когда присылается некорректная XML)
	*/
	
	private function chechErrorCode($code) {
		
		if($code==500) return 1;
		if($code==501) return 2;
		if($code==502) return 6;
		if($code==510) return 7;
		if($code==511) return 2;
		if($code==512) return 1;
		if($code==516) return 3;
		if($code==517) return 6;
		if($code==518) return 1;
		if($code==519) return 6;
		
		return 9999;
	
	}
	
	/*
	* Приводим коды статусов в 1 вид
	1 - ожидает отправки с сайта
	2 - передано на шлюз
	3 - передано оператору
	4 - доставлено
	5 - просрочено
	6 - ожидает отправки на шлюзе
	7 - невозможно доставить
	8 - неверный номер
	9 - запрещено на сервисе
	10 - недостаточно средств
	11 - недоступный номер
	12 - ошибка при отправке смс
	*/
	private function _checkStatus($code) {
		
		if(strpos($code,'<onModer><number>')!==false) return 6;
		if(strpos($code,'<delivered><number>')!==false) return 4;
		if(strpos($code,'<notDelivered><number>')!==false) return 7;
		if(strpos($code,'<waiting><number>')!==false) return 6;
		if(strpos($code,'<enqueued><number>')!==false) return 3;
		if(strpos($code,'<cancel><number>')!==false) return 7;
		if(strpos($code,'<process><number>')!==false) return 3;
		
		return false;
		
	}
	
}
?>