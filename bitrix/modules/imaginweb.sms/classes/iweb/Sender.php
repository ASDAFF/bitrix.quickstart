<?
use Bitcall\Client\Core\BitcallClientFactory;
use Bitcall\Client\Models\Requests\TextCallRequest;
if(CIWebSMS::checkPhpVer53()) {
	include 'Autoloader.php';
}
#debmes(IWEB_APP_PATH);

// функция преобразует рекурсивно объект в массив
if (!function_exists("CIWebObjToArray"))
{
	function CIWebObjToArray(&$obj) {
		if(is_object($obj)) {
			if(count($obj)>0) {
				$obj = (array) $obj;
				array_walk_recursive($obj,'CIWebObjToArray');
			} else {
				$obj = '';
			}
		}
	}
}
/*
 * class CIWebSMS
 */
IncludeModuleLangFile(__FILE__);
class CIWebSMS  {
	
	/*
	 * __construct()
	 * @param $arg
	 */
	public $error = '';
	public $return_mess = '';
	
	function __construct() {
		
	}
	function checkPhpVer53() {
		
		if(defined('PHP_VERSION_ID') && intval(substr(PHP_VERSION_ID,0,3)) >= 503) {
			return true;
		} else {
			return false;
		}
	}
	/*
	* метод проверки номера сотового телефона
	*/
	public function CheckPhoneNumber($phone) {
		$result = true;
		if(!preg_match("/^[0-9]{11,14}+$/", $phone)) {
			if(isset($this)) $this->error = GetMessage("IMAGINWEB_SMS_TELEFON_ZADAN_V_NEVE").$phone;
			$result = false;
		}
		return $result;
	}
	
	public function MakePhoneNumber($phone) {
		$result = preg_match_all('/\d/',$phone,$found);
		$res = implode('',$found[0]);
		if(($found[0][0] == '7' || $found[0][0] == '8') && strlen($res) >= '11' && $found[0][1] != 0) {
			$phone = '7'.substr($res,1,10);
		} elseif(($found[0][0].$found[0][1] == '80') && strlen($res) >= '11') {
			$phone = '38'.substr($res,1,10);
		} elseif(($found[0][0].$found[0][1].$found[0][2] == '380') && strlen($res) >= '12') {
			$phone = '380'.substr($res,3,9);
		}  elseif(($found[0][0].$found[0][1].$found[0][2] == '375') && strlen($res) >= '12') {
			$phone = '375'.substr($res,3,9);
		} elseif(strlen($res) == '10' && $res{0} == 0) {
			$phone = '38'.$res;
		} elseif(strlen($res) == '9') {
			$phone = '375'.$res;
		} elseif(strlen($res) == '10') {
			$phone = '7'.$res;
		} elseif(strlen($res) == '14') {
			$phone = $res;
		} else {
			$phone = '';
		}
		return $phone;
	}
	
