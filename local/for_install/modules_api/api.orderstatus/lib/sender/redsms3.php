<?
//https://cp.redsms.ru/reference/api
namespace Api\OrderStatus\Sender;

use Bitrix\Main\Application;
use Bitrix\Main\Result;
use Bitrix\Main\Text;
use Bitrix\Main\Error;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Redsms3
 *
 * @package Api\OrderStatus\Sender
 */
class Redsms3
{
	const BASE_CLASS = 'Redsms3';
	const BASE_URL   = 'https://cp.redsms.ru/api';

	const SMS_TYPE    = 'sms';
	const VIBER_TYPE  = 'viber';
	const RESEND_TYPE = 'viber,sms';


	public static $lastResponse = array();

	protected static $login, $apiKey;


	protected static function getHeaders($data = array())
	{
		ksort($data);
		reset($data);
		$ts = microtime() . rand(0, 10000);

		return [
			 'login: ' . self::$login,
			 'ts: ' . $ts,
			 'sig: ' . md5(implode('', $data) . $ts . self::$apiKey),
		];
	}

	protected static function get($url, $data = array())
	{
		$vars = http_build_query($data, '', '&');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::BASE_URL . $url . "?$vars");
		curl_setopt($ch, CURLOPT_HTTPHEADER, self::getHeaders($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		return self::getCurlResult($ch);
	}

	private static function post($url, $data = array())
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::BASE_URL . $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, self::getHeaders($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		return self::getCurlResult($ch);
	}

	protected static function getCurlResult($ch)
	{
		$result = new Result();

		$body = curl_exec($ch);
		$info = curl_getinfo($ch);

		self::$lastResponse = array(
			 'body' => $body,
			 'meta' => $info,
		);
		curl_close($ch);

		if(!$info)
			$result->addError(new Error(self::BASE_CLASS . ': Empty meta'));

		if(!$body)
			$result->addError(new Error(self::BASE_CLASS . ': Empty body'));

		$body = Json::decode($body);

		if(json_last_error() != JSON_ERROR_NONE) {
			$result->addError(new Error(self::BASE_CLASS . ': (' . $info['http_code'] . ') ' . 'Error response format'));
		}

		if($info['http_code'] != 200) {

			if($errors = $body['errors']){
				$body['error_message'] = '';
				if(is_array($errors)){
					foreach($errors as $error){
						$body['error_message'] .=   $error['message'] . ' ' .$error['to'] ."\n";
					}
				}
			}

			$result->addError(new Error( self::BASE_CLASS .': (' . $info['http_code'] . ') ' .  $body['error_message'] . ': ' . $body['exception']));
		}

		//$ttfile = dirname(__FILE__) . '/1_txt.php';
		//file_put_contents($ttfile, "<pre>" . print_r($info, 1) . "</pre>\n");
		//file_put_contents($ttfile, "<pre>" . print_r($body, 1) . "</pre>\n", FILE_APPEND);

		$result->setData((array)$body);

		return $result;
	}

	public static function getBalance($params)
	{
		self::$login  = $params['LOGIN'];
		self::$apiKey = $params['API_KEY'];

		$result = self::get('/client/info');

		if($result->isSuccess()) {
			$data    = $result->getData()['info'];
			$balance = $data['balance'];
			$balance = roundEx($balance, SALE_VALUE_PRECISION);
		}
		else
			$balance = join("<br>", $result->getErrorMessages());


		return $balance;
	}

	/**
	 * Send sms
	 *
	 * @param $to
	 * @param $text
	 * @param $siteId
	 * @param $params
	 *
	 * @return \Bitrix\Main\Result
	 */
	public static function send($to, $text, $siteId, $params)
	{
		self::$login  = $params['LOGIN'];
		self::$apiKey = $params['API_KEY'];

		$from  = $params['SENDER'][ $siteId ];
		$route = self::SMS_TYPE;
		$text  = trim($text);

		if(!Application::isUtfMode())
			$text = Text\Encoding::convertEncoding($text, SITE_CHARSET, 'UTF-8');

		$to = is_array($to) ? $to : array($to);

		$data = array(
			 'to'    => implode(',', $to),
			 'text'  => $text,
			 'from'  => $from,
			 'route' => $route,
		);

		$result = self::post('/message', $data);

		$body = $result->getData();
		if($result->isSuccess()) {
			if($items = $body['items']) {
				$smsid = array();
				if(is_array($items)){
					foreach($items as $item){
						$smsid[] = $item['uuid'];
					}
				}
				$result->setData($smsid);
			}
			else {
				$error_message = '';
				if($errors = $body['errors']){
					$body['error_message'] = '';
					if(is_array($errors)){
						foreach($errors as $error){
							$error_message .=   $error['message'] . ' ' .$error['to'] ."\n";
						}
					}
				}
				else
					$error_message = $body['error_message'];

				$result->addError(new Error(self::BASE_CLASS . ': ' . $error_message));
			}
		}


		return $result;
	}

}