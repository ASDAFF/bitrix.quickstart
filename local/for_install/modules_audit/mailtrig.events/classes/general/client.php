<?
class CMailTrigClient
{
	private static $autoLoginUri = 'http://app.mailtrig.ru/wizard.php';
	private static $trackUri = 'http://app.mailtrig.ru/track.php';
	private static $apiUri = 'http://app.mailtrig.ru/api/';

	protected $connectionTimeout = 5;
	protected $requestTimeout = 30;

	protected $login;
	protected $password;
	protected $appId;

	function __construct()
	{
		$this->login = COption::GetOptionString("mailtrig.events", "login");
		$this->password = COption::GetOptionString("mailtrig.events", "password");
		$this->appId = COption::GetOptionString("mailtrig.events", "appId");

		if(strlen($this->appId) == 0)
		{
			$arAppData = $this->getAppId($this->login, $this->password);
			if($arAppData["status"] == 200)
			{
				Option::SetOptionString("mailtrig.events", "appId", $arAppData["data"]["appId"]);
				$this->appId = $arAppData["data"]["appId"];
			}
		}
	}

	public function getAutoLoginUri($login, $password)
	{
		if(strlen(self::$autoLoginUri) == 0)
			return false;

		$login = trim($login);
		$password = trim($password);

		if(strlen($login) == 0 || strlen($password) == 0)
			return false;

		$hash = md5($login . md5($password));

		return self::$autoLoginUri . "?new&autologin=" . $hash;
	}

	public function getAutoLoginResultCampaignUri($login, $password, $id)
	{
		if(strlen(self::$autoLoginUri) == 0)
			return false;

		$login = trim($login);
		$password = trim($password);
		$id = intval($id);

		if(strlen($login) == 0 || strlen($password) == 0 || $id <= 0)
			return false;

		$hash = md5($login . md5($password));

		return self::$autoLoginUri . "?page=result_campaign&id=".$id."&autologin=" . $hash;
	}

	public function regUser($email = "", $password = "", $mtApiUrl = "", $partner = "")
	{
		if(strlen(self::$apiUri) == 0)
			return false;

		$email = trim($email);
		$password = trim($password);
		$mtApiUrl = trim($mtApiUrl);
		$partner = trim($partner);

		if(strlen($email) == 0 || strlen($password) == 0)
			return false;

		$arParams = array(
			"method" => "reg_user",
			"email" => $email,
			"password" => $password,

			"mt_integration" => "bitrix"
		);

		if(strlen($mtApiUrl) > 0)
			$arParams["mt_api_url"] = $mtApiUrl;

		if(strlen($partner) > 0)
			$arParams["mt_partner"] = $partner;

		$result = $this->get(self::$apiUri, $arParams);

		$arResult = json_decode($result, true);

		return $arResult;
	}

	public function getAppId($login = "", $password = "", $mtApiUrl = "", $partner = "")
	{
		$bDebugMode = (COption::GetOptionString("mailtrig.events", "debug_mode") == "Y")?true:false;

		if(strlen(self::$apiUri) == 0)
			return false;

		$login = trim($login);
		$password = trim($password);
		$mtApiUrl = trim($mtApiUrl);
		$partner = trim($partner);

		if(strlen($login) == 0 || strlen($password) == 0)
			return false;

		$arParams = array(
			"method" => "get_appId",
			"username" => $login,
			"password" => $password,

			"mt_integration" => "bitrix"
		);

		if(strlen($mtApiUrl) > 0)
			$arParams["mt_api_url"] = $mtApiUrl;

		if(strlen($partner) > 0)
			$arParams["mt_partner"] = $partner;

		$result = $this->get(self::$apiUri, $arParams);

		if($bDebugMode) {
			$arDebug = array(
				"arParams" => $arParams,
				"result" => $result
			);
			CMailTrigLogger::debug("getAppId", $arDebug);
		}

		$arResult = json_decode($result, true);

		return $arResult;
	}

	public function getCampaigns($id = 0)
	{
		if(strlen(self::$apiUri) == 0)
			return false;

		if(strlen($this->login) == 0 || strlen($this->password) == 0)
			return false;

		$id = intval($id);

		$arParams = array(
			"method" => "get_campaigns",
			"username" => $this->login,
			"password" => $this->password,
		);

		if($id > 0)
			$arParams["campaign_id"] = $id;

		$result = $this->get(self::$apiUri, $arParams);

		$arResult = json_decode($result, true);

		return $arResult;
	}

