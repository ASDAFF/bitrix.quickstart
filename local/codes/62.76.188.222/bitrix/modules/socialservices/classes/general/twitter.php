<?
IncludeModuleLangFile(__FILE__);

class CSocServTwitter extends CSocServAuth
{
	const ID = "Twitter";

	public function GetSettings()
	{
		return array(
			array("twitter_key", GetMessage("socserv_tw_key"), "", Array("text", 40)),
			array("twitter_secret", GetMessage("socserv_tw_secret"), "", Array("text", 40)),
			array("note"=>GetMessage("socserv_tw_sett_note")),
		);
	}

	public function GetFormHtml($arParams)
	{
		$url = $GLOBALS['APPLICATION']->GetCurPageParam('auth_service_id='.self::ID.'&check_key='.$_SESSION["UNIQUE_KEY"], array("logout", "auth_service_error", "auth_service_id"));
		return '<a href="javascript:void(0)" onclick="BX.util.popup(\''.htmlspecialcharsbx(CUtil::JSEscape($url)).'\', 800, 450)" class="bx-ss-button twitter-button"></a><span class="bx-spacer"></span><span>'.GetMessage("socserv_tw_note").'</span>';
	}

	public function Authorize()
	{
		$GLOBALS["APPLICATION"]->RestartBuffer();
		$bSuccess = false;

		$appID = self::GetOption("twitter_key");
		$appSecret = self::GetOption("twitter_secret");

		if(!isset($_REQUEST["oauth_token"]) || $_REQUEST["oauth_token"] == '')
		{
			$tw = new CTwitterInterface($appID, $appSecret);
			$callback = CSocServUtil::GetCurUrl('auth_service_id='.self::ID);
			if($tw->GetRequestToken($callback))
				$tw->RedirectAuthUrl();
		}
		elseif(CSocServAuthManager::CheckUniqueKey())
		{
			$tw = new CTwitterInterface($appID, $appSecret, $_REQUEST["oauth_token"], $_REQUEST["oauth_verifier"]);
			if(($arResult = $tw->GetAccessToken()) !== false && $arResult["user_id"] <> '')
			{
				$twUser = $tw->GetUserInfo($arResult["user_id"]);

				$first_name = $last_name = "";
				if($twUser["name"] <> '')
				{
					$aName = explode(" ", $twUser["name"]);
					$first_name = $aName[0];
					if(isset($aName[1]))
						$last_name = $aName[1];
				}

				$arFields = array(
					'EXTERNAL_AUTH_ID' => self::ID,
					'XML_ID' => $arResult["user_id"],
					'LOGIN' => $arResult["screen_name"],
					'NAME'=> $first_name,
					'LAST_NAME'=> $last_name,
				);
				$arFields["PERSONAL_WWW"] = "https://twitter.com/".$twUser["screen_name"];

				$bSuccess = $this->AuthorizeUser($arFields);
			}
		}

		$aRemove = array("logout", "auth_service_error", "auth_service_id", "oauth_token", "oauth_verifier", "check_key");
		$url = $GLOBALS['APPLICATION']->GetCurPageParam(($bSuccess? '':'auth_service_id='.self::ID.'&auth_service_error=1'), $aRemove);
		echo '
<script type="text/javascript">
if(window.opener)
	window.opener.location = \''.CUtil::JSEscape($url).'\';
window.close();
</script>
';
		die();
	}
}

class CTwitterInterface
{
	const REQUEST_URL = "http://api.twitter.com/oauth/request_token";
	const AUTH_URL = "http://api.twitter.com/oauth/authorize";
	const TOKEN_URL = "http://api.twitter.com/oauth/access_token";
	const API_URL = "http://api.twitter.com/1/";

	protected $appID;
	protected $appSecret;
	protected $token = false;
	protected $tokenVerifier = false;
	protected $tokenSecret = false;

	public function __construct($appID, $appSecret, $token=false, $tokenVerifier=false)
	{
		$this->appID = $appID;
		$this->appSecret = $appSecret;
		$this->token = $token;
		$this->tokenVerifier = $tokenVerifier;
		if($this->token && isset($_SESSION["twitter_token_secret"]))
			$this->tokenSecret = $_SESSION["twitter_token_secret"];
	}

