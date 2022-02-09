<?
IncludeModuleLangFile(__FILE__);

class CSocServFacebook extends CSocServAuth
{
	const ID = "Facebook";

	public function GetSettings()
	{
		return array(
			array("facebook_appid", GetMessage("socserv_fb_id"), "", Array("text", 40)),
			array("facebook_appsecret", GetMessage("socserv_fb_secret"), "", Array("text", 40)),
			array("note"=>GetMessage("socserv_fb_sett_note")),
		);
	}

	public function GetFormHtml($arParams)
	{
		$redirect_uri = CSocServUtil::GetCurUrl('auth_service_id='.self::ID.'&check_key='.$_SESSION["UNIQUE_KEY"]);
		$appID = self::GetOption("facebook_appid");
		$appSecret = self::GetOption("facebook_appsecret");

		$fb = new CFacebookInterface($appID, $appSecret);
		$url = $fb->GetAuthUrl($redirect_uri);

		return '<a href="javascript:void(0)" onclick="BX.util.popup(\''.htmlspecialcharsbx(CUtil::JSEscape($url)).'\', 580, 400)" class="bx-ss-button facebook-button"></a><span class="bx-spacer"></span><span>'.GetMessage("socserv_fb_note").'</span>';
	}
	
	public function Authorize()
	{
		$GLOBALS["APPLICATION"]->RestartBuffer();
		$bSuccess = false;

			if(isset($_REQUEST["code"]) && $_REQUEST["code"] <> '')
			{
				if(CSocServAuthManager::CheckUniqueKey())
				{
				$redirect_uri = CSocServUtil::GetCurUrl('auth_service_id='.self::ID, array("code"));

				$appID = self::GetOption("facebook_appid");
				$appSecret = self::GetOption("facebook_appsecret");

				$fb = new CFacebookInterface($appID, $appSecret, $_REQUEST["code"]);

				if($fb->GetAccessToken($redirect_uri) !== false)
				{
					$arFBUser = $fb->GetCurrentUser();
					if(isset($arFBUser["id"]))
					{
						$arFields = array(
							'EXTERNAL_AUTH_ID' => self::ID,
							'XML_ID' => $arFBUser["id"],
							'LOGIN' => "FB_".$arFBUser["email"],
							'EMAIL' => $arFBUser["email"],
							'NAME'=> $arFBUser["first_name"],
							'LAST_NAME'=> $arFBUser["last_name"],
						);

						if(isset($arFBUser['picture']['data']['url']) && self::CheckPhotoURI($arFBUser['picture']['data']['url']))
							if ($arPic = CFile::MakeFileArray($arFBUser['picture']['data']['url']))
								$arFields["PERSONAL_PHOTO"] = $arPic;
						if(isset($arFBUser['birthday']))
							if ($date = MakeTimeStamp($arFBUser['birthday'], "MM/DD/YYYY"))
								$arFields["PERSONAL_BIRTHDAY"] = ConvertTimeStamp($date);
						if(isset($arFBUser['gender']) && $arFBUser['gender'] != '')
						{
							if ($arFBUser['gender'] == 'male')
								$arFields["PERSONAL_GENDER"] = 'M';
							elseif ($arFBUser['gender'] == 'female')
								$arFields["PERSONAL_GENDER"] = 'F';
						}
						$arFields["PERSONAL_WWW"] = "http://www.facebook.com/".$arFBUser["id"];
						$bSuccess = $this->AuthorizeUser($arFields);
					}
				}
			}
		}
		$aRemove = array("logout", "auth_service_error", "auth_service_id", "code", "error_reason", "error", "error_description", "check_key");
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

class CFacebookInterface
{
	const AUTH_URL = "https://www.facebook.com/dialog/oauth";
	const GRAPH_URL = "https://graph.facebook.com";

	protected $appID;
	protected $appSecret;
	protected $code = false;
	protected $access_token = false;
	
	public function __construct($appID, $appSecret, $code=false)
	{
		$this->appID = $appID;
		$this->appSecret = $appSecret;
		$this->code = $code;
	}

	public function GetAuthUrl($redirect_uri)
	{
		return self::AUTH_URL."?client_id=".$this->appID."&redirect_uri=".urlencode($redirect_uri)."&scope=email,user_birthday&display=popup";
	}
	
	public function GetAccessToken($redirect_uri)
	{
		if($this->code === false)
			return false;

		$result = CHTTP::sGet(self::GRAPH_URL.'/oauth/access_token?client_id='.$this->appID.'&client_secret='.$this->appSecret.'&redirect_uri='.urlencode($redirect_uri).'&code='.urlencode($this->code));

		$arResult = array();
		parse_str($result, $arResult);
		if(isset($arResult["access_token"]) && $arResult["access_token"] <> '')
		{
			$this->access_token = $arResult["access_token"];
			return true;
		}
		return false;
	}
	
	public function GetCurrentUser()
	{
		if($this->access_token === false)
			return false;

		$result = CHTTP::sGet(self::GRAPH_URL.'/me?access_token='.$this->access_token."&fields=picture,id,name,first_name,last_name,gender,birthday,email");
		
		if(!defined("BX_UTF"))
			$result = CharsetConverter::ConvertCharset($result, "utf-8", LANG_CHARSET);

		return CUtil::JsObjectToPhp($result);
	}
}
?>