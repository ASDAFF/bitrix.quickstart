<?
IncludeModuleLangFile(__FILE__);

class CSocServOdnoklassniki extends CSocServAuth
{
	const ID = "Odnoklassniki";

	public function GetSettings()
	{
		return array(
			array("odnoklassniki_appid", GetMessage("socserv_odnoklassniki_client_id"), "", Array("text", 40)),
			array("odnoklassniki_appkey", GetMessage("socserv_odnoklassniki_client_key"), "", Array("text", 40)),
			array("odnoklassniki_appsecret", GetMessage("socserv_odnoklassniki_client_secret"), "", Array("text", 40)),
			array("note"=>GetMessage("socserv_odnoklassniki_form_note", array('#URL#'=>CSocServUtil::ServerName()))),
		);
	}

	public function GetFormHtml($arParams)
	{
		$appID = self::GetOption("odnoklassniki_appid");
		$appSecret = self::GetOption("odnoklassniki_appsecret");
		$appKey = self::GetOption("odnoklassniki_appkey");

		$gAuth = new COdnoklassnikiInterface($appID, $appSecret, $appKey);

		$redirect_uri = CSocServUtil::GetCurUrl('auth_service_id='.self::ID.'&check_key='.$_SESSION["UNIQUE_KEY"]);
		$state = 'site_id='.SITE_ID.'&backurl='.urlencode($GLOBALS["APPLICATION"]->GetCurPageParam('', array("logout", "auth_service_error", "auth_service_id")));

		$url = $gAuth->GetAuthUrl($redirect_uri, $state);

		return '<a href="javascript:void(0)" onclick="BX.util.popup(\''.htmlspecialcharsbx(CUtil::JSEscape($url)).'\', 580, 400)" class="bx-ss-button odnoklassniki-button"></a><span class="bx-spacer"></span><span>'.GetMessage("MAIN_OPTION_COMMENT1").'</span>';
	}
	
