<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
/*
Authorization form (for prolog)
Params:
	REGISTER_URL => path to page with authorization script (component?)
	PROFILE_URL => path to page with profile component
*/

$arParamsToDelete = array(
	"login",
	"logout",
	"register",
	"forgot_password",
	"change_password",
	"confirm_registration",
	"confirm_code",
	"confirm_user_id",
	"logout_butt",
	"auth_service_id",
);

$currentUrl = $APPLICATION->GetCurPageParam("", $arParamsToDelete);

$arResult["BACKURL"] = $currentUrl;

$arResult['ERROR'] = false;
$arResult['SHOW_ERRORS'] = (array_key_exists('SHOW_ERRORS', $arParams) && $arParams['SHOW_ERRORS'] == 'Y'? 'Y' : 'N');

if(!$USER->IsAuthorized())
{
	$arResult["FORM_TYPE"] = "login";

	$arResult["STORE_PASSWORD"] = COption::GetOptionString("main", "store_password", "Y") == "Y" ? "Y" : "N";
	$arResult["NEW_USER_REGISTRATION"] = COption::GetOptionString("main", "new_user_registration", "N") == "Y" ? "Y" : "N";

	if(defined("AUTH_404"))
		$arResult["AUTH_URL"] = htmlspecialcharsback(POST_FORM_ACTION_URI);
	else
		$arResult["AUTH_URL"] = $APPLICATION->GetCurPageParam("login=yes", array_merge($arParamsToDelete, array("logout_butt", "backurl")));

	$arParams["REGISTER_URL"] = ($arParams["REGISTER_URL"] <> ''? $arParams["REGISTER_URL"] : $currentUrl);
	$arParams["FORGOT_PASSWORD_URL"] = ($arParams["FORGOT_PASSWORD_URL"] <> ''? $arParams["FORGOT_PASSWORD_URL"] : $arParams["REGISTER_URL"]);

	$url = urlencode($APPLICATION->GetCurPageParam("", array_merge($arParamsToDelete, array("backurl"))));

	$custom_reg_page = COption::GetOptionString('main', 'custom_register_page');
	$arResult["AUTH_REGISTER_URL"] = ($custom_reg_page <> ''? $custom_reg_page : $arParams["REGISTER_URL"].(strpos($arParams["REGISTER_URL"], "?") !== false? "&" : "?")."register=yes&backurl=".$url);
	$arResult["AUTH_FORGOT_PASSWORD_URL"] = $arParams["FORGOT_PASSWORD_URL"].(strpos($arParams["FORGOT_PASSWORD_URL"], "?") !== false? "&" : "?")."forgot_password=yes&backurl=".$url;

	$arRes = array();
	foreach($arResult as $key=>$value)
	{
		$arRes[$key] = htmlspecialcharsbx($value);
		$arRes['~'.$key] = $value;
	}
	$arResult = $arRes;

	$arVarExcl = array("USER_LOGIN"=>1, "USER_PASSWORD"=>1, "backurl"=>1, "auth_service_id"=>1);
	$arResult["POST"] = array();
	foreach($_POST as $vname=>$vvalue)
	{
		if(!array_key_exists($vname, $arVarExcl) && !is_array($vvalue))
			$arResult["POST"][htmlspecialcharsbx($vname)] = htmlspecialcharsbx($vvalue);
	}

	if(defined("HTML_PAGES_FILE") && !defined("ERROR_404"))
		$arResult["~USER_LOGIN"] = "";
	else
		$arResult["~USER_LOGIN"] = $_COOKIE[COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"];

	$arResult["USER_LOGIN"] = $arResult["LAST_LOGIN"] = htmlspecialcharsbx($arResult["~USER_LOGIN"]);
	$arResult["~LAST_LOGIN"] = $arResult["~USER_LOGIN"];

	$arResult["AUTH_SERVICES"] = false;
	$arResult["CURRENT_SERVICE"] = false;
	if(!$USER->IsAuthorized() && CModule::IncludeModule("socialservices"))
	{
		$oAuthManager = new CSocServAuthManager();
		$arServices = $oAuthManager->GetActiveAuthServices($arResult);

		if(!empty($arServices))
		{
			$arResult["AUTH_SERVICES"] = $arServices;
			if(isset($_REQUEST["auth_service_id"]) && $_REQUEST["auth_service_id"] <> '' && isset($arResult["AUTH_SERVICES"][$_REQUEST["auth_service_id"]]))
			{
				$arResult["CURRENT_SERVICE"] = $_REQUEST["auth_service_id"];
				if(isset($_REQUEST["auth_service_error"]) && $_REQUEST["auth_service_error"] <> '')
				{
					$arResult['ERROR_MESSAGE'] = $oAuthManager->GetError($arResult["CURRENT_SERVICE"], $_REQUEST["auth_service_error"]);
				}
				elseif(!$oAuthManager->Authorize($_REQUEST["auth_service_id"]))
				{
					$ex = $APPLICATION->GetException();
					if ($ex)
						$arResult['ERROR_MESSAGE'] = $ex->GetString();
				}
			}
		}
	}

	$arResult["RND"] = mt_rand(0, 99999);
	$arResult["SECURE_AUTH"] = false;
	if(!CMain::IsHTTPS() && COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
	{
		$sec = new CRsaSecurity();
		if(($arKeys = $sec->LoadKeys()))
		{
			$sec->SetKeys($arKeys);
			$sec->AddToForm('system_auth_form'.$arResult["RND"], array('USER_PASSWORD'));
			$arResult["SECURE_AUTH"] = true;
		}
	}

	if(isset($APPLICATION->arAuthResult))
		$arResult['ERROR_MESSAGE'] = $APPLICATION->arAuthResult;

	if($arResult['ERROR_MESSAGE'] <> '')
		$arResult['ERROR'] = true;

	if($APPLICATION->NeedCAPTHAForLogin($arResult["USER_LOGIN"]))
		$arResult["CAPTCHA_CODE"] = $APPLICATION->CaptchaGetCode();
	else
		$arResult["CAPTCHA_CODE"] = false;
}
else //if(!$USER->IsAuthorized())
{
	$arResult["FORM_TYPE"] = "logout";

	$arResult["AUTH_URL"] = $currentUrl;
	$arResult["PROFILE_URL"] = $arParams["PROFILE_URL"].(strpos($arParams["PROFILE_URL"], "?") !== false? "&" : "?")."backurl=".urlencode($currentUrl);
	$arResult["ORDER_URL"] = $arParams["ORDER_URL"].(strpos($arParams["ORDER_URL"], "?") !== false? "&" : "?")."backurl=".urlencode($currentUrl);
	$arResult["ADRESS_URL"] = $arParams["ADRESS_URL"].(strpos($arParams["ADRESS_URL"], "?") !== false? "&" : "?")."backurl=".urlencode($currentUrl);

	$arRes = array();
	foreach($arResult as $key=>$value)
	{
		$arRes[$key] = htmlspecialcharsbx($value);
		$arRes['~'.$key] = $value;
	}
	$arResult = $arRes;

	$arResult["USER_NAME"] = htmlspecialcharsEx($USER->GetFormattedName(false),false);
	$arResult["USER_LOGIN"] = htmlspecialcharsEx($USER->GetLogin());

	$arResult["GET"] = array();
	foreach($_GET as $vname=>$vvalue)
		if(!is_array($vvalue) && $vname!="backurl" && $vname != "login" && $vname != "auth_service_id")
			$arResult["GET"][htmlspecialcharsbx($vname)] = htmlspecialcharsbx($vvalue);
}

$this->IncludeComponentTemplate();
?>