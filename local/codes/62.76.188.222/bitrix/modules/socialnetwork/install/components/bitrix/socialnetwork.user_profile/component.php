<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("socialnetwork"))
{
	ShowError(GetMessage("SONET_MODULE_NOT_INSTALL"));
	return;
}

$arParams["ID"] = IntVal($arParams["ID"]);
if ($arParams["ID"] <= 0)
	$arParams["ID"] = IntVal($USER->GetID());

if(strLen($arParams["USER_VAR"])<=0)
	$arParams["USER_VAR"] = "user_id";
if(strLen($arParams["PAGE_VAR"])<=0)
	$arParams["PAGE_VAR"] = "page";
if(strLen($arParams["GROUP_VAR"])<=0)
	$arParams["GROUP_VAR"] = "group_id";

$arParams["SHOW_YEAR"] = $arParams["SHOW_YEAR"] == "Y" ? "Y" : ($arParams["SHOW_YEAR"] == "M" ? "M" : "N");
// activation rating
CRatingsComponentsMain::GetShowRating($arParams);

$arParams["SET_NAV_CHAIN"] = ($arParams["SET_NAV_CHAIN"] == "N" ? "N" : "Y");

$arParams["PATH_TO_USER"] = trim($arParams["PATH_TO_USER"]);
if(strlen($arParams["PATH_TO_USER"])<=0)
	$arParams["PATH_TO_USER"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_USER_FRIENDS"] = trim($arParams["PATH_TO_USER_FRIENDS"]);
if(strlen($arParams["PATH_TO_USER_FRIENDS"])<=0)
	$arParams["PATH_TO_USER_FRIENDS"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user_friends&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_USER_FRIENDS_ADD"] = trim($arParams["PATH_TO_USER_FRIENDS_ADD"]);
if(strlen($arParams["PATH_TO_USER_FRIENDS_ADD"])<=0)
	$arParams["PATH_TO_USER_FRIENDS_ADD"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user_friends_add&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_USER_FRIENDS_DELETE"] = trim($arParams["PATH_TO_USER_FRIENDS_DELETE"]);
if(strlen($arParams["PATH_TO_USER_FRIENDS_DELETE"])<=0)
	$arParams["PATH_TO_USER_FRIENDS_DELETE"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user_friends_delete&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_SEARCH"] = trim($arParams["PATH_TO_SEARCH"]);
if (strlen($arParams["PATH_TO_SEARCH"]) <= 0)
	$arParams["PATH_TO_SEARCH"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=search");

$arParams["PATH_TO_LOG"] = trim($arParams["PATH_TO_LOG"]);
if (strlen($arParams["PATH_TO_LOG"]) <= 0)
	$arParams["PATH_TO_LOG"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=log");

$arParams["PATH_TO_ACTIVITY"] = trim($arParams["PATH_TO_ACTIVITY"]);
if (strlen($arParams["PATH_TO_ACTIVITY"]) <= 0)
	$arParams["PATH_TO_ACTIVITY"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=activity&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_SUBSCRIBE"] = trim($arParams["PATH_TO_SUBSCRIBE"]);
if (strlen($arParams["PATH_TO_SUBSCRIBE"]) <= 0)
	$arParams["PATH_TO_SUBSCRIBE"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=subscribe");

$arParams["PATH_TO_SEARCH_INNER"] = trim($arParams["PATH_TO_SEARCH_INNER"]);
if (strlen($arParams["PATH_TO_SEARCH_INNER"]) <= 0)
	$arParams["PATH_TO_SEARCH_INNER"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=search");

$arParams["PATH_TO_USER_GROUPS"] = trim($arParams["PATH_TO_USER_GROUPS"]);
if(strlen($arParams["PATH_TO_USER_GROUPS"])<=0)
	$arParams["PATH_TO_USER_GROUPS"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user_groups&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_GROUP"] = trim($arParams["PATH_TO_GROUP"]);
if (strlen($arParams["PATH_TO_GROUP"]) <= 0)
	$arParams["PATH_TO_GROUP"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group&".$arParams["GROUP_VAR"]."=#group_id#");

$arParams["PATH_TO_GROUP_EDIT"] = trim($arParams["PATH_TO_GROUP_EDIT"]);
if (strlen($arParams["PATH_TO_GROUP_EDIT"]) <= 0)
	$arParams["PATH_TO_GROUP_EDIT"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group_edit&".$arParams["GROUP_VAR"]."=#group_id#");

$arParams["PATH_TO_GROUP_CREATE"] = trim($arParams["PATH_TO_GROUP_CREATE"]);
if (strlen($arParams["PATH_TO_GROUP_CREATE"]) <= 0)
	$arParams["PATH_TO_GROUP_CREATE"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group_create&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_USER_EDIT"] = trim($arParams["PATH_TO_USER_EDIT"]);
if(strlen($arParams["PATH_TO_USER_EDIT"])<=0)
	$arParams["PATH_TO_USER_EDIT"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user_profile_edit&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_MESSAGE_FORM"] = trim($arParams["PATH_TO_MESSAGE_FORM"]);
if (strlen($arParams["PATH_TO_MESSAGE_FORM"]) <= 0)
	$arParams["PATH_TO_MESSAGE_FORM"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=message_form&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_MESSAGES_CHAT"] = trim($arParams["PATH_TO_MESSAGES_CHAT"]);
if (strlen($arParams["PATH_TO_MESSAGES_CHAT"]) <= 0)
	$arParams["PATH_TO_MESSAGES_CHAT"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=messages_chat&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_VIDEO_CALL"] = trim($arParams["PATH_TO_VIDEO_CALL"]);
if (strlen($arParams["PATH_TO_VIDEO_CALL"]) <= 0)
	$arParams["PATH_TO_VIDEO_CALL"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=video_call&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_MESSAGES_USERS_MESSAGES"] = trim($arParams["PATH_TO_MESSAGES_USERS_MESSAGES"]);
if (strlen($arParams["PATH_TO_MESSAGES_USERS_MESSAGES"]) <= 0)
	$arParams["PATH_TO_MESSAGES_USERS_MESSAGES"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=messages_users_messages&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_USER_FEATURES"] = trim($arParams["PATH_TO_USER_FEATURES"]);
if (strlen($arParams["PATH_TO_USER_FEATURES"]) <= 0)
	$arParams["PATH_TO_USER_FEATURES"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user_features&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_USER_SUBSCRIBE"] = trim($arParams["PATH_TO_USER_SUBSCRIBE"]);
if (strlen($arParams["PATH_TO_USER_SUBSCRIBE"]) <= 0)
	$arParams["PATH_TO_USER_SUBSCRIBE"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user_subscribe&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_USER_SETTINGS_EDIT"] = trim($arParams["PATH_TO_USER_SETTINGS_EDIT"]);
if (strlen($arParams["PATH_TO_USER_SETTINGS_EDIT"]) <= 0)
	$arParams["PATH_TO_USER_SETTINGS_EDIT"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user_settings_edit&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_GROUP_REQUEST_GROUP_SEARCH"] = trim($arParams["PATH_TO_GROUP_REQUEST_GROUP_SEARCH"]);
if (strlen($arParams["PATH_TO_GROUP_REQUEST_GROUP_SEARCH"]) <= 0)
	$arParams["PATH_TO_GROUP_REQUEST_GROUP_SEARCH"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group_request_group_search&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_CONPANY_DEPARTMENT"] = trim($arParams["PATH_TO_CONPANY_DEPARTMENT"]);
if (strlen($arParams["PATH_TO_CONPANY_DEPARTMENT"]) <= 0)
	$arParams["PATH_TO_CONPANY_DEPARTMENT"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=conpany_department&department=#ID#");

$arParams["DATE_TIME_FORMAT"] = trim(empty($arParams["DATE_TIME_FORMAT"]) ? $DB->DateFormatToPHP(CSite::GetDateFormat("FULL")) : $arParams["DATE_TIME_FORMAT"]);
$arParams["SHORT_FORM"] = $arParams["SHORT_FORM"] == "Y";

if (!isset($arParams["USER_PROPERTY_MAIN"]) || !is_array($arParams["USER_PROPERTY_MAIN"]))
	$arParams["USER_PROPERTY_MAIN"] = array();
if (!isset($arParams["USER_PROPERTY_CONTACT"]) || !is_array($arParams["USER_PROPERTY_CONTACT"]))
	$arParams["USER_PROPERTY_CONTACT"] = array();
if (!isset($arParams["USER_PROPERTY_PERSONAL"]) || !is_array($arParams["USER_PROPERTY_PERSONAL"]))
	$arParams["USER_PROPERTY_PERSONAL"] = array();

if (!isset($arParams["USER_FIELDS_MAIN"]) || !is_array($arParams["USER_FIELDS_MAIN"]))
	$arParams["USER_FIELDS_MAIN"] = array();
if (!isset($arParams["USER_FIELDS_CONTACT"]) || !is_array($arParams["USER_FIELDS_CONTACT"]))
	$arParams["USER_FIELDS_CONTACT"] = array();
if (!isset($arParams["USER_FIELDS_PERSONAL"]) || !is_array($arParams["USER_FIELDS_PERSONAL"]))
	$arParams["USER_FIELDS_PERSONAL"] = array();

if (!isset($arParams["SONET_USER_FIELDS_SEARCHABLE"]) || !is_array($arParams["SONET_USER_FIELDS_SEARCHABLE"]))
	$arParams["SONET_USER_FIELDS_SEARCHABLE"] = array();
if (!isset($arParams["SONET_USER_PROPERTY_SEARCHABLE"]) || !is_array($arParams["SONET_USER_PROPERTY_SEARCHABLE"]))
	$arParams["SONET_USER_PROPERTY_SEARCHABLE"] = array();

$arParams["PATH_TO_GROUP_SEARCH"] = trim($arParams["PATH_TO_GROUP_SEARCH"]);
if (strlen($arParams["PATH_TO_GROUP_SEARCH"]) <= 0)
	$arParams["PATH_TO_GROUP_SEARCH"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group_search");

$arParams["ITEMS_COUNT"] = IntVal($arParams["ITEMS_COUNT"]);
if ($arParams["ITEMS_COUNT"] <= 0)
	$arParams["ITEMS_COUNT"] = 6;

$arParams["USE_MAIN_MENU"] = (isset($arParams["USE_MAIN_MENU"]) && $arParams["USE_MAIN_MENU"] == "Y" ? $arParams["USE_MAIN_MENU"] : false);

// for bitrix:main.user.link
if (IsModuleInstalled('intranet'))
{
	$arTooltipFieldsDefault	= serialize(array(
		"EMAIL",
		"PERSONAL_MOBILE",
		"WORK_PHONE",
		"PERSONAL_ICQ",
		"PERSONAL_PHOTO",
		"PERSONAL_CITY",
		"WORK_COMPANY",
		"WORK_POSITION",
	));
	$arTooltipPropertiesDefault = serialize(array(
		"UF_DEPARTMENT",
		"UF_PHONE_INNER",
	));
}
else
{
	$arTooltipFieldsDefault = serialize(array(
		"PERSONAL_ICQ",
		"PERSONAL_BIRTHDAY",
		"PERSONAL_PHOTO",
		"PERSONAL_CITY",
		"WORK_COMPANY",
		"WORK_POSITION"
	));
	$arTooltipPropertiesDefault = serialize(array());
}

if (!array_key_exists("SHOW_FIELDS_TOOLTIP", $arParams))
	$arParams["SHOW_FIELDS_TOOLTIP"] = unserialize(COption::GetOptionString("socialnetwork", "tooltip_fields", $arTooltipFieldsDefault));
if (!array_key_exists("USER_PROPERTY_TOOLTIP", $arParams))
	$arParams["USER_PROPERTY_TOOLTIP"] = unserialize(COption::GetOptionString("socialnetwork", "tooltip_properties", $arTooltipPropertiesDefault));

if (IsModuleInstalled("intranet"))
	$arParams['CAN_OWNER_EDIT_DESKTOP'] = $arParams['CAN_OWNER_EDIT_DESKTOP'] != "Y" ? "N" : "Y";
else
	$arParams['CAN_OWNER_EDIT_DESKTOP'] = $arParams['CAN_OWNER_EDIT_DESKTOP'] != "N" ? "Y" : "N";

if ($arParams["ID"] <= 0)
{
	$arResult["NEED_AUTH"] = "Y";
}
else
{
	$arListParams = array("SELECT" => array("UF_*"));

	if ($arParams["SHOW_RATING"] == 'Y' && array_key_exists("RATING_ID", $arParams))
	{
		if (is_array($arParams["RATING_ID"]) && count($arParams["RATING_ID"]) > 0)
		{
			$arParams["RATING_ID_ARR"] = $arParams["RATING_ID"];
			$arParams["RATING_ID"] = $arParams["RATING_ID_ARR"][0];

			foreach($arParams["RATING_ID_ARR"] as $rating_id)
			{
				if (intval($rating_id) > 0)
				{
					$db_rating = CRatings::GetByID($rating_id);
					if ($arRating = $db_rating->GetNext())
						$arResult["RatingMultiple"][$rating_id] = array("NAME" => $arRating["NAME"]);

					$arListParams["SELECT"][] = "RATING_".$rating_id;
				}
			}
			$arResult["Rating"]["NAME"] = $arResult["RatingMultiple"][$arParams["RATING_ID"]]["NAME"];
		}
		elseif (intval($arParams["RATING_ID"]) > 0)
		{
			$db_rating = CRatings::GetByID($arParams["RATING_ID"]);
			if ($arRating = $db_rating->GetNext())
				$arResult["Rating"]["NAME"] = $arRating["NAME"];

			$arListParams["SELECT"][] = "RATING_".$arParams["RATING_ID"];
		}

		$dbUser = CUser::GetList(($by="id"), ($order="asc"), array("ID_EQUAL_EXACT"=>$arParams["ID"]), $arListParams);
		$arResult["User"] = $dbUser->GetNext();
	}
	else
	{
		$dbUser = CUser::GetByID($arParams["ID"]);
		$arResult["User"] = $dbUser->GetNext();
	}

	if (!is_array($arResult["User"]))
	{
		$arResult["FatalError"] = GetMessage("SONET_P_USER_NO_USER").". ";
	}
	else
	{
		if (CModule::IncludeModule('extranet') && !CExtranet::IsProfileViewable($arResult["User"]) && $arResult["User"]["ID"] != $USER->GetID())
			return false;
		$arResult["CurrentUserPerms"] = CSocNetUserPerms::InitUserPerms($GLOBALS["USER"]->GetID(), $arResult["User"]["ID"], CSocNetUser::IsCurrentUserModuleAdmin());

		if (CModule::IncludeModule('extranet') && CExtranet::IsExtranetSite())
			$arResult["CurrentUserPerms"]["Operations"]["viewfriends"] = false;
		if (IsModuleInstalled("im"))
			$arResult["CurrentUserPerms"]["Operations"]["message"] = true;

		$arResult["Urls"]["Edit"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER_EDIT"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["Friends"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER_FRIENDS"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["FriendsAdd"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER_FRIENDS_ADD"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["FriendsDelete"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER_FRIENDS_DELETE"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["Groups"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER_GROUPS"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["Search"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_SEARCH"], array());
		$arResult["Urls"]["GroupsAdd"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_CREATE"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["MessageForm"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_MESSAGE_FORM"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["Features"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER_FEATURES"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["Subscribe"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER_SUBSCRIBE"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["SubscribeList"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_SUBSCRIBE"], array());
		$arResult["Urls"]["MessageChat"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_MESSAGES_CHAT"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["UserMessages"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_MESSAGES_USERS_MESSAGES"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["Settings"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER_SETTINGS_EDIT"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["RequestGroup"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_REQUEST_GROUP_SEARCH"], array("user_id" => $arResult["User"]["ID"]));
		$arResult["Urls"]["GroupSearch"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_SEARCH"], array());

		$arResult["Urls"]["Log"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_LOG"], array());

		$arResult["Urls"]["LogGroups"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_LOG"], array());
		$arResult["Urls"]["LogGroups"] .= ((StrPos($arResult["Urls"]["LogGroups"], "?") !== false) ? "&" : "?")."flt_entity_type=".SONET_ENTITY_GROUP;

		$arResult["Urls"]["LogUsers"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_LOG"], array());
		$arResult["Urls"]["LogUsers"] .= ((StrPos($arResult["Urls"]["LogUsers"], "?") !== false) ? "&" : "?")."flt_entity_type=".SONET_ENTITY_USER;

		$arResult["Urls"]["Activity"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_ACTIVITY"], array());

		$arResult["Urls"]["VideoCall"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_VIDEO_CALL"], array("user_id" => $arResult["User"]["ID"]));

		$arResult["ALLOW_CREATE_GROUP"] = (CSocNetUser::IsCurrentUserModuleAdmin() || $GLOBALS["APPLICATION"]->GetGroupRight("socialnetwork", false, "Y", "Y", array(SITE_ID, false)) >= "K");

		if(!CModule::IncludeModule("video"))
			$arResult["CurrentUserPerms"]["Operations"]["videocall"] = false;
		elseif(!CVideo::CanUserMakeCall())
			$arResult["CurrentUserPerms"]["Operations"]["videocall"] = false;

		$arResult["IS_ONLINE"] = ($arResult["User"]["IS_ONLINE"] == "Y");

		if (CModule::IncludeModule('intranet'))
		{
			$arResult['IS_HONOURED'] = CIntranetUtils::IsUserHonoured($arResult["User"]["ID"]);
			$arResult['IS_ABSENT'] = CIntranetUtils::IsUserAbsent($arResult["User"]["ID"], $arParams['CALENDAR_USER_IBLOCK_ID']);

			//departments and managers
			$obCache = new CPHPCache;
			$path = "/user_card_".intval($arResult["User"]["ID"] / 100);
			if($arParams["CACHE_TIME"] == 0 || $obCache->StartDataCache($arParams["CACHE_TIME"], $arResult["User"]["ID"], $path))
			{
				if($arParams["CACHE_TIME"] > 0 && defined("BX_COMP_MANAGED_CACHE"))
				{
					$GLOBALS["CACHE_MANAGER"]->StartTagCache($path);
					$GLOBALS["CACHE_MANAGER"]->RegisterTag("USER_CARD_".intval($arResult["User"]["ID"] / 100));
				}

				//departments
				$arResult['DEPARTMENTS'] = array();
				$dbRes = CIntranetUtils::GetSubordinateDepartmentsList($arResult["User"]["ID"]);
				while ($arRes = $dbRes->GetNext())
				{
					$arRes['URL'] = str_replace('#ID#', $arRes['ID'], $arParams['PATH_TO_CONPANY_DEPARTMENT']);

					$arResult['DEPARTMENTS'][$arRes['ID']] = $arRes;
					$arResult['DEPARTMENTS'][$arRes['ID']]['EMPLOYEE_COUNT'] = 0;

					$rsUsers = CIntranetUtils::GetDepartmentEmployees(array($arRes['ID']), $bRecursive=true);
					while($arUser = $rsUsers->Fetch())
					{
						if($arUser['ID'] <> $arResult["User"]["ID"]) //self
							$arResult['DEPARTMENTS'][$arRes['ID']]['EMPLOYEE_COUNT'] ++;
					}
				}

				//managers
				$arResult['MANAGERS'] = CIntranetUtils::GetDepartmentManager($arResult["User"]["UF_DEPARTMENT"], $arResult["User"]["ID"], true);

				if($arParams["CACHE_TIME"] > 0)
				{
					$obCache->EndDataCache(array(
						'DEPARTMENTS' => $arResult['DEPARTMENTS'],
						'MANAGERS' => $arResult['MANAGERS'],
					));
					if(defined("BX_COMP_MANAGED_CACHE"))
						$GLOBALS["CACHE_MANAGER"]->EndTagCache();
				}
			}
			elseif($arParams["CACHE_TIME"] > 0)
			{
				$vars = $obCache->GetVars();
				$arResult['DEPARTMENTS'] = $vars['DEPARTMENTS'];
				$arResult['MANAGERS'] = $vars['MANAGERS'];
			}
			
			if (
				CModule::IncludeModule("extranet")
				&& CExtranet::IsExtranetSite()
				&& !CExtranet::IsIntranetUser()
			)
				$arResult['MANAGERS'] = array();
		}
		if ($arResult["User"]['PERSONAL_BIRTHDAY'] <> '')
		{
			$arBirthDate = ParseDateTime($arResult["User"]['PERSONAL_BIRTHDAY'], CSite::GetDateFormat('SHORT'));
			$arResult['IS_BIRTHDAY'] = (intval($arBirthDate['MM']) == date('n') && intval($arBirthDate['DD']) == date('j'));
		}

		if (strlen($arParams["NAME_TEMPLATE"]) <= 0)
			$arParams["NAME_TEMPLATE"] = CSite::GetNameFormat();

		$arParams["TITLE_NAME_TEMPLATE"] = str_replace(
			array("#NOBR#", "#/NOBR#"),
			array("", ""),
			$arParams["NAME_TEMPLATE"]
		);
		$bUseLogin = $arParams['SHOW_LOGIN'] != "N" ? true : false;

		$arTmpUser = array(
				"NAME" => $arResult["User"]["~NAME"],
				"LAST_NAME" => $arResult["User"]["~LAST_NAME"],
				"SECOND_NAME" => $arResult["User"]["~SECOND_NAME"],
				"LOGIN" => $arResult["User"]["~LOGIN"],
		);

		$strTitleFormatted = CUser::FormatName($arParams['TITLE_NAME_TEMPLATE'], $arTmpUser, $bUseLogin);

		if ($arParams["SET_TITLE"] == "Y")
				$APPLICATION->SetTitle($strTitleFormatted.": ".GetMessage("SONET_C38_PAGE_TITLE"));

		if (!$arParams["SHORT_FORM"] && $arParams["SET_NAV_CHAIN"] != "N")
			$APPLICATION->AddChainItem($strTitleFormatted);

		$arResult["User"]["NAME_FORMATTED"] = CUser::FormatName($arParams["NAME_TEMPLATE"], $arTmpUser, $bUseLogin);

		if (intval($arParams["AVATAR_SIZE"]) > 0)
			$iSize = $arParams["AVATAR_SIZE"];
		elseif ($arParams["SHORT_FORM"])
			$iSize = 150;
		else
			$iSize = 300;

		if (intval($arResult["User"]["PERSONAL_PHOTO"]) <= 0)
		{
			switch ($arResult["User"]["PERSONAL_GENDER"])
			{
				case "M":
					$suffix = "male";
					break;
				case "F":
					$suffix = "female";
					break;
				default:
					$suffix = "unknown";
			}
			$arResult["User"]["PERSONAL_PHOTO"] = COption::GetOptionInt("socialnetwork", "default_user_picture_".$suffix, false, SITE_ID);
		}

		$arImage = CSocNetTools::InitImage($arResult["User"]["PERSONAL_PHOTO"], $iSize, "/bitrix/images/socialnetwork/nopic_user_150.gif", 150, "", false);

		$arResult["User"]["PersonalPhotoFile"] = $arImage["FILE"];
		$arResult["User"]["PersonalPhotoImg"] = $arImage["IMG"];

		$bIntranet = (IsModuleInstalled('intranet') && (!CModule::IncludeModule("extranet") || !CExtranet::IsExtranetSite()));

		if ($arResult["CurrentUserPerms"]["Operations"]["viewprofile"])
		{
			$arResult["User"]["PERSONAL_LOCATION"] = GetCountryByID($arResult["User"]["PERSONAL_COUNTRY"]);
			if (strlen($arResult["User"]["PERSONAL_LOCATION"])>0 && strlen($arResult["User"]["PERSONAL_CITY"])>0)
				$arResult["User"]["PERSONAL_LOCATION"] .= ", ";
			$arResult["User"]["PERSONAL_LOCATION"] .= $arResult["User"]["PERSONAL_CITY"];

			$arResult["User"]["WORK_LOCATION"] = GetCountryByID($arResult["User"]["WORK_COUNTRY"]);
			if (strlen($arResult["User"]["WORK_LOCATION"])>0 && strlen($arResult["User"]["WORK_CITY"])>0)
				$arResult["User"]["WORK_LOCATION"] .= ", ";
			$arResult["User"]["WORK_LOCATION"] .= $arResult["User"]["WORK_CITY"];

			$arResult["Sex"] = array(
				"M" => GetMessage("SONET_P_USER_SEX_M"),
				"F" => GetMessage("SONET_P_USER_SEX_F"),
			);

			if (strlen($arResult["User"]["PERSONAL_WWW"]) > 0)
				$arResult["User"]["PERSONAL_WWW"] = ((strpos($arResult["User"]["PERSONAL_WWW"], "http") === false) ? "http://" : "").$arResult["User"]["PERSONAL_WWW"];

			$arResult["UserFieldsMain"] = array("SHOW" => "N", "DATA" => array());
			$arResult["UserFieldsContact"] = array("SHOW" => "N", "DATA" => array());
			$arResult["UserFieldsPersonal"] = array("SHOW" => "N", "DATA" => array());

			$arMonths_r = array();
			for ($i = 1; $i <= 12; $i++)
				$arMonths_r[$i] = ToLower(GetMessage('MONTH_'.$i.'_S'));

			if (count($arParams["USER_FIELDS_MAIN"]) > 0
				|| count($arParams["USER_FIELDS_CONTACT"]) > 0
				|| count($arParams["USER_FIELDS_PERSONAL"]) > 0)
			{
				foreach ($arResult["User"] as $userFieldName => $userFieldValue)
				{
					if (in_array($userFieldName, $arParams["USER_FIELDS_MAIN"])
						|| in_array($userFieldName, $arParams["USER_FIELDS_CONTACT"])
						|| in_array($userFieldName, $arParams["USER_FIELDS_PERSONAL"]))
					{
						$val = $userFieldValue;
						$strSearch = "";
						switch ($userFieldName)
						{
							case 'EMAIL':
								if (StrLen($val) > 0)
									$val = '<a href="mailto:'.$val.'">'.$val.'</a>';
								break;

							case 'PERSONAL_WWW':
							case 'WORK_WWW':
								if (StrLen($val) > 0)
								{
									$valLink = $val;
									if (StrToLower(SubStr($val, 0, StrLen("http://"))) != "http://")
										$valLink = "http://".$val;
									$val = '<a href="'.$valLink.'" target="_blank">'.$val.'</a>';
								}
								break;

							case 'PERSONAL_COUNTRY':
							case 'WORK_COUNTRY':
								if (StrLen($val) > 0)
								{
									if (in_array($userFieldName, $arParams["SONET_USER_FIELDS_SEARCHABLE"]))
										$strSearch = $arParams["PATH_TO_SEARCH_INNER"].(StrPos($arParams["PATH_TO_SEARCH_INNER"], "?") !== false ? "&" : "?")."flt_".StrToLower($userFieldName)."=".UrlEncode($val);
									$val = GetCountryByID($val);
								}
								break;

							case 'PERSONAL_ICQ':
								if (StrLen($val) > 0)
									$val = $val.'<!-- <img src="http://web.icq.com/whitepages/online?icq='.$val.'&img=5" alt="" />-->';
								break;

							case 'PERSONAL_PHONE':
							case 'PERSONAL_FAX':
							case 'PERSONAL_MOBILE':
							case 'WORK_PHONE':
							case 'WORK_FAX':
								if (StrLen($val) > 0)
								{
									$valEncoded = preg_replace('/[^\d\+]+/', '', $val);
									$val = '<a href="callto:'.$valEncoded.'">'.$val.'</a>';
								}
								break;

							case 'PERSONAL_GENDER':
								if (in_array($userFieldName, $arParams["SONET_USER_FIELDS_SEARCHABLE"]))
									$strSearch = $arParams["PATH_TO_SEARCH_INNER"].(StrPos($arParams["PATH_TO_SEARCH_INNER"], "?") !== false ? "&" : "?")."flt_".StrToLower($userFieldName)."=".UrlEncode($val);
								$val = (($val == 'F') ? GetMessage("SONET_P_USER_SEX_F") : (($val == 'M') ? GetMessage("SONET_P_USER_SEX_M") : ""));
								break;

							case 'PERSONAL_BIRTHDAY':
								if (StrLen($val) > 0)
								{
									$arBirthdayTmp = CSocNetTools::Birthday($val, $arResult["User"]['PERSONAL_GENDER'], $arParams['SHOW_YEAR']);
									if (in_array($userFieldName, $arParams["SONET_USER_FIELDS_SEARCHABLE"]))
										$strSearch = $arParams["PATH_TO_SEARCH_INNER"].(StrPos($arParams["PATH_TO_SEARCH_INNER"], "?") !== false ? "&" : "?")."flt_personal_birthday_day=".UrlEncode($arBirthdayTmp["MONTH"]."-".$arBirthdayTmp["DAY"]);
									$val = $arBirthdayTmp["DATE"];
								}
								break;

							case 'WORK_LOGO':
								if (IntVal($val) > 0)
								{
									$iSize = 150;
									$arImage = CSocNetTools::InitImage($val, $iSize, "/bitrix/images/1.gif", 1, "", false);
									$val = $arImage["IMG"];
								}
								break;

							case 'TIME_ZONE':
								if($arResult["User"]["AUTO_TIME_ZONE"] <> "N")
									continue 2;
								break;

							case 'LAST_LOGIN':

								if (StrLen($val) > 0)
									$val = FormatDate($DB->DateFormatToPHP(FORMAT_DATETIME), MakeTimeStamp($val, FORMAT_DATETIME));
								break;

							default:
								if (in_array($userFieldName, $arParams["SONET_USER_FIELDS_SEARCHABLE"]))
									$strSearch = $arParams["PATH_TO_SEARCH_INNER"].(StrPos($arParams["PATH_TO_SEARCH_INNER"], "?") !== false ? "&" : "?")."flt_".StrToLower($userFieldName)."=".UrlEncode($val);
								break;
						}

						if (in_array($userFieldName, $arParams["USER_FIELDS_MAIN"]))
							$arResult["UserFieldsMain"]["DATA"][$userFieldName] = array("NAME" => GetMessage("SONET_UP1_".$userFieldName), "VALUE" => $val, "SEARCH" => $strSearch);
						if (in_array($userFieldName, $arParams["USER_FIELDS_CONTACT"]))
							$arResult["UserFieldsContact"]["DATA"][$userFieldName] = array("NAME" => GetMessage("SONET_UP1_".$userFieldName), "VALUE" => $val, "SEARCH" => $strSearch);
						if (in_array($userFieldName, $arParams["USER_FIELDS_PERSONAL"]))
							$arResult["UserFieldsPersonal"]["DATA"][$userFieldName] = array("NAME" => GetMessage("SONET_UP1_".$userFieldName), "VALUE" => $val, "SEARCH" => $strSearch);
					}
				}
				if (count($arResult["UserFieldsMain"]["DATA"]) > 0)
					$arResult["UserFieldsMain"]["SHOW"] = "Y";
				if (count($arResult["UserFieldsContact"]["DATA"]) > 0)
					$arResult["UserFieldsContact"]["SHOW"] = "Y";
				if (count($arResult["UserFieldsPersonal"]["DATA"]) > 0)
					$arResult["UserFieldsPersonal"]["SHOW"] = "Y";
			}

			// USER PROPERIES
			$arResult["UserPropertiesMain"] = array("SHOW" => "N", "DATA" => array());
			$arResult["UserPropertiesContact"] = array("SHOW" => "N", "DATA" => array());
			$arResult["UserPropertiesPersonal"] = array("SHOW" => "N", "DATA" => array());
			if (count($arParams["USER_PROPERTY_MAIN"]) > 0
				|| count($arParams["USER_PROPERTY_CONTACT"]) > 0
				|| count($arParams["USER_PROPERTY_PERSONAL"]) > 0)
			{
				$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER", $arResult["User"]["ID"], LANGUAGE_ID);
				foreach ($arUserFields as $fieldName => $arUserField)
				{
					//echo "<pre>".print_r($arUserField, true)."</pre>";
					$arUserField["EDIT_FORM_LABEL"] = StrLen($arUserField["EDIT_FORM_LABEL"]) > 0 ? $arUserField["EDIT_FORM_LABEL"] : $arUserField["FIELD_NAME"];
					$arUserField["EDIT_FORM_LABEL"] = htmlspecialcharsEx($arUserField["EDIT_FORM_LABEL"]);
					$arUserField["~EDIT_FORM_LABEL"] = $arUserField["EDIT_FORM_LABEL"];

					$arUserField["PROPERTY_VALUE_LINK"] = "";
					if (in_array($arUserField["FIELD_NAME"], $arParams["SONET_USER_PROPERTY_SEARCHABLE"]))
						$arUserField["PROPERTY_VALUE_LINK"] = $arParams["PATH_TO_SEARCH_INNER"].(StrPos($arParams["PATH_TO_SEARCH_INNER"], "?") !== false ? "&" : "?")."flt_".StrToLower($arUserField["FIELD_NAME"])."=#VALUE#";
					elseif ($bIntranet)
						$arUserField['SETTINGS']['SECTION_URL'] = $arParams["PATH_TO_CONPANY_DEPARTMENT"];

					if (in_array($fieldName, $arParams["USER_PROPERTY_MAIN"]))
						$arResult["UserPropertiesMain"]["DATA"][$fieldName] = $arUserField;
					if (in_array($fieldName, $arParams["USER_PROPERTY_CONTACT"]))
						$arResult["UserPropertiesContact"]["DATA"][$fieldName] = $arUserField;
					if (in_array($fieldName, $arParams["USER_PROPERTY_PERSONAL"]))
						$arResult["UserPropertiesPersonal"]["DATA"][$fieldName] = $arUserField;
				}
				if (count($arResult["UserPropertiesMain"]["DATA"]) > 0)
					$arResult["UserPropertiesMain"]["SHOW"] = "Y";
				if (count($arResult["UserPropertiesContact"]["DATA"]) > 0)
					$arResult["UserPropertiesContact"]["SHOW"] = "Y";
				if (count($arResult["UserPropertiesPersonal"]["DATA"]) > 0)
					$arResult["UserPropertiesPersonal"]["SHOW"] = "Y";
			}

			if (!$arParams["SHORT_FORM"])
			{
				// USER FRIENDS
				$arResult["Friends"] = false;
				if (CSocNetUser::IsFriendsAllowed() && $arResult["CurrentUserPerms"]["Operations"]["viewfriends"])
				{
					$dbFriends = CSocNetUserRelations::GetRelatedUsers($arResult["User"]["ID"], SONET_RELATIONS_FRIEND, array("nTopCount" => $arParams["ITEMS_COUNT"]));
					if ($dbFriends)
					{
						$arResult["Friends"] = array();
						$arResult["Friends"]["Count"] = CSocNetUserRelations::GetList(array(), array("USER_ID" => $arResult["User"]["ID"], "RELATION" => SONET_RELATIONS_FRIEND), array());

						$arResult["Friends"]["List"] = false;
						while ($arFriends = $dbFriends->GetNext())
						{
							if ($arResult["Friends"]["List"] == false)
								$arResult["Friends"]["List"] = array();

							$pref = ((IntVal($arResult["User"]["ID"]) == $arFriends["FIRST_USER_ID"]) ? "SECOND" : "FIRST");

							$pu = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $arFriends[$pref."_USER_ID"]));
							$canViewProfile = CSocNetUserPerms::CanPerformOperation($GLOBALS["USER"]->GetID(), $arFriends[$pref."_USER_ID"], "viewprofile", CSocNetUser::IsCurrentUserModuleAdmin());

							if (intval($arParams["THUMBNAIL_LIST_SIZE"]) > 0)
							{
								if (intval($arFriends[$pref."_USER_PERSONAL_PHOTO"]) <= 0)
								{
									switch ($arFriends[$pref."_USER_PERSONAL_GENDER"])
									{
										case "M":
											$suffix = "male";
											break;
										case "F":
											$suffix = "female";
											break;
										default:
											$suffix = "unknown";
									}
									$arFriends[$pref."_USER_PERSONAL_PHOTO"] = COption::GetOptionInt("socialnetwork", "default_user_picture_".$suffix, false, SITE_ID);
								}
								$arImage = CSocNetTools::InitImage($arFriends[$pref."_USER_PERSONAL_PHOTO"], $arParams["THUMBNAIL_LIST_SIZE"], "/bitrix/images/socialnetwork/nopic_30x30.gif", 30, $pu, $canViewProfile);
							}
							else // old
								$arImage = CSocNetTools::InitImage($arFriends[$pref."_USER_PERSONAL_PHOTO"], 50, "/bitrix/images/socialnetwork/nopic_user_50.gif", 50, $pu, $canViewProfile);


							$arResult["Friends"]["List"][] = array(
								"ID" => $arFriends["ID"],
								"USER_ID" => $arFriends[$pref."_USER_ID"],
								"USER_NAME" => $arFriends[$pref."_USER_NAME"],
								"USER_LAST_NAME" => $arFriends[$pref."_USER_LAST_NAME"],
								"USER_SECOND_NAME" => $arFriends[$pref."_USER_SECOND_NAME"],
								"USER_LOGIN" => $arFriends[$pref."_USER_LOGIN"],
								"USER_PERSONAL_PHOTO" => $arFriends[$pref."_USER_PERSONAL_PHOTO"],
								"USER_PERSONAL_PHOTO_FILE" => $arImage["FILE"],
								"USER_PERSONAL_PHOTO_IMG" => $arImage["IMG"],
								"USER_PROFILE_URL" => $pu,
								"SHOW_PROFILE_LINK" => $canViewProfile,
							);
						}
					}
				}


				// USER GROUPS
				$arResult["Groups"] = false;
				if ($arResult["CurrentUserPerms"]["Operations"]["viewgroups"])
				{
					$arGroupFilter = array(
						"USER_ID" => $arResult["User"]["ID"],
						"<=ROLE" => SONET_ROLES_USER,
						"GROUP_SITE_ID" => SITE_ID,
						"GROUP_ACTIVE" => "Y"
					);

					if (COption::GetOptionString("socialnetwork", "work_with_closed_groups", "N") != "Y")
						$arGroupFilter["GROUP_CLOSED"] = "N";

					if (CModule::IncludeModule('extranet') && CExtranet::IsExtranetSite()):

						if (!$GLOBALS["USER"]->IsAdmin() && !CSocNetUser::IsCurrentUserModuleAdmin()):

							$arGroupFilterMy = array(
								"USER_ID" => $GLOBALS["USER"]->GetID(),
								"<=ROLE" => SONET_ROLES_USER,
								"GROUP_SITE_ID" => SITE_ID,
								"GROUP_ACTIVE" => "Y"
							);

							$dbGroups = CSocNetUserToGroup::GetList(
								array(),
								$arGroupFilterMy,
								false,
								false,
								array("GROUP_ID")
							);

							$arMyGroups = array();
							while ($arGroups = $dbGroups->GetNext())
								$arMyGroups[] = $arGroups["GROUP_ID"];

							$arGroupFilter["GROUP_ID"] = $arMyGroups;

						endif;

					else:
						if ($arResult["User"]["ID"] != $USER->GetID() && !CSocNetUser::IsCurrentUserModuleAdmin())
							$arGroupFilter["GROUP_VISIBLE"] = "Y";
					endif;

					$dbGroups = CSocNetUserToGroup::GetList(
						array("GROUP_DATE_ACTIVITY" => "DESC", "GROUP_NAME" => "ASC"),
						$arGroupFilter,
						false,
						false,
						array("ID", "GROUP_ID", "GROUP_NAME")
					);

					if ($dbGroups)
					{
						$arResult["Groups"] = array();
						$arResult["Groups"]["Count"] = 0;
						$arResult["Groups"]["List"] = false;
						$arResult["Groups"]["ListFull"] = false;
						while ($arGroups = $dbGroups->GetNext())
						{
							if ($arResult["Groups"]["ListFull"] == false)
								$arResult["Groups"]["ListFull"] = array();
							$arResult["Groups"]["Count"]++;
							$arResult["Groups"]["ListFull"][] = array(
								"ID" => $arGroups["ID"],
								"GROUP_ID" => $arGroups["GROUP_ID"],
								"GROUP_NAME" => $arGroups["GROUP_NAME"],
								"GROUP_URL" => CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP"], array("group_id" => $arGroups["GROUP_ID"])),
							);
						}
						if (is_array($arResult["Groups"]["ListFull"]))
							$arResult["Groups"]["List"] = array_slice($arResult["Groups"]["ListFull"], 0, $arParams["ITEMS_COUNT"]);
					}

				}

				//Blog
				$arResult["ActiveFeatures"] = CSocNetFeatures::GetActiveFeaturesNames(SONET_ENTITY_USER, $arResult["User"]["ID"]);

				$arResult["BLOG"] = array("SHOW" => false, "TITLE" => GetMessage("SONET_C39_BLOG_TITLE"));
				if(array_key_exists("blog", $arResult["ActiveFeatures"]) && (CSocNetFeaturesPerms::CanPerformOperation($USER->GetID(), SONET_ENTITY_USER, $arResult["User"]["ID"], "blog", "view_post", CSocNetUser::IsCurrentUserModuleAdmin()) || $APPLICATION->GetGroupRight("blog") >= "W") && CModule::IncludeModule("blog"))
				{
					$arResult["BLOG"]["SHOW"] = true;
					if (StrLen($arResult["ActiveFeatures"]["blog"]) > 0)
						$arResult["BLOG"]["TITLE"] = $arResult["ActiveFeatures"]["blog"];
				}

				$arResult["forum"] = array("SHOW" => false, "TITLE" => GetMessage("SONET_C39_FORUM_TITLE"));
				if(array_key_exists("forum", $arResult["ActiveFeatures"]) && (CSocNetFeaturesPerms::CanPerformOperation($USER->GetID(), SONET_ENTITY_USER, $arResult["User"]["ID"], "forum", "view", CSocNetUser::IsCurrentUserModuleAdmin())  || $APPLICATION->GetGroupRight("forum") >= "W") && CModule::IncludeModule("forum"))
				{
					$arResult["forum"]["SHOW"] = true;
					if (StrLen($arResult["ActiveFeatures"]["forum"]) > 0)
						$arResult["forum"]["TITLE"] = $arResult["ActiveFeatures"]["forum"];
				}

				$arResult["tasks"] = array("SHOW" => false, "TITLE" => GetMessage("SONET_C39_TASKS_TITLE"));
				if(array_key_exists("tasks", $arResult["ActiveFeatures"]) && (CSocNetFeaturesPerms::CanPerformOperation($USER->GetID(), SONET_ENTITY_USER, $arResult["User"]["ID"], "tasks", "view", CSocNetUser::IsCurrentUserModuleAdmin())  || $APPLICATION->GetGroupRight("intranet") >= "W") && CModule::IncludeModule("intranet"))
				{
					$arResult["tasks"]["SHOW"] = true;
					if (StrLen($arResult["ActiveFeatures"]["tasks"]) > 0)
						$arResult["tasks"]["TITLE"] = $arResult["ActiveFeatures"]["tasks"];
				}
			}
		}

		if (
			array_key_exists("RatingMultiple", $arResult)
			&& count($arResult["RatingMultiple"]) > 0
		)
			foreach($arParams["RATING_ID_ARR"] as $rating_id)
				if (array_key_exists($rating_id, $arResult["RatingMultiple"]))
					$arResult["RatingMultiple"][$rating_id]["VALUE"] = $arResult["User"]["RATING_".$rating_id."_CURRENT_VALUE"];
	}
}
$this->IncludeComponentTemplate();

return array(
	"NAME" => $arResult["User"]["NAME_FORMATTED"],
);
?>