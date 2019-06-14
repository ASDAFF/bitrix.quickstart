<?
//https://lk.redsms.ru/api/
namespace Api\OrderStatus\Sender;

use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


/**
 * Class Redsms
 *
 * @package Api\OrderStatus\Sender
 */
class Redsms
{
	const BASE_CLASS = 'Redsms';
	const BASE_URL   = 'https://lk.redsms.ru/get';

	public static $lastResponse = array();

	private static function get($query, $url)
	{
		$result = new Result();

		if(strlen($url) > 0)
			$query .= '?' . $url;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::BASE_URL . $query);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // return result instead of echoing
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   // stop cURL from verifying the peer's certificate
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    // follow redirects, Location: headers
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);           // but dont redirect more than 10 times
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));

		$body = curl_exec($ch);
		$meta = curl_getinfo($ch);

		self::$lastResponse = array(
			'body' => $body,
			'meta' => $meta,
		);
		curl_close($ch);

		if(!$meta)
			$result->addError(new Error(self::BASE_CLASS . ': Empty meta'));

		if(!$body)
			$result->addError(new Error(self::BASE_CLASS . ': Empty body'));

		$body = Json::decode($body);

		switch($meta['http_code'])
		{
			default:
				if($meta['http_code'] != 200)
					$result->addError(new Error(self::BASE_CLASS . $body['error']));
		}

		$result->setData((array)$body);

		return $result;
	}

	private static function getSignature($params, $api_key)
	{
		ksort($params);
		reset($params);
		return md5(implode($params) . $api_key);
	}

	private static function getTimestamp()
	{
		$url = 'https://lk.redsms.ru/get/timestamp.php';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));

		$timestamp = curl_exec($ch);

		return $timestamp;
	}


	public static function getBalance($params)
	{
		$api_key   = $params['API_KEY'];
		$login     = $params['LOGIN'];
		$timestamp = self::getTimestamp();
		$return    = 'json';

		$postData = array(
			'login'     => $login,
			'timestamp' => $timestamp,
			'return'    => $return,
		);

		$signature = self::getSignature($postData, $api_key);
		$url       = http_build_query($postData) . '&signature=' . $signature;

		$result = self::get('/balance.php', $url);

		if($result->isSuccess())
		{
			$balance = $result->getData()['money'];
			$balance = roundEx($balance, SALE_VALUE_PRECISION);

			if($currency = $result->getData()['currency'])
				$balance = $balance . ' ' . $currency;
		}
		else
			$balance = join("<br>", $result->getErrorMessages());

		return $balance;
	}


	public static function send($phone, $message, $siteId, $params)
	{
		$api_key   = $params['API_KEY'];
		$login     = $params['LOGIN'];
		$sender    = $params['SENDER'][ $siteId ];
		$timestamp = self::getTimestamp();
		$return    = 'json';

		$postData = array(
			'timestamp' => $timestamp,
			'login'     => $login,
			'phone'     => $phone,
			'text'      => $message,
			'sender'    => $sender,
			'return'    => $return,
		);

		ksort($postData);
		reset($postData);

		$signature = self::getSignature($postData, $api_key);
		$url       = http_build_query($postData) . '&signature=' . $signature;

		$result = self::get('/send.php', $url);

		//Prepare result
		if($result->isSuccess())
		{
			$body = $result->getData()[0];
			$body = reset($body);

			if($body['id_sms'])
				$result->setData((array)$body['id_sms']);
			else
				$result->addError(new Error(self::BASE_CLASS . ': '. Loc::getMessage('AOS_LS_REDSMS_ERROR_SEND')));
		}

		return $result;
	}
}