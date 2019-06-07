<?
//https://smsclub.mobi/api
//https://my.smsclub24.ru/api/index
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
class Smsclub
{
	const BASE_CLASS = 'Smsclub';
	const BASE_URL   = 'https://gate.smsclub.mobi/token';

	public static $lastResponse = array();

	private static function get($query, $url)
	{
		$result = new Result();

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, self::BASE_URL . $query . '?' . $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($curl, CURLOPT_TIMEOUT, 120);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

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

		if($query == '/') {
			//Ошибки возвращаются строкой типа: Phone in black list. | No input file specified.
			if(!preg_match('/=IDS START=/',$out)) {
				$result->addError(new Error(self::BASE_CLASS . ': ' . $out));
			}
		}

		switch($meta['http_code']) {
			default:
				if($meta['http_code'] != 200)
					$result->addError(new Error(self::BASE_CLASS . $out['error']));
		}

		$result->setData((array)$out);

//		$ttfile = dirname(__FILE__) . '/1_txt.php';
//		file_put_contents($ttfile, "<pre>" . print_r($result, 1) . "</pre>\n", FILE_APPEND);

		return $result;
	}

	//https://gate.smsclub.mobi/token/httpgetbalance.php?username=user&token=user_token
	public static function getBalance($params)
	{
		$username = trim($params['LOGIN']);
		$token    = trim($params['TOKEN']);

		$postData = array(
			 'username' => $username,
			 'token'    => $token,
		);

		$url    = http_build_query($postData);
		$result = self::get('/getbalance.php', $url);

		if($result->isSuccess()) {
			$balance = $result->getData()[0];
			$balance = str_replace('<br/>', ', ', $balance);
		}
		else
			$balance = join("<br>", $result->getErrorMessages());

		return $balance;
	}

	//https://gate.smsclub.mobi/token/?username=user&token=user_token&from=SMS CLUB&to=380675126767&text=Hello from SMS CLUB
	public static function send($phone, $message, $siteId, $params)
	{

		$username = trim($params['LOGIN']);
		$token    = trim($params['TOKEN']);
		$sender   = trim($params['SENDER'][ $siteId ]);

		if(Application::isUtfMode())
			$message = Text\Encoding::convertEncoding($message, 'UTF-8', 'Windows-1251');

		$postData = array(
			 'username' => $username,
			 'token'    => $token,
			 'from'     => $sender,
			 'to'       => $phone,
			 'text'     => $message,
		);

		$url = http_build_query($postData);

		$result = self::get('/', $url);

		if($result->isSuccess()) {
			$out = $result->getData()[0];
			$out = str_replace('<br/>', '', $out);
			$result->setData((array)$out);
		}

		return $result;
	}
}