	public function CompatibilityCheck($arParams = array()) {
		
		$arResult = array('CHECK'=>false);
		
		if(isset($arParams['GATE']) && $arParams['GATE']=='turbosms.ua') {
			$arResult['CHECK'] = (class_exists('SoapClient'))?true:false;
			$arResult['MESSAGE'] = ($arParams['TEXT'])?GetMessage("IMAGINWEB_SMS_DLA_RABOTY_NE_OBHODI").' php Soap!':'<span style="color: red;">'.GetMessage("IMAGINWEB_SMS_DLA_RABOTY_NE_OBHODI").' php Soap!</span>';
		}
		
		if(isset($arParams['GATE']) && ($arParams['GATE']=='axtele.com'
			 || $arParams['GATE']=='redsms.ru'
			 || $arParams['GATE']=='epochtasms'
			 || $arParams['GATE']=='mobilmoney.ru'
			 || $arParams['GATE']=='giper.mobi'
			 || $arParams['GATE']=='kompeito.ru'
			 || $arParams['GATE']=='mainsms.ru'
			 || $arParams['GATE']=='am4u.ru'
			 || $arParams['GATE']=='sms-sending.ru'
			 || $arParams['GATE']=='nssms.ru'
			 )
		) {
			
			$arResult['CHECK'] = (function_exists('curl_init'))?true:false;
			$arResult['MESSAGE'] = ($arParams['TEXT'])?GetMessage("IMAGINWEB_SMS_DLA_RABOTY_NE_OBHODI").' php cURL!':'<span style="color: red;">'.GetMessage("IMAGINWEB_SMS_DLA_RABOTY_NE_OBHODI").' php cURL!</span>';
		}
		
		if(isset($arParams['GATE']) && $arParams['GATE']=='infosmska.ru') {
			$arResult['CHECK'] = (extension_loaded('sockets'))?true:false;
			$arResult['MESSAGE'] = ($arParams['TEXT'])?GetMessage("IMAGINWEB_SMS_DLA_RABOTY_NE_OBHODI").' php sockets!':'<span style="color: red;">'.GetMessage("IMAGINWEB_SMS_DLA_RABOTY_NE_OBHODI").' php sockets!</span>';
		}
		
		if(isset($arParams['GATE']) && $arParams['GATE']=='alfa-sms.ru') {
			$arResult['CHECK'] = (extension_loaded('sockets'))?true:false;
			$arResult['MESSAGE'] = ($arParams['TEXT'])?GetMessage("IMAGINWEB_SMS_DLA_RABOTY_NE_OBHODI").' php sockets!':'<span style="color: red;">'.GetMessage("IMAGINWEB_SMS_DLA_RABOTY_NE_OBHODI").' php sockets!</span>';
		}
		
		if(isset($arParams['GATE']) && $arParams['GATE']=='imobis') {
			$arResult['CHECK'] = (ini_get('allow_url_fopen'))?true:false;
			$arResult['MESSAGE'] = ($arParams['TEXT'])?GetMessage("IMAGINWEB_SMS_NEOBHODIMO_USTANOVIT").' php.ini allow_url_open = on':'<span style="color: red;">'.GetMessage("IMAGINWEB_SMS_NEOBHODIMO_USTANOVIT").' php.ini  allow_url_fopen = on</span>';
		}
		if(isset($arParams['GATE']) && $arParams['GATE']=='bytehand.com') {
			$arResult['CHECK'] = (ini_get('allow_url_fopen'))?true:false;
			$arResult['MESSAGE'] = ($arParams['TEXT'])?GetMessage("IMAGINWEB_SMS_NEOBHODIMO_USTANOVIT").' php.ini allow_url_open = on':'<span style="color: red;">'.GetMessage("IMAGINWEB_SMS_NEOBHODIMO_USTANOVIT").' php.ini  allow_url_fopen = on</span>';
		}
		return $arResult;
	}
	
