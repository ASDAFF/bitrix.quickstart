<?
IncludeModuleLangFile(__FILE__);

class CSocServMyMailRu extends CSocServAuth
{
	const ID = "MyMailRu";

	public function GetSettings()
	{
		return array(
			array("mailru_id", GetMessage("socserv_mailru_id"), "", Array("text", 40)),
			array("mailru_private_key", GetMessage("socserv_mailru_key"), "", Array("text", 40)),
			array("mailru_secret_key", GetMessage("socserv_mailru_secret"), "", Array("text", 40)),
			array("note"=>GetMessage("socserv_mailru_sett_note")." ".GetMessage("socserv_mailru_opt_note")),
		);
	}

	public function GetFormHtml($arParams)
	{
		$appID = self::GetOption("mailru_id");
		$appSecret = self::GetOption("mailru_secret_key");

		$gAuth = new CMailRuOAuthInterface($appID, $appSecret);

		$redirect_uri = CSocServUtil::GetCurUrl('auth_service_id='.self::ID);
		$state = 'site_id='.SITE_ID.'&backurl='.urlencode($GLOBALS["APPLICATION"]->GetCurPageParam('check_key='.$_SESSION["UNIQUE_KEY"], array("logout", "auth_service_error", "auth_service_id")));

		$url = $gAuth->GetAuthUrl($redirect_uri, $state);

		return '<a href="javascript:void(0)" onclick="BX.util.popup(\''.htmlspecialcharsbx(CUtil::JSEscape($url)).'\', 580, 400)" class="bx-ss-button mymailru-button"></a><span class="bx-spacer"></span><span>'.GetMessage("socserv_mailru_note").'</span>';
	}

	public function Authorize()
	{
		$GLOBALS["APPLICATION"]->RestartBuffer();
		$bSuccess = false;
		if((isset($_REQUEST["code"]) && $_REQUEST["code"] <> '') && CSocServAuthManager::CheckUniqueKey())
		{
			$redirect_uri = CSocServUtil::GetCurUrl('auth_service_id='.self::ID, array("code", "state", "check_key", "backurl"));
			$appID = self::GetOption("mailru_id");
			$appSecret = self::GetOption("mailru_secret_key");

			$gAuth = new CMailRuOAuthInterface($appID, $appSecret, $_REQUEST["code"]);

			if($gAuth->GetAccessToken($redirect_uri) !== false)
			{
				$arMRUser = $gAuth->GetCurrentUser();

				if($arMRUser['0']['uid'] <> '')
				{
					$first_name = $last_name = $gender = "";
					$email = $arMRUser['0']['uid']."@".self::ID.".bitrix";
					if($arMRUser['0']['first_name'] <> '')
					{
						$first_name = $arMRUser['0']['first_name'];
					}
					if($arMRUser['0']['last_name'] <> '')
					{
						$last_name = $arMRUser['0']['last_name'];
					}
					if($arMRUser['0']['email'] <> '')
					{
						$email = $arMRUser['0']['email'];
					}
					if(isset($arMRUser['0']['sex']) && $arMRUser['0']['sex'] != '')
					{
						if ($arMRUser['0']['sex'] == '1')
							$gender = 'M';
						elseif ($arMRUser['0']['sex'] == '2')
							$gender = 'F';
					}

					$arFields = array(
						'EXTERNAL_AUTH_ID' => self::ID,
						'XML_ID' => $arMRUser['0']['uid'],
						'LOGIN' => "MM_".$email,
						'NAME'=> $first_name,
						'EMAIL'=> $email,
						'LAST_NAME'=> $last_name,
						'PERSONAL_GENDER' => $gender,
					);

					if(isset($arMRUser['0']['birthday']))
						if ($date = MakeTimeStamp($arMRUser['0']['birthday'], "DD.MM.YYYY"))
							$arFields["PERSONAL_BIRTHDAY"] = ConvertTimeStamp($date);
					if(isset($arMRUser['0']['pic_190']) && self::CheckPhotoURI($arMRUser['0']['pic_190']))
						if ($arPic = CFile::MakeFileArray($arMRUser['0']['pic_190'].'?name=/'.md5($arMRUser['0']['pic_190']).'.jpg'))
							$arFields["PERSONAL_PHOTO"] = $arPic;
					$arFields["PERSONAL_WWW"] = $arMRUser['0']['link'];

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

class CMailRuOAuthInterface
{
	const AUTH_URL = "https://connect.mail.ru/oauth/authorize";
	const TOKEN_URL = "https://connect.mail.ru/oauth/token";
	const CONTACTS_URL = "http://www.appsmail.ru/platform/api";

	protected $appID;
	protected $appSecret;
	protected $code = false;
	protected $access_token = false;
	protected $userID = false;

	public function __construct($appID, $appSecret, $code=false)
	{
		$this->appID = $appID;
		$this->appSecret = $appSecret;
		$this->code = $code;
	}

	public function GetAuthUrl($redirect_uri, $state='')
	{
		return self::AUTH_URL.
			"?client_id=".urlencode($this->appID).
			"&redirect_uri=".$redirect_uri.
			"&response_type=code".
			($state <> ''? '&state='.urlencode($state):'');
	}

	public function GetAccessToken($redirect_uri)
	{
		if($this->code === false)
			return false;

		$result = CHTTP::sPost(self::TOKEN_URL, array(
			"client_id"=>$this->appID,
			"client_secret"=>$this->appSecret,
			"code"=>$this->code,
			"redirect_uri"=>$redirect_uri,
			"grant_type"=>"authorization_code",
		));

		$arResult = CUtil::JsObjectToPhp($result);

		if((isset($arResult["access_token"]) && $arResult["access_token"] <> '') && isset($arResult["x_mailru_vid"]) && $arResult["x_mailru_vid"] <> '')
		{
			$this->access_token = $arResult["access_token"];
			$this->userID = $arResult["x_mailru_vid"];

			return true;
		}
		return false;
	}

	public function GetCurrentUser()
	{
		if($this->access_token === false)
			return false;
		$sign=md5("app_id=".$this->appID."method=users.getInfosecure=1session_key=".$this->access_token.$this->appSecret);
		$result = CHTTP::sGet(self::CONTACTS_URL.'?method=users.getInfo&secure=1&app_id='.$this->appID.'&session_key='.urlencode($this->access_token).'&sig='.$sign);
		if(!defined("BX_UTF"))
			$result = CharsetConverter::ConvertCharset($result, "utf-8", LANG_CHARSET);

		return CUtil::JsObjectToPhp($result);
	}
}
?>