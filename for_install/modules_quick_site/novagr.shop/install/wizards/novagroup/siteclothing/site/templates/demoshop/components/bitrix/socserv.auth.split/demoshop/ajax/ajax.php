<?php 
/**
 * скрипт удаляет изера соцсети
 *
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
//deb($_REQUEST);
if ($_REQUEST["action"] == "delete" && isset($_REQUEST["userId"]) && intval($_REQUEST["userId"] > 0) && check_bitrix_sessid())
{
	if (!CModule::IncludeModule("socialservices"))
		return;
	
	$result = array();
	$result['result'] = 'ERROR';
	$result['userId'] = $_REQUEST["userId"];
	
	$userID = $GLOBALS["USER"]->GetID();
	if (!$userID) return;
	// получаем пользователей, доступных для удаления
	$arResult["ALLOW_DELETE_ID"] = array();
	$dbSocservUser = CSocServAuthDB::GetList(array("PERSONAL_PHOTO" => "DESC"),array('USER_ID' => $userID));
	//***************************************
	//Obtain data on the related user account.
	//***************************************
	while($arUser = $dbSocservUser->Fetch())
	{
		/*if($arUser["NAME"] != '' && $arUser["LAST_NAME"] != '')
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
		);*/
		if($arUser["CAN_DELETE"] != 'N' && $arParams["ALLOW_DELETE"] != 'N')
			$arResult["ALLOW_DELETE_ID"][] = $arUser["ID"];
	}
	
	
	
	$userIdDel = intval($_REQUEST["userId"]);
	if(in_array($userIdDel, $arResult["ALLOW_DELETE_ID"]))
	{
		if (!CSocServAuthDB::Delete($userId))
			$_SESSION["LAST_ERROR"] = GetMessage("DELETE_ERROR");
		$result['result'] = 'OK';
	}
	//LocalRedirect($APPLICATION->GetCurPageParam("", array("sessid", "user_id", "action")));
	
	$message =  "<span class='error'>Неверный логин или пароль.</span>";
	// возвращаем на страницу с ошибкой
	
	
	//$result['message'] = $message;
	$resultJson = json_encode($result);
	die($resultJson);
}
?>
