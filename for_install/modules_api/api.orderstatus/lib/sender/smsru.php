<?
//http://sms.ru/api
namespace Api\OrderStatus\Sender;

use Bitrix\Main\Application;
use Bitrix\Main\Result;
use Bitrix\Main\Text;
use Bitrix\Main\Error;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


/**
 * Class Smsclub
 *
 * @package Api\OrderStatus\Sender
 */
class Smsru
{
	const BASE_CLASS = 'Smsru';
	const BASE_URL   = 'https://sms.ru';

	public static $lastResponse = array();

	private static function get($query, $url)
	{
		$result = new Result();

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, self::BASE_URL . $query . '?' . $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($curl, CURLOPT_TIMEOUT, 120);
		/*curl_setopt($curl, CURLOPT_POSTFIELDS, array(
			 "api_id" => "123456",
			 "json" => 1
		));*/

		$out  = curl_exec($curl);
		$meta = curl_getinfo($curl);

		self::$lastResponse = array(
			 'out'  => $out,
			 'meta' => $meta,
		);
		curl_close($curl);

//		$ttfile = dirname(__FILE__) . '/1_txt.php';
//		file_put_contents($ttfile, "<pre>" . print_r(self::$lastResponse, 1) . "</pre>\n");

		if(!$meta)
			$result->addError(new Error(self::BASE_CLASS . ': Empty meta'));

		if(!$out)
			$result->addError(new Error(self::BASE_CLASS . ': Empty out'));


		$out = Json::decode($out);

		switch($meta['http_code']) {
			default:
				if($meta['http_code'] != 200)
					$result->addError(new Error(self::BASE_CLASS . 'http_code: ' . $meta['http_code']));
		}

		if(!$out['status'] || $out['status'] != 'OK') {
			$result->addError(new Error(self::BASE_CLASS . 'status_code: ' . $out['status_code']));
		}

		$result->setData((array)$out);

//		$ttfile = dirname(__FILE__) . '/1_txt.php';
//		file_put_contents($ttfile, "<pre>" . print_r($result, 1) . "</pre>\n", FILE_APPEND);

		return $result;
	}

	//https://sms.ru/my/balance?api_id=api_id&json=1
	public static function getBalance($params)
	{
		$postData = array(
			 'api_id' => trim($params['API_ID']),
			 'json'   => 1,
		);

		$url    = http_build_query($postData);
		$result = self::get('/my/balance', $url);

		if($result->isSuccess()) {
			$balance = $result->getData()['balance'];
		}
		else
			$balance = join("<br>", $result->getErrorMessages());

		return $balance;
	}

	//https://sms.ru/sms/send?api_id=api_id&to=7999999999,7888888888&msg=hello+world&json=1
	public static function send($phone, $message, $siteId, $params)
	{

		$api_id = trim($params['API_ID']);
		$sender = trim($params['SENDER'][ $siteId ]);

		if(!Application::isUtfMode())
			$message = Text\Encoding::convertEncoding($message, 'Windows-1251', 'UTF-8');

		$postData = array(
			 'api_id' => $api_id,
			 'from'   => $sender,
			 'to'     => $phone,
			 'msg'    => $message,
			 'json'   => 1,
			 //Имитирует отправку сообщения для тестирования
			 //'test'     => 1,
		);

		$url    = http_build_query($postData);
		$result = self::get('/sms/send', $url);

		if($result->isSuccess()) {
			$out = '';
			if($smsList = $result->getData()['sms']) {
				foreach($smsList as $sms) {
					$out .= $sms['sms_id'] . ',';
				}

				$out = rtrim($out, ',');
			}
			else{
				$out = self::BASE_CLASS . ' status_code: ' .  $result->getData()['status_code'];
			}

			$result->setData((array)$out);
		}

		return $result;
	}
}