	public function Authorize()
	{
		$GLOBALS["APPLICATION"]->RestartBuffer();
		$bSuccess = false;
		if((isset($_REQUEST["code"]) && $_REQUEST["code"] <> '') && CSocServAuthManager::CheckUniqueKey())
		{
			$redirect_uri = CSocServUtil::GetCurUrl('auth_service_id='.self::ID, array("code"));
			$appID = self::GetOption("odnoklassniki_appid");
			$appSecret = self::GetOption("odnoklassniki_appsecret");
			$appKey = self::GetOption("odnoklassniki_appkey");
			$gAuth = new COdnoklassnikiInterface($appID, $appSecret, $appKey, $_REQUEST["code"]);

			if($gAuth->GetAccessToken($redirect_uri) !== false)
			{
				$arOdnoklUser = $gAuth->GetCurrentUser();

				if ($arOdnoklUser['uid'] <> '')
				{
					$uid = $arOdnoklUser['uid'];
					$first_name = $last_name = $gender = "";
					if($arOdnoklUser['first_name'] <> '')
						$first_name = $arOdnoklUser['first_name'];
					if($arOdnoklUser['last_name'] <> '')
						$last_name = $arOdnoklUser['last_name'];
					if(isset($arOdnoklUser['gender']) && $arOdnoklUser['gender'] != '')
					{
						if ($arOdnoklUser['gender'] == 'male')
							$gender = 'M';
						elseif ($arOdnoklUser['gender'] == 'female')
							$gender = 'F';
					}

					$arFields = array(
						'EXTERNAL_AUTH_ID' => self::ID,
						'XML_ID' => "OK".$uid,
						'LOGIN' => "OKuser".$uid,
						'EMAIL'=> $uid."@".self::ID.".bitrix",
						'NAME'=> $first_name,
						'LAST_NAME'=> $last_name,
						'PERSONAL_GENDER' => $gender,
					);
					if(isset($arOdnoklUser['birthday']))
						if ($date = MakeTimeStamp($arOdnoklUser['birthday'], "YYYY-MM-DD"))
							$arFields["PERSONAL_BIRTHDAY"] = ConvertTimeStamp($date);
					if(isset($arOdnoklUser['pic_2']) && self::CheckPhotoURI($arOdnoklUser['pic_2']))
						if ($arPic = CFile::MakeFileArray($arOdnoklUser['pic_2'].'&name=/'.md5($arOdnoklUser['pic_2']).'.jpg'))
							$arFields["PERSONAL_PHOTO"] = $arPic;
					$arFields["PERSONAL_WWW"] = "http://odnoklassniki.ru/profile/".$uid;

					$bSuccess = $this->AuthorizeUser($arFields);
				}
			}
		}
		$url = ($GLOBALS["APPLICATION"]->GetCurDir() == "/login/") ? "/auth/" : $GLOBALS["APPLICATION"]->GetCurDir();
		if(isset($_REQUEST["state"]))
		{
			$arState = array();
			parse_str($_REQUEST["state"], $arState);

			if(isset($arState['backurl']))
				$url = parse_url($arState['backurl'], PHP_URL_PATH);
		}
		$aRemove = array("logout", "auth_service_error", "auth_service_id", "code", "error_reason", "error", "error_description", "check_key");
		if(!$bSuccess)
			$url = $GLOBALS['APPLICATION']->GetCurPageParam(('auth_service_id='.self::ID.'&auth_service_error=1'), $aRemove);

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

class COdnoklassnikiInterface
{
	const AUTH_URL = "http://www.odnoklassniki.ru/oauth/authorize";
	const TOKEN_URL = "http://api.odnoklassniki.ru/oauth/token.do";
	const CONTACTS_URL = "http://api.odnoklassniki.ru/fb.do";

	protected $appID;
	protected $appSecret;
	protected $appKey;
	protected $code = false;
	protected $access_token = false;
	protected $sign = false;
	
	public function __construct($appID, $appSecret, $appKey, $code=false)
	{
		$this->appID = $appID;
		$this->appSecret = $appSecret;
		$this->code = $code;
		$this->appKey = $appKey;
	}

	public function GetAuthUrl($redirect_uri, $state='')
	{
		return self::AUTH_URL.
			"?client_id=".urlencode($this->appID).
			"&redirect_uri=".urlencode($redirect_uri).
			"&response_type=code";//.
			($state <> ''? '&state='.urlencode($state):'');
	}
	
	public function GetAccessToken($redirect_uri)
	{
		if($this->code === false)
			return false;

		$result = CHTTP::sPost(self::TOKEN_URL, array(
			"code"=>$this->code,
			"client_id"=>$this->appID,
			"client_secret"=>$this->appSecret,
			"redirect_uri"=>$redirect_uri,
			"grant_type"=>"authorization_code",
		));

		$arResult = CUtil::JsObjectToPhp($result);

		if(isset($arResult["access_token"]) && $arResult["access_token"] <> '')
		{
			$this->access_token = $arResult["access_token"];
			$arguments = array();
			$arguments["application_key"] = $this->appKey;
			$arguments['method'] = 'users.getCurrentUser';
			ksort($arguments);
			$this->sign = strtolower(md5('application_key='.$arguments["application_key"].'method='.$arguments['method'].md5($this->access_token.$this->appSecret)));
			return true;
		}
		return false;
	}
	
	public function GetCurrentUser()
	{
		if($this->access_token === false)
			return false;

		$result = CHTTP::sGet(self::CONTACTS_URL."?method=users.getCurrentUser&application_key=".$this->appKey."&access_token=".$this->access_token."&sig=".$this->sign);
		if(!defined("BX_UTF"))
			$result = CharsetConverter::ConvertCharset($result, "utf-8", LANG_CHARSET);

		return CUtil::JsObjectToPhp($result);
	}
}
?>