<?
//http://docs.devinotele.com/httpapiv2.html
namespace Api\OrderStatus\Sender;

use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Devinotele
 *
 * @package Api\OrderStatus\Sender
 */
class Devinotele
{
	const BASE_CLASS  = 'Devinotele';
	const BASE_URL    = 'https://integrationapi.net/rest/v2';

	public static $lastResponse = array();

	private static function post($query, $postData = array())
	{
		$result = new Result();

		$strData = http_build_query($postData);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::BASE_URL . $query);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // return result instead of echoing
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   // stop cURL from verifying the peer's certificate
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    // follow redirects, Location: headers
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);           // but dont redirect more than 10 times
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120 );
		curl_setopt($ch, CURLOPT_TIMEOUT, 120 );
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $strData);
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
					$result->addError(new Error( self::BASE_CLASS . ': ('. $body['Code'] .') ' . $body['Desc']));
		}

		$result->setData((array)$body);

		return $result;
	}


	//Получение баланса авторизованного пользователя
	public static function getBalance($params)
	{
		$login    = $params['LOGIN'];
		$password = $params['PASSWORD'];

		$postData = array(
			'Login'              => $login,
			'Password'           => $password,
		);

		$result = self::post('/User/Balance',$postData);
		if($result->isSuccess())
		{
			$balance = $result->getData()[0];
			$balance = roundEx($balance, SALE_VALUE_PRECISION);
		}
		else
			$balance = join("<br>", $result->getErrorMessages());


		return $balance;
	}

	//Получение статуса отправленного SMS-сообщения
	/*public static function getState($messageId)
	{
		$arPostData = array(
			'messageId' => $messageId,
		);

		$result = self::post('/Sms/State', $arPostData);

		return $result;
	}*/

	//Отправка SMS-сообщения на один номер без учета часового пояса получателя
	public static function send($phone, $message, $siteId, $params)
	{
		$login    = $params['LOGIN'];
		$password = $params['PASSWORD'];
		$sender   = $params['SENDER'][ $siteId ];
		$message  = substr($message, 0, 2000);

		$postData = array(
			'Login'              => $login,
			'Password'           => $password,
			'SourceAddress'      => $sender,
			'DestinationAddress' => $phone,
			'Data'               => $message,
			'Validaty'           => 0,
		);

		$result = self::post('/Sms/Send', $postData);

		return $result;
	}

}