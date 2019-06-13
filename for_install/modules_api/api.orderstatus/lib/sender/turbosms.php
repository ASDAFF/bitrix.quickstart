<?
//https://turbosms.ua/soap.html
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
class Turbosms
{
	const BASE_CLASS = 'Turbosms';
	const BASE_URL   = 'http://turbosms.in.ua/api/wsdl.html';

	public static $lastResponse = array();

	private static function post($query, $params, $sms = array())
	{
		$result = new Result();

		// Подключаемся к серверу
		$client = new \SoapClient('http://turbosms.in.ua/api/wsdl.html');

		// Данные авторизации
		$auth = array(
			'login'    => $params['LOGIN'],
			'password' => $params['PASSWORD'],
		);

		// Авторизируемся на сервере
		$res = $client->Auth($auth);

		$body = null;


		if($query == 'getBalance')
		{
			$res  = $client->GetCreditBalance();
			$body = $res->GetCreditBalanceResult;

			if(!preg_match('/^[\d\.]+$/', $body))
				$result->addError(new Error(self::BASE_CLASS . ': ' . $body));
		}
		elseif($query == 'sendSMS')
		{
			$res  = $client->SendSMS($sms);

			/*
			 [SendSMSResult] => stdClass Object
	          (
	              [ResultArray] => Array
	                  (
	                      [0] => Не хватает 2.56 кредитов для отправки SMS | Сообщения успешно отправлены
	                      [1] => 0c306f85-57f5-f84a-1033-6075af2bcc6e
	                  )

	          )
			 */

			$data = (array)$res->SendSMSResult->ResultArray;
			if(isset($data[1]))
			{
				unset($data[0]);
				$body = join("<br>",$data);
			}


			$mess = trim($res->SendSMSResult->ResultArray[0]);
			if(!$body || $mess != Loc::getMessage('AOS_LS_TURBOSMS_MESS_OK'))
			{
				$error = $res->SendSMSResult->ResultArray;
				if(is_array($error))
					$error = join("<br>",$error);

				$result->addError(new Error(self::BASE_CLASS . ': ' . $error));
				unset($error);
			}
		}

		if(!$body)
			$result->addError(new Error(self::BASE_CLASS . ': Empty body'));


		self::$lastResponse = array(
			'body' => $body,
			'meta' => $res,
		);

		$result->setData((array)$body);

		return $result;
	}

	public static function getBalance($params)
	{
		$result = self::post('getBalance', $params);

		if($result->isSuccess())
		{
			$balance = $result->getData()[0];
			$balance = roundEx($balance, SALE_VALUE_PRECISION);
		}
		else
			$balance = join("<br>", $result->getErrorMessages());


		return $balance;
	}

	public static function send($phone, $message, $siteId, $params)
	{

		$sender = $params['SENDER'][ $siteId ];

		$sms    = array(
			'sender'      => $sender,
			'destination' => $phone,
			'text'        => $message,
		);
		$result = self::post('sendSMS', $params, $sms);

		return $result;
	}
}