	public function GetCreditBalance($arParams = array(), $encoding = LANG_CHARSET) {

		
		$result = "";
		if((isset($arParams['GATE']) && strlen($arParams['GATE'])<=0)
		   || (!is_array($arParams) && strlen($arParams) <= 0)
		   || (is_array($arParams) && !isset($arParams['GATE']))
		   ) $gate = COption::GetOptionString('imaginweb.sms', 'gate');
		if(!is_array($arParams) && strlen($arParams) >= 0) {
			$gate = $arParams;
			$arParams = array();
		}
		if(is_array($arParams) && isset($arParams['GATE']) && strlen($arParams['GATE']) >= 0) $gate = $arParams['GATE'];
		
		if(!is_array($arParams)) $arParams = array();
		$arParams['GATE'] = $gate;
		$arRes = CIWebSMS::CompatibilityCheck($arParams);
		
		if(!$arRes['CHECK']) return $arRes['MESSAGE'];
		
		if($gate == 'alfa-sms.ru') {
			$result = GetMessage("IMAGINWEB_SMS_BALANS_MOJNO_UZNATQ");
		}
		
		if($gate == 'turbosms.ua') {
			$client = new SoapClient(COption::GetOptionString('imaginweb.sms', 'host2'));
			// Данные авторизации
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username2'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password2')
			);
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];

			// Авторизируемся на сервере
			$client->Auth($auth);
			// Получаем количество доступных кредитов
			$resultTMP = $client->GetCreditBalance();
			$result = $resultTMP->GetCreditBalanceResult;
		}
		if($gate == 'infosmska.ru') {
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username4'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password4')
			);
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			$host = "api.infosmska.ru";
			$fp = fsockopen($host, 80);
			$response = '';
			fwrite($fp, "GET /interfaces/getbalance.ashx" .
			"?login=" . rawurlencode($auth['login']) .
			"&pwd=" . rawurlencode($auth['password']) .
			" HTTP/1.1\r\nHost: $host\r\nConnection: Close\r\n\r\n");
			fwrite($fp, "Host: " . $host . "\r\n");
			fwrite($fp, "\n");
			while(!feof($fp)) {
				$response .= fread($fp, 1);
			}
			list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
			fclose($fp);
			$responseBody = iconv('utf-8', $encoding, $responseBody);
			return $responseBody;
		}
		if($gate == 'kompeito.ru') {

			require_once("kompeitosms.php");
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username9'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password9')
			);
			$FROM = COption::GetOptionString('imaginweb.sms', 'originator9');
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			$smser = new KompeitoSms($auth['login'], $auth['password'], $FROM);
			
			$bal = $smser->getBalance();
			
			if (array_key_exists('error', $bal)) {
				#$bal = "Не удалось получить баланс. Ошибка Http: ".$bal['error']."\n";
				return GetMessage("IMAGINWEB_SMS_NE_UDALOSQ_POLUCITQ")." Http: ".$bal['error']."\n";
			}
			
			#debmes($bal,array_key_exists('error', $bal));
			return $bal['money']." ".GetMessage("IMAGINWEB_SMS_RUB").$bal['credits']. " ".GetMessage("IMAGINWEB_SMS_KREDITOV");;
		}
		if($gate == 'mainsms.ru') {

			require_once("mainsms.class.php");
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username10'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password10')
			);
			$FROM = COption::GetOptionString('imaginweb.sms', 'originator10');
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			$api = new MainSMS($auth['login'] , $auth['password'], false, false);
			#debmes()
			$bal = $api->getBalance();
			if(!$bal) {
				return GetMessage("IMAGINWEB_SMS_OSIBKA_PRI_ZAPROSE_B");
			}
			return $bal;
		}
		#sms-sending.ru alfa-sms.ru
		if($gate == 'sms-sending.ru') {
			require_once("sms-sending_transport.php");
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username_sms-sending.ru'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password_sms-sending.ru')
			);
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			

			
			$api = new smsSendingTransport($auth['login'],$auth['password']);
		
			$bal = $api->balance();
			if(!$bal) {
				return GetMessage("IMAGINWEB_SMS_OSIBKA_PRI_ZAPROSE_B");
			}
			return $bal;
		}
		
		if($gate == 'am4u.ru') {
			require_once("am4u_transport.php");
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username11'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password11')
			);
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			
			#define("IWEB_AM4U_HTTPS_LOGIN", $auth['login']); //Ваш логин для HTTPS-протокола
			#define("IWEB_AM4U_HTTPS_PASSWORD", $auth['password']); //Ваш пароль для HTTPS-протокола

			
			$api = new am4uTransport($auth['login'],$auth['password']);
		
			$bal = $api->balance();
			if(!$bal) {
				return GetMessage("IMAGINWEB_SMS_OSIBKA_PRI_ZAPROSE_B");
			}
			return $bal;
		}
		
		if($gate == 'redsms.ru') {
			require_once("redsms_smsClient.php");
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username_redsms.ru'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password_redsms.ru')
			);
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			#$user = COption::GetOptionString('imaginweb.sms', 'username5'); // Ваш логин при регистрации, также его называют System_ID
			#$pass = COption::GetOptionString('imaginweb.sms', 'password5');   // Ваш пароль к логину
			
			$client = new SMSClient($auth['login'],$auth['password']);
			$sessionID = $client->getSessionID();
			$bal = $client->getBalance();
			
			return $bal;
		}
		
		if($gate == 'axtele.com') {
			require_once("DEVINOSMS.Class.v2.1.php");
			$devino = new DEVINOSMS(); // Создание объекта типа DEVINOSMS(необходим для отправки СМС)
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username5'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password5')
			);
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			
			#$user = COption::GetOptionString('imaginweb.sms', 'username5'); // Ваш логин при регистрации, также его называют System_ID
			#$pass = COption::GetOptionString('imaginweb.sms', 'password5');   // Ваш пароль к логину
			
			$result = $devino->GetSessionID($auth['login'],$auth['password']);
			
			$bal = $devino->GetBalance($result['SessionID']);

			return $bal['GetBalanceResult'];
		}
		
		if($gate == 'epochtasms') {
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username3'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password3')
			);
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			$src = '<?xml version="1.0" encoding="UTF-8"?>
			<SMS>
				<operations>
					<operation>BALANCE</operation>
				</operations>
				<authentification>
					<username>'.$auth['login'].'</username>
					<password>'.$auth['password'].'</password>
				</authentification>
			</SMS>';
			$Curl = curl_init();
			$CurlOptions = array(
				CURLOPT_URL=>COption::GetOptionString('imaginweb.sms', 'host3'),
				CURLOPT_FOLLOWLOCATION=>false,
				CURLOPT_POST=>true,
				CURLOPT_HEADER=>false,
				CURLOPT_RETURNTRANSFER=>true,
				CURLOPT_CONNECTTIMEOUT=>15,
				CURLOPT_TIMEOUT=>100,
				CURLOPT_POSTFIELDS=>array('XML'=>$src),
			);
			curl_setopt_array($Curl, $CurlOptions);
			if(false === ($Result = curl_exec($Curl))) {
				#throw new Exception('Http request failed');
			} else {
				$Xml = simplexml_load_string($Result);
				CIWebObjToArray($Xml);
				if($Xml['status'] == 0) {
					$test = ($Xml['trialsms'])?' ('.$Xml['trialsms'].' SMS '.GetMessage("IMAGINWEB_SMS_DLA_TESTA"):'';
					$result = $Xml['credits'].$test;
				}
			}
			curl_close($Curl);
		}
		if($gate == 'imobis') {
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username6'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password6')
			);
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			$query = "http://gate.sms-manager.ru/_balance.php?user=".$auth['login']."&password=".$auth['password'];
			
			$result = file_get_contents($query);
			
			
		}
		
		if($gate == 'bytehand.com') {
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username8'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password8')
			);
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			$query = "http://bytehand.com:3800/balance?id=".$auth['login']."&key=".$auth['password'];
			$result = @file_get_contents($query);
			
			$obResult = json_decode($result);
			if(isset($obResult->description))
				$result = $obResult->description;
			else
				$result = GetMessage("IMAGINWEB_SMS_NEOPOZNANNAA_OSIBKA");
		}
		
		if($gate == 'giper.mobi') {
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username7'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password7')
			);
			
			$uagent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";
			
			$postdata = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
					<info xmlns="http://Giper.mobi/schema/Info">
						<login>'.$auth['login'].'</login>
						<pwd>'.$auth['password'].'</pwd>
					</info>
