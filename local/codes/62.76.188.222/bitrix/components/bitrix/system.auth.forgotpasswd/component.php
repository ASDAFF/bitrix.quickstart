<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

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
	$arResult["AUTH_URL"] = $APPLICATION->GetCurPageParam("forgot_password=yes",$arParamsToDelete);
}

$arResult["BACKURL"] = $APPLICATION->GetCurPageParam("",$arParamsToDelete);

$arResult["AUTH_AUTH_URL"] = $APPLICATION->GetCurPageParam("login=yes",$arParamsToDelete);

foreach ($arResult as $key => $value)
{
	if (!is_array($value)) $arResult[$key] = htmlspecialcharsbx($value);
}

$arResult["LAST_LOGIN"] = htmlspecialcharsbx($_COOKIE[COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"]);

$this->IncludeComponentTemplate();
?>