	protected function GetDefParams()
	{
		return array(
			"oauth_consumer_key" => $this->appID,
			"oauth_nonce" => md5(microtime().mt_rand()),
			"oauth_signature_method" => "HMAC-SHA1",
			"oauth_timestamp" => time(),
			"oauth_version" => "1.0",
		);
	}

	public function GetRequestToken($callback)
	{
		$arParams = array_merge($this->GetDefParams(), array(
			"oauth_callback" => $callback,
		));

		$arParams["oauth_signature"] = $this->BuildSignature($this->GetSignatureString($arParams, self::REQUEST_URL));

		$result = CHTTP::sPost(self::REQUEST_URL, $arParams);

		parse_str($result, $arResult);
		if(isset($arResult["oauth_token"]) && $arResult["oauth_token"] <> '')
		{
			$this->token = $arResult["oauth_token"];
			$this->tokenSecret = $arResult["oauth_token_secret"];
			$_SESSION["twitter_token_secret"] = $this->tokenSecret;
			return true;
		}
		return false;
	}

	public function RedirectAuthUrl()
	{
		if(!$this->token)
			return false;
		LocalRedirect(self::AUTH_URL."?oauth_token=".urlencode($this->token).'&check_key='.$_SESSION["UNIQUE_KEY"], true);
	}

	public function GetAccessToken()
	{
		if(!$this->token || !$this->tokenVerifier || !$this->tokenSecret)
			return false;

		$arParams = array_merge($this->GetDefParams(), array(
			"oauth_token" => $this->token,
			"oauth_verifier" => $this->tokenVerifier,
		));

		$arParams["oauth_signature"] = $this->BuildSignature($this->GetSignatureString($arParams, self::TOKEN_URL));

		$result = CHTTP::sPost(self::TOKEN_URL, $arParams);
		parse_str($result, $arResult);
		if(isset($arResult["oauth_token"]) && $arResult["oauth_token"] <> '')
		{
			$this->token = $arResult["oauth_token"];
			$this->tokenSecret = $arResult["oauth_token_secret"];
			return $arResult;
		}
		return false;
	}

	public function GetUserInfo($user_id)
	{
		$result = CHTTP::sGet(self::API_URL.'users/show.json?user_id='.$user_id);
		if(!defined("BX_UTF"))
			$result = CharsetConverter::ConvertCharset($result, "utf-8", LANG_CHARSET);
		return CUtil::JsObjectToPhp($result);
	}

	protected function urlencode($mixParams)
	{
		if(is_array($mixParams))
			return array_map(array($this, 'urlencode'), $mixParams);
		elseif (is_scalar($mixParams))
			return str_replace(array('+','%7E'), array(' ','~'), rawurlencode($mixParams));
		else
			return '';
	}

	protected function GetSignatureString($arParams, $url)
	{
		if(array_key_exists('oauth_signature', $arParams))
			unset($arParams['oauth_signature']);

		return implode('&',
			$this->urlencode(
				array(
					"POST",
					$url,
					$this->BuildQuery($arParams),
				)
			)
		);
	}

	protected function BuildQuery($params)
	{
		if (!$params)
			return '';

		$keys = $this->urlencode(array_keys($params));
		$values = $this->urlencode(array_values($params));
		$params = array_combine($keys, $values);

		uksort($params, 'strcmp');

		$pairs = array();
		foreach ($params as $parameter => $value)
		{
			if(is_array($value))
			{
				natsort($value);
				foreach ($value as $duplicate_value)
					$pairs[] = $parameter . '=' . $duplicate_value;
			}
			else
				$pairs[] = $parameter . '=' . $value;
		}
		return implode('&', $pairs);
	}

	protected function BuildSignature($sigString)
	{
		$key = implode('&',
			$this->urlencode(
				array(
					$this->appSecret,
					($this->tokenSecret? $this->tokenSecret : ''),
				)
			)
		);

		return base64_encode(hash_hmac('sha1', $sigString, $key, true));
	}
}

?>