';
			
			$url = 'http://giper.mobi/api/info';
			$ch = curl_init( $url );
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_ENCODING, "");
			curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
			curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch, CURLOPT_COOKIEJAR, "c://coo.txt");
			curl_setopt($ch, CURLOPT_COOKIEFILE,"c://coo.txt");
			
			
			$content = curl_exec( $ch );
			$err     = curl_errno( $ch );
			$errmsg  = curl_error( $ch );
			$header  = curl_getinfo( $ch );
			curl_close( $ch );
			
			if(false === $content) {
				#throw new Exception('Http request failed');
			} else {
				$Xml = simplexml_load_string($content);
				CIWebObjToArray($Xml);
				$result = $Xml['account'];
			}
		}
		
		if($gate == 'nssms.ru') {
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username_nssms.ru'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password_nssms.ru')
			);
			
			$uagent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";

			$postdata = '<?xml version="1.0" encoding="UTF-8"?>
			<request method="check_balance">
				<login>'.$auth['login'].'</login>
				<password>'.$auth['password'].'</password>
			</request>';
			$postdata = 'xml='.$postdata;

			#debmes(htmlspecialchars($postdata));
			$url = 'http://nssms.ru/gateway/';
			$ch = curl_init( $url );
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_ENCODING, "");
			curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
			curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			#curl_setopt($ch, CURLOPT_COOKIEJAR, "c://coo.txt");
			#curl_setopt($ch, CURLOPT_COOKIEFILE,"c://coo.txt");
			
			
			$content = curl_exec( $ch );
			$err     = curl_errno( $ch );
			$errmsg  = curl_error( $ch );
			$header  = curl_getinfo( $ch );
			curl_close( $ch );
			#debmes($content);
			if(false === $content) {
				#throw new Exception('Http request failed');
			} else {
				$Xml = simplexml_load_string($content);
				CIWebObjToArray($Xml);
				
				$result = $Xml['money'];
			}
		}
		
		return $result;
	}
	
	public function SendCall($phone, $message, $arParams = array(), $encoding = LANG_CHARSET)
	{
		if(strlen(trim($message))<=0) return false;
		
		$phone = CIWebSMS::MakePhoneNumber(trim($phone));
		
		
		if(!CIWebSMS::checkPhpVer53()) return false;
		
		$SITE_ID = (isset($arParams['SITE_ID']))?$arParams['SITE_ID']:SITE_ID;
		$originator = trim(COption::GetOptionString('imaginweb.sms', 'call_sender'.$SITE_ID));
		
		if(is_array($arParams) && isset($arParams['ORIGINATOR']) && strlen($arParams['ORIGINATOR']) >= 0) $originator = $arParams['ORIGINATOR'];
		
		$key = COption::GetOptionString('imaginweb.sms', 'call_key');
		if(CIWebSMS::CheckPhoneNumber($phone) && $key) {
			$message = iconv($encoding, 'utf-8', $message);
			//debmes($message);
			//debmes($phone,$originator);
			//debmes($key);
			
			////Телефон отправителя
			//$callerPhone = '791********';
			////Телефон абонента
			//$phone = "792********";
			////Секретный ключ
			//$key = '****';
			//Инициализируем фабрику
			$clientFactory = new BitcallClientFactory();
			//Создаем клиент
			$client = $clientFactory->getClient($key);
			//Создаем запрос на добавление текстового звонка в очередь
			$request = new TextCallRequest($message, $originator, $phone);
			//Выполняем запрос
			$response = $client->text($request);
			//Показываем ответ
			//var_dump($response);

			if(isset($this)) $this->return_mess = $response;
			
			if($response)
				return true;
			else
				return false;
		}
		return false;
	}
	
	public function Send($phone, $message, $arParams = array(), $encoding = LANG_CHARSET)
	{
		if(strlen(trim($message))<=0) return false;
		
		$phone = CIWebSMS::MakePhoneNumber(trim($phone));
		
		
		if(isset($arParams['GATE']) && is_array($arParams))
			$gate = $arParams['GATE'];
		else
			$gate = COption::GetOptionString('imaginweb.sms', 'gate');
		
		if(!is_array($arParams)) $arParams = array();
		$arParams['GATE'] = $gate;
		
		$arRes = CIWebSMS::CompatibilityCheck($arParams);
		if(!$arRes['CHECK']) return false;
		
		$SITE_ID = (isset($arParams['SITE_ID']))?$arParams['SITE_ID']:SITE_ID;
		$allOriginator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'sender'.$SITE_ID);
		if(strlen(trim($allOriginator)) > 0) $originator = $allOriginator;
		
		if(!is_array($arParams) && strlen($arParams) >= 0) $originator = $arParams;
		if(is_array($arParams) && isset($arParams['ORIGINATOR']) && strlen($arParams['ORIGINATOR']) >= 0) $originator = $arParams['ORIGINATOR'];
		
		if(CIWebSMS::CheckPhoneNumber($phone) && $gate == 'redsms.ru') {
			
			require_once("redsms_smsClient.php");
			//$devino = new DEVINOSMS(); // Создание объекта типа DEVINOSMS(необходим для отправки СМС)
			
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator_redsms.ru');
			
			$message = iconv($encoding, 'utf-8', $message);
			$originator = iconv($encoding, 'utf-8', $originator);
			
			$user = COption::GetOptionString('imaginweb.sms', 'username_redsms.ru'); // Ваш логин при регистрации, также его называют System_ID
			$pass = COption::GetOptionString('imaginweb.sms', 'password_redsms.ru');   // Ваш пароль к логину
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $user = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $pass = $arParams['PASSWORD'];
			
			$client = new SMSClient($user,$pass);
			$sessionID = $client->getSessionID();
			$result = $client->send($originator,$phone,$message);
			
			if(isset($this)) $this->return_mess = $result;
			
			if($result)
				return true;
			else
				return false;
		}

		if(CIWebSMS::CheckPhoneNumber($phone) && $gate == 'axtele.com') {
			
			require_once("DEVINOSMS.Class.v2.1.php");
			$devino = new DEVINOSMS(); // Создание объекта типа DEVINOSMS(необходим для отправки СМС)
			
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator5');
			
			
			
			$message = iconv($encoding, 'utf-8', $message);
			$originator = iconv($encoding, 'utf-8', $originator);
			
			$user = COption::GetOptionString('imaginweb.sms', 'username5'); // Ваш логин при регистрации, также его называют System_ID
			$pass = COption::GetOptionString('imaginweb.sms', 'password5');   // Ваш пароль к логину
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $user = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $pass = $arParams['PASSWORD'];
			
			$result = $devino->GetSessionID($user,$pass);
			$da = array($phone);
			$countDA = count($da); //Количество номеров.
			$sourceAddress = addslashes('<![CDATA['.$originator.']]>'); //Имя отправителя, подключаются у менеджеров
			$receiptRequested='true';
			foreach ($da as $s)									//Перевод номеров в тег <string>
				$destinationAddresses.='<string>'.$s.'</string>';
			$data = addslashes('<![CDATA['.$message.']]>');  //Текст СМС, вводится между квадратными скобками

			$result += $devino->SendMessage($result[SessionID],$data, $destinationAddresses,$sourceAddress,$receiptRequested,$countDA); //

			$result['CommandStatus'] = iconv('windows-1251',$encoding,$result['CommandStatus']);
			
			if(isset($this)) $this->return_mess = $result;
			
			if($result)
				return true;
			else
				return false;
		}
		
		
		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == '' || $gate == 'am4u.ru')) {
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator');
			
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username11'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password11')
			);
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator11');
			#$FROM = COption::GetOptionString('imaginweb.sms', 'originator9');
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			
			require_once("am4u_transport.php");
			
			#define("IWEB_AM4U_HTTPS_LOGIN", $auth['login']); //Ваш логин для HTTPS-протокола
			#define("IWEB_AM4U_HTTPS_PASSWORD", $auth['password']); //Ваш пароль для HTTPS-протокола
			
			$api = new am4uTransport($auth['login'],$auth['password']);
			
			$params = array(
				"text" => $message,
				"source" => $originator
			);
			
			$result = $api->send($params,array($phone));
			
			if(isset($this)) $this->return_mess = $result;
			
			if($result)
				return true;
			else
				return false;
		}
		
		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == '' || $gate == 'sms-sending.ru')) {
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator');
			
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username_sms-sending.ru'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password_sms-sending.ru')
			);
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator_sms-sending.ru');
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			
			require_once("sms-sending_transport.php");
			
			
			$api = new smsSendingTransport($auth['login'],$auth['password']);
			
			$params = array(
				"text" => $message,
				"source" => $originator
			);
			#debmes($auth);
			#debmes($params);
			$result = $api->send($params,array($phone));
			
			if(isset($this)) $this->return_mess = $result;
			
			if($result)
				return true;
			else
				return false;
		}
		
		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == 'nssms.ru')) {
			$message = iconv($encoding, 'utf-8', $message);
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator_nssms.ru');
			
			// Данные авторизации
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username_nssms.ru'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password_nssms.ru')
			);
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
 
 			$postdata = '<?xml version="1.0" encoding="UTF-8"?>
