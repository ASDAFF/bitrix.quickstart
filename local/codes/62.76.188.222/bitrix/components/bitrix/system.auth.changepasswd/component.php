<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

$arResult["GROUP_POLICY"] = CUser::GetGroupPolicy($arResult["ID"]);

$arParamsToDelete = array(
	"login",
	"logout",
	"register",
	"forgot_password",
	"change_password",
	"confirm_registration",
	"confirm_code",
	"confirm_user_id",
);

if(defined("AUTH_404"))
{
	$arResult["AUTH_URL"] = POST_FORM_ACTION_URI;
}
else
{
	$arResult["AUTH_URL"] = $APPLICATION->GetCurPageParam("change_password=yes", $arParamsToDelete);
}

$arResult["BACKURL"] = $APPLICATION->GetCurPageParam("", $arParamsToDelete);

$arResult["AUTH_AUTH_URL"] = $APPLICATION->GetCurPageParam("login=yes",$arParamsToDelete);

foreach ($arResult as $key => $value)
{
	if (!is_array($value)) $arResult[$key] = htmlspecialcharsbx($value);
}

$arRequestParams = array(
	"USER_CHECKWORD",
	"USER_PASSWORD",
	"USER_CONFIRM_PASSWORD",
);

foreach ($arRequestParams as $param)
{
	$arResult[$param] = strlen($_REQUEST[$param]) > 0 ? $_REQUEST[$param] : "";
	$arResult[$param] = htmlspecialcharsbx($arResult[$param]);
}

$arResult["LAST_LOGIN"] = (isset($_REQUEST["USER_LOGIN"]) ? htmlspecialcharsbx($_REQUEST["USER_LOGIN"]) : htmlspecialcharsbx($_COOKIE[COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"]));

$arResult["SECURE_AUTH"] = false;
if(!CMain::IsHTTPS() && COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
{
	$sec = new CRsaSecurity();
	if(($arKeys = $sec->LoadKeys()))
	{
		$sec->SetKeys($arKeys);
		$sec->AddToForm('bform', array('USER_PASSWORD', 'USER_CONFIRM_PASSWORD'));
		$arResult["SECURE_AUTH"] = true;
	}
}

$this->IncludeComponentTemplate();
?>
