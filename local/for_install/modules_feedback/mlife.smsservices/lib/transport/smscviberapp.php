<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Smscviberapp{
	
	private $config;
	
	//конструктор, получаем данные доступа к шлюзу
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	/**********************************************************
	*   Метод для получения списка отправителей
	***********************************************************
	*	error - { "error": "authorise error", "error_code": 2 }
	*	error_code=1 - Ошибка в параметрах.
	*	error_code=2 - Неверный логин или пароль.
	*	error_code=4 - IP-адрес временно заблокирован.
	*	error_code=9 - Попытка отправки более трех одинаковых запросов на получение списка доступных имен отправителей в течение минуты.
	***********************************************************
	*	ok - [{"sender": "<sender>"},...]
	***********************************************************/
	public function _getAllSender() {
		
		$url = 'http://smsc.ru/sys/get.php?get_senders=1&login='.$this->config->login.'&psw='.$this->config->passw.'&fmt=3';
		$response = $this->openHttp($url);
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		return json_decode($response);
	}
	
	/**
	* Метод для отправки смс
	* @param string     		$phones    номер телефона
	* @param string     		$mess      текст сообщения
	* @param string     		$time      время в UNIXTIME, 0-по умолчанию
	* @param boolean, string   $sender    false - взять из настроек, либо переданный отправитель
	* @return array    		Результат запроса
	***********************************************************
	*	error - { "error": "authorise error", "error_code": 2 }
	*	error_code=1 - Ошибка в параметрах.
	*	error_code=2 - Неверный логин или пароль.
	*	error_code=3 - Недостаточно средств на счете Клиента.
	*	error_code=4 - IP-адрес временно заблокирован из-за частых ошибок в запросах.
	*	error_code=5 - Неверный формат даты.
	*	error_code=6 - Сообщение запрещено (по тексту или по имени отправителя).
	*	error_code=7 - Неверный формат номера телефона.
	*	error_code=8 - Сообщение на указанный номер не может быть доставлено.
	*	error_code=9 - Отправка более одного одинакового запроса на передачу SMS-сообщения либо более пяти одинаковых запросов на получение стоимости сообщения в течение минуты.
	***********************************************************
	*	ok - {"id": <id>,"cnt": <n>, "cost": "<cost>", "balance": "<balance>"} - ид сообщения, количество смс, стоимость рассылки, новый баланс
	***********************************************************
	*/
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
		
		if($time!=0 && $time>time()) {
			$time = '0'.$time;
		}
		else{
			$time = 0;
		}
		
		$phones = urlencode($phones);
		$mess = urlencode($mess);
		$charset = $this->config->charset;
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$pp = '327698';
		$url =  'http://smsc.ru/sys/send.php?login='.$this->config->login.'&psw='.$this->config->passw.'&phones='.$phones.'&mes='.$mess.
				'&fmt=3&charset='.$charset.'&time='.$time.'&sender='.$sender.'&cost=3&viber=1&pp='.$pp;
		$response = $this->openHttp($url);
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		return json_decode($response);
		
	}
	
	/**********************************************************
	*   метод для получения баланса
	***********************************************************
	*	error - { "error": "authorise error", "error_code": 2 }
	*	error_code=1 - Ошибка в параметрах.
	*	error_code=2 - Неверный логин или пароль.
	*	error_code=4 - IP-адрес временно заблокирован.
	*	error_code=9 - Попытка отправки более десяти запросов на получение баланса в течение минуты.
	***********************************************************
	*	ok - { "balance": "178.10" }
	***********************************************************/
	public function _getBalance () {
	
		$url = 'http://smsc.ru/sys/balance.php?login='.$this->config->login.'&psw='.$this->config->passw.'&fmt=3';
		$response = $this->openHttp($url);

		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		return json_decode($response);
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$url = 'http://smsc.ru/sys/status.php?login='.$this->config->login.'&psw='.$this->config->passw.'&phone='.$phone.'&id='.$smsid.'&fmt=3';
		$response = $this->openHttp($url);

		if(!$response){
			$data = new stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$data = json_decode($response);
		
		if($data->error_code) return $data;
		
		$res = new \stdClass();
		$res->last_timestamp = $data->last_timestamp;
		$res->last_timestamp = time();
		$res->status = $this->_checkStatus($data->status);
		return $res;
		
	}
	
	/**
	 * получаем данные доступа к шлюзу из настроек модуля
	 */
	private function getConfig($params) {
		
		$c = new \stdClass();
		$c->login = $params['login'];
		$c->passw = $params['passw'];
		$c->sender = $params['sender'];
		$c->charset = $params['charset'];
		
		return $c;
		
	}
	
	/**
	* Метод для отправки запросов
	* @param string     $url    УРЛ
	* @param boolean     $method    false - GET, true - POST
	* @param string     $params    Параметры для POST запроса
	* @return string    Результат запроса
	*/
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
	
		if($code==-1) return 2;
		if($code==0) return 3;
		if($code==1) return 14;
		if($code==2) return 14;
		if($code==3) return 5;
		if($code==20) return 7;
		if($code==22) return 8;
		if($code==23) return 9;
		if($code==24) return 10;
		if($code==25) return 11;
		
	}
	
}
?>