<request method="Sendsms">
	<login>'.$auth['login'].'</login>
	<password>'.$auth['password'].'</password>
	<sender>'.$originator.'</sender>
	<phone_to num="0">+'.$phone.'</phone_to>
	<message>'.$message.'</message>
 </request>';
			$postdata = 'xml='.$postdata;

			#debmes(htmlspecialchars($postdata));
			$url = 'http://nssms.ru/gateway/';
			$ch = curl_init( $url );
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_ENCODING, "");
			curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
			curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			#curl_setopt($ch, CURLOPT_COOKIEJAR, "c://coo.txt");
			#curl_setopt($ch, CURLOPT_COOKIEFILE,"c://coo.txt");
			
			
			$content = curl_exec( $ch );
			$err     = curl_errno( $ch );
			$errmsg  = curl_error( $ch );
			$header  = curl_getinfo( $ch );
			curl_close( $ch );
			#debmes($content);
			if(false === $content) {
				#throw new Exception('Http request failed');
			} else {
				#debmes($content);
				$Xml = simplexml_load_string($content);
				CIWebObjToArray($Xml);
				$Xml[] = htmlspecialchars($postdata);
				if(isset($this)) $this->return_mess = $Xml;
			}
			
			if($Result)
				return true;
			else
				return false;
		}
		
		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == '' || $gate == 'alfa-sms.ru')) {
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator');
			
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username_alfa-sms.ru'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password_alfa-sms.ru')
			);
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator_alfa-sms.ru');
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			
			require_once("ASSMS.class.php");
			
			$sms= new ASSMS($auth['login'],$auth['password']);
			$result=$sms->post_message($message, $phone, $originator);
			#debmes($auth);
			#debmes($message,$phone);
			//$api = new smsSendingTransport($auth['login'],$auth['password']);
			//
			//$params = array(
			//	"text" => $message,
			//	"source" => $originator
			//);

			//$result = $api->send($params,array($phone));
			
			if(isset($this)) $this->return_mess = $result;
			
			if($result)
				return true;
			else
				return false;
		}
		
		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == '' || $gate == 'mainsms.ru')) {
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator');
			
			require_once("mainsms.class.php");
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username10'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password10')
			);
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator10');
			#$FROM = COption::GetOptionString('imaginweb.sms', 'originator9');
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			$message = iconv($encoding, 'utf-8', $message);
			
			$api = new MainSMS($auth['login'] , $auth['password'], false, false);
			
			
			#debmes($message,$phone); debmes($originator);
			$result = $api->sendSMS ( $phone , $message , $originator);
			
			if(isset($this)) $this->return_mess = $result;
			
			if($result)
				return true;
			else
				return false;
		}
		
		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == '' || $gate == 'kompeito.ru')) {
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator');
			
			require_once("kompeitosms.php");
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username9'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password9')
			);
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator9');
			#$FROM = COption::GetOptionString('imaginweb.sms', 'originator9');
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			$message = iconv($encoding, 'utf-8', $message);
			
			$smser = new KompeitoSms($auth['login'], $auth['password'], $originator);
			
			$send_result = $smser->sendSingle($phone, $message);
			$responseBody = $send_result;
			if (array_key_exists('error', $send_result)) {
				#$responseBody = "Ошибка отправки сообщения. Ошибка Http: " + $send_result['error']."\n";
				$result = false;
			} else {
				$result = true;
			}
			if(isset($this)) $this->return_mess = $responseBody;
			
			if($result)
				return true;
			else
				return false;
		}
		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == '' || $gate == 'infosmska.ru')) {
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username4'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password4')
			);
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator4');
			
			
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			$tf = (COption::GetOptionString('imaginweb.sms', 'tf'))?'&tf=1':'';
			
			$message = iconv($encoding, 'utf-8', $message);
			$host = "api.infosmska.ru";
			$fp = fsockopen($host, 80);
			$query = "GET /interfaces/SendMessages.ashx".
			"?login=".rawurlencode($auth['login']).
			"&pwd=".rawurlencode($auth['password']).
			"&phones=".rawurlencode($phone).
			"&message=".rawurlencode($message).
			"&sender=".rawurlencode($originator).
			$tf.
			" HTTP/1.1\r\nHost: $host\r\nConnection: Close\r\n\r\n";
			fwrite($fp, $query);
			fwrite($fp, "Host: " . $host . "\r\n");
			fwrite($fp, "\n");
			while(!feof($fp)) {
				$response .= fread($fp, 1);
			}
			fclose($fp);
			
			list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
			$responseBody = iconv('utf-8', $encoding, $responseBody);
			if(isset($this)) $this->return_mess = $responseBody;
			
			if($responseBody)
				return true;
			else
				return false;
		}
		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == '' || $gate == 'mobilmoney.ru')) {
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator');
			
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password')
			);
			
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			$src = '<?xml version="1.0" encoding="'.$encoding.'"?>
			<request method="SendSMS">
				<login>'.$auth['login'].'</login>
				<pwd>'.$auth['password'].'</pwd>
				<originator>'.$originator.'</originator>
				<phone_to>+'.$phone.'</phone_to>
				<message>'.$message.'</message>
				<sync>0</sync>
			</request>';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml; charset='.$encoding.'"));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $src);
			curl_setopt($ch, CURLOPT_URL, COption::GetOptionString('imaginweb.sms', 'host'));
			
			$result = curl_exec($ch);
			$result = iconv('utf-8', $encoding, $responseBody);
			if(isset($this)) $this->return_mess = $result;
			
			curl_close($ch);
			
			if($result)
				return true;
			else
				return false;
		}

		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == 'turbosms.ua')) {
			
			$originator = (strlen($originator) <= 0)?COption::GetOptionString('imaginweb.sms', 'originator2'):$originator;
			
			
			chdir(dirname(__FILE__));
			$client = new SoapClient(COption::GetOptionString('imaginweb.sms', 'host2'));
			// Данные авторизации
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username2'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password2')
			);
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			// Авторизируемся на сервере
			$client->Auth($auth);
			$message = iconv($encoding, 'utf-8', $message);
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator2');
			// Данные для отправки
			$sms = Array(
				'sender' => $originator,
				'destination' => '+'.$phone,
				'text' => $message
			);

			// Отправляем сообщение на один номер. 
			// Подпись отправителя может содержать английские буквы и цифры. Максимальная длина - 11 символов.
			// Номер указывается в полном формате, включая плюс и код страны
			$result = $client->SendSMS($sms);
			
			
			if(isset($this)) $this->return_mess = $result->SendSMSResult->ResultArray;
			
			if($result)
				return true;
			else
				return false;
		}
		
		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == 'epochtasms')) {
			$message = iconv($encoding, 'utf-8', $message);
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator3');
			
			// Данные авторизации
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username3'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password3')
			);
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			$src = '<?xml version="1.0" encoding="UTF-8"?>
			<SMS>
				<operations>
					<operation>SEND</operation>
				</operations>
				<authentification>
					<username>'.$auth['login'].'</username>
					<password>'.$auth['password'].'</password>
				</authentification>
				<message>
					<sender>'.$originator.'</sender>
					<text>'.$message.'</text>
				</message>
				<numbers>
					<number>'.$phone.'</number>
				</numbers>
			</SMS>';
			
			
			$Curl = curl_init();
			$CurlOptions = array(
				CURLOPT_URL=>COption::GetOptionString('imaginweb.sms', 'host3'),
				CURLOPT_FOLLOWLOCATION=>false,
				CURLOPT_POST=>true,
				CURLOPT_HEADER=>false,
				CURLOPT_RETURNTRANSFER=>true,
				CURLOPT_CONNECTTIMEOUT=>15,
				CURLOPT_TIMEOUT=>100,
				CURLOPT_POSTFIELDS=>array('XML'=>$src),
			);
			curl_setopt_array($Curl, $CurlOptions);
			if(false === ($Result = curl_exec($Curl))) {
				throw new Exception('Http request failed');
			} else {
				$Xml = simplexml_load_string($Result);
				CIWebObjToArray($Xml);
				if(isset($this)) $this->return_mess = $Xml;
			}
			curl_close($Curl);
			
			if($Result)
				return true;
			else
				return false;
		}
		
		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == 'imobis')) {
			$message = iconv($encoding, 'utf-8', $message);
			$binary = bin2hex( iconv("UTF-8", "UTF-16BE", $message) );
			
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator6');
			
			// Данные авторизации
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username6'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password6')
			);
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			$query = "http://gate.sms-manager.ru/_getsmsd.php?user=".$auth['login']."&password=".$auth['password']."&sender=".urlencode($originator)."&SMSText=".urlencode($message)."&binary=".$binary."&GSM=$phone";
			$response = file_get_contents($query);
			
			if(isset($this)) $this->return_mess = $response;
			
			if($response)
				return true;
			else
				return false;
		}
		
		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == 'bytehand.com')) {
			$message = iconv($encoding, 'utf-8', $message);
			
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator8');
			
			// Данные авторизации
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username8'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password8')
			);
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			$query = "http://bytehand.com:3800/send?id=".$auth['login']."&key=".$auth['password']."&to=".$phone."&from=".urlencode($originator)."&text=".urlencode($message);
			$response = @file_get_contents($query);
			
			if(isset($this)) $this->return_mess = $response;
			
			if($response)
				return true;
			else
				return false;
		}

		if(CIWebSMS::CheckPhoneNumber($phone) && ($gate == 'giper.mobi')) {
			$message = iconv($encoding, 'utf-8', $message);
			
			$originator = (strlen(trim($originator)) > 0)?trim($originator):COption::GetOptionString('imaginweb.sms', 'originator7');
			
			// Данные авторизации
			$auth = Array (
				'login' => COption::GetOptionString('imaginweb.sms', 'username7'),
				'password' => COption::GetOptionString('imaginweb.sms', 'password7')
			);
			if(isset($arParams['LOGIN']) && is_array($arParams)) $auth['login'] = $arParams['LOGIN'];
			if(isset($arParams['PASSWORD']) && is_array($arParams)) $auth['password'] = $arParams['PASSWORD'];
			
			$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".
				"<message>".
					"<login>" . $auth['login'] . "</login>".
					"<pwd>" . $auth['password'] . "</pwd>".
					"<id>" . rand(100000,999999) . "</id>".
					"<sender>" . $originator . "</sender>".
					"<text>" . $message . "</text>".
					"<phones>".
					"<phone>" . $phone . "</phone>".
					"</phones>".
				"</message>";
			
			$uagent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";
			$url = 'http://giper.mobi/api/message';
			$ch = curl_init( $url );
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_ENCODING, "");
			curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
			curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($ch, CURLOPT_COOKIEJAR, "c://coo.txt");
			curl_setopt($ch, CURLOPT_COOKIEFILE,"c://coo.txt");
			
			
			$content = curl_exec( $ch );
			$err     = curl_errno( $ch );
			$errmsg  = curl_error( $ch );
			$header  = curl_getinfo( $ch );
			curl_close( $ch );
			
			if(false === $content) {
				return false;
			} else {
				if(isset($this)) $this->return_mess = $content;
				return true;
			}
		}
		
		return false;
	}
}

