<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if (!CModule::IncludeModule("socialservices"))
	return;

if (!$GLOBALS["USER"]->IsAuthorized())
	return;

if ($_SESSION["LAST_ERROR"])
{
	ShowError($_SESSION["LAST_ERROR"]);
	$_SESSION["LAST_ERROR"] = false;
}

$oAuthManager = new CSocServAuthManager();
$arServices = $oAuthManager->GetActiveAuthServices($arResult);
$arResult["AUTH_SERVICES"] = $arServices;
//***************************************
//Checking the input parameters.
//***************************************

if((isset($_REQUEST["code"]) && $_REQUEST["code"] <> '') || (isset($_REQUEST["auth_service_id"]) && $_REQUEST["auth_service_id"] <> '' && isset($arResult["AUTH_SERVICES"][$_REQUEST["auth_service_id"]])))
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

$userID = $GLOBALS["USER"]->GetID();
$userName = '';
$arResult["ALLOW_DELETE_ID"] = array();
$dbSocservUser = CSocServAuthDB::GetList(array("PERSONAL_PHOTO" => "DESC"),array('USER_ID' => $userID));
//***************************************
//Obtain data on the related user account.
//***************************************
while($arUser = $dbSocservUser->Fetch())
{
	if($arUser["NAME"] != '' && $arUser["LAST_NAME"] != '')
		$userName = $arUser["NAME"]." ".$arUser["LAST_NAME"];
	elseif ($arUser["NAME"] != '')
		$userName = $arUser["NAME"];
	elseif ($arUser["LAST_NAME"] != '')
		$userName = $arUser["LAST_NAME"];
	elseif ($arUser["LOGIN"] != '')
		$userName = $arUser["LOGIN"];

	preg_match("/\/([a-zA-Z0-9._]{1,})\//", $arUser["EXTERNAL_AUTH_ID"], $result);
	if (isset($result[1]))
	{
		switch($result[1])
		{
			case 'openid.mail.ru' : $arUser["EXTERNAL_AUTH_ID"] = 'MailRuOpenID';
			break;
			case 'www.livejournal.com' : $arUser["EXTERNAL_AUTH_ID"] = 'Livejournal';
			break;
			case 'openid.yandex.ru' : $arUser["EXTERNAL_AUTH_ID"] = 'YandexOpenID';
			break;
			case 'www.liveinternet.ru' : $arUser["EXTERNAL_AUTH_ID"] = 'Liveinternet';
			break;
			default : $arUser["EXTERNAL_AUTH_ID"] = $result[1];
		}

	}

	$arResult["DB_SOCSERV_USER"][] = array(
		"ID" => $arUser["ID"],
		"LOGIN" => htmlspecialcharsbx($arUser["LOGIN"]),
		"NAME" => htmlspecialcharsbx($arUser["NAME"]),
		"LAST_NAME" => htmlspecialcharsbx($arUser["LAST_NAME"]),
		"EXTERNAL_AUTH_ID" => $arUser["EXTERNAL_AUTH_ID"],
		"VIEW_NAME" => htmlspecialcharsbx($userName),
		"PERSONAL_LINK" => htmlspecialcharsbx($arUser["PERSONAL_WWW"]),
		"PERSONAL_PHOTO" => intval($arUser["PERSONAL_PHOTO"]),
	);
	if($arUser["CAN_DELETE"] != 'N' && $arParams["ALLOW_DELETE"] != 'N')
		$arResult["ALLOW_DELETE_ID"][] = $arUser["ID"];
}

$arParamsToDelete = array(
	"auth_service_id",
	"openid_assoc_handle",
	"openid_identity",
	"openid_sreg_email",
	"openid_sreg_fullname",
	"openid_sreg_gender",
	"openid_mode",
	"openid_op_endpoint",
	"openid_response_nonce",
	"openid_return_to",
	"openid_signed",
	"openid_sig",
	"current_fieldset",
);

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_REQUEST["action"] == "delete" && isset($_REQUEST["user_id"]) && intval($_REQUEST["user_id"] > 0) && check_bitrix_sessid())
{
	$userId = intval($_REQUEST["user_id"]);
	if(in_array($userId, $arResult["ALLOW_DELETE_ID"]))
	{
		if (!CSocServAuthDB::Delete($userId))
			$_SESSION["LAST_ERROR"] = GetMessage("DELETE_ERROR");
	}
	LocalRedirect($APPLICATION->GetCurPageParam("", array("sessid", "user_id", "action")));

}
$arResult['CURRENTURL'] = $APPLICATION->GetCurPageParam("", $arParamsToDelete);
if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_REQUEST["auth_service_id"]))
	LocalRedirect($arResult['CURRENTURL']);

$this->IncludeComponentTemplate();

?>