	public function getResults($dateFrom = "", $dateTo = "")
	{
		if(strlen(self::$apiUri) == 0)
			return false;

		if(strlen($this->login) == 0 || strlen($this->password) == 0)
			return false;

		$arParams = array(
			"method" => "get_results",
			"username" => $this->login,
			"password" => $this->password
		);

		// check date format
		if(self::checkDateFormat($dateFrom))
			$arParams["datefrom"] = $dateFrom;
		if(self::checkDateFormat($dateTo))
			$arParams["dateto"] = $dateTo;

		$result = $this->get(self::$apiUri, $arParams);

		$arResult = json_decode($result, true);

		return $arResult;
	}

	public function getLinechart($id = 0, $dateFrom = "", $dateTo = "")
	{
		if(strlen(self::$apiUri) == 0)
			return false;

		if(strlen($this->login) == 0 || strlen($this->password) == 0)
			return false;

		$arParams = array(
			"method" => "get_linechart",
			"username" => $this->login,
			"password" => $this->password
		);

		if(intval($id) > 0)
			$arParams["id"] = $id;

		// check date format
		if(self::checkDateFormat($dateFrom))
			$arParams["datefrom"] = $dateFrom;
		if(self::checkDateFormat($dateTo))
			$arParams["dateto"] = $dateTo;

		$result = $this->get(self::$apiUri, $arParams);

		$arResult = json_decode($result, true);

		return $arResult;
	}

	public function sendEvent($arEvent = array(), $customerId = "", $arUser = array(), $method = "get")
	{
		$bDebugMode = (COption::GetOptionString("mailtrig.events", "debug_mode") == "Y")?true:false;

		if(strlen(self::$trackUri) == 0)
			return false;

		$this->appId = trim($this->appId);
		if(strlen($this->appId) == 0)
			return false;

		$this->login = trim($this->login);
		if(strlen($this->login) == 0)
			return false;

		if(empty($arEvent))
			return false;

		$arRequestParams = array(
			array(
				"_init",
				array(
					"appId" => $this->appId,
					"username" => $this->login
				)
			)
		);

		$arUserRequest = array(
			"customer_id" => $customerId
		);
		if(!empty($arUser))
		{
			foreach($arUser as $key => $value)
			{
				$arUserRequest[$key] = $value;
			}
		}
		$arRequestParams[] = array(
			"_user",
			$arUserRequest
		);

		$bCheckEvents = false;
		foreach($arEvent as $arValue)
		{
			if(in_array("_event", $arValue))
			{
				$arRequestParams[] = $arValue;

				$bCheckEvents = true;
			}
		}
		if(!$bCheckEvents)
			return false;

		$arParams = array(
			"params" => json_encode($arRequestParams)
		);

		if($method == "post")
		{
			$result = $this->post(self::$trackUri, $arParams);
		}
		elseif($method == "delete")
		{
			$result = $this->delete(self::$trackUri, $arParams);
		}
		else
		{
			$result = $this->get(self::$trackUri, $arParams);
		}

		if($bDebugMode) {
			$arDebug = array(
				"arParams" => $arParams,
				"result" => $result,
				"method" => $method
			);
			CMailTrigLogger::debug("event", $arDebug);
		}

		$arResult = json_decode($result, true);

		return $arResult;
	}

	public static function checkDateFormat($date)
	{
		if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date))
			return true;

		return false;
	}

	public static function checkCurl()
	{
		if(function_exists('curl_version')) {
			return true;
		} else {
			$bDebugMode = (COption::GetOptionString("mailtrig.events", "debug_mode") == "Y")?true:false;
			if($bDebugMode)
				CMailTrigLogger::error("CURL disabled");

			return false;
		}
	}

	public function setTimeout($requestTimeout = null, $connectionTimeout = null)
	{
		if ($requestTimeout !== null)
			$this->requestTimeout = floatval($requestTimeout);
		if ($connectionTimeout !== null)
			$this->connectionTimeout = floatval($connectionTimeout);
	}

	public function delete($sUrl = "", $arParams = array())
	{
		return $this->request("DELETE", $sUrl, $arParams);
	}

	public function get($sUrl = "", $arParams = array())
	{
		return $this->request("GET", $sUrl, $arParams);
	}

	public function post($sUrl = "", $arParams = array())
	{
		return $this->request("POST", $sUrl, $arParams);
	}

	// Do request
	private function request($sMethod = "GET", $sUrl = "", $arParams = array())
	{
		if($sMethod != "POST" && $sMethod != "DELETE")
		{
			$sMethod = "GET";
		}

		if(strlen($sUrl) <= 0)
			return;

		if($sMethod === "GET" || $sMethod === "DELETE") {
			$sUrl .= empty($arParams) ? '' : '?'.http_build_query($arParams, '', '&');
		}

		if(self::checkCurl())
		{
			$ch  = curl_init($sUrl);
			//curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $sMethod);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

			if($sMethod === "POST" && !empty($arParams))
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arParams));

			$response = curl_exec($ch);
		}
		else
		{
			$response = false;
		}

		return $response;
	}
}
?>