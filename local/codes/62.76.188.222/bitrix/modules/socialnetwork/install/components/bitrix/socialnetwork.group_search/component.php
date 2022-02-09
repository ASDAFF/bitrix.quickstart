<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("socialnetwork"))
{
	ShowError(GetMessage("SONET_MODULE_NOT_INSTALL"));
	return;
}

$bSearchInstalled = IsModuleInstalled("search");

if ($bSearchInstalled)
	CModule::IncludeModule("search");

$arParams["SET_NAV_CHAIN"] = ($arParams["SET_NAV_CHAIN"] == "N" ? "N" : "Y");

if (strLen($arParams["GROUP_VAR"]) <= 0)
	$arParams["GROUP_VAR"] = "group_id";
if (strLen($arParams["PAGE_VAR"]) <= 0)
	$arParams["PAGE_VAR"] = "page";
if (strLen($arParams["USER_VAR"]) <= 0)
	$arParams["USER_VAR"] = "user_id";

$arParams["PATH_TO_GROUP"] = trim($arParams["PATH_TO_GROUP"]);
if (strlen($arParams["PATH_TO_GROUP"]) <= 0)
	$arParams["PATH_TO_GROUP"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group&".$arParams["GROUP_VAR"]."=#group_id#");

$arParams["PATH_TO_GROUP_SEARCH"] = trim($arParams["PATH_TO_GROUP_SEARCH"]);
if (strlen($arParams["PATH_TO_GROUP_SEARCH"]) <= 0)
	$arParams["PATH_TO_GROUP_SEARCH"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group_search");

$arParams["PATH_TO_GROUP_CREATE"] = trim($arParams["PATH_TO_GROUP_CREATE"]);
if (strlen($arParams["PATH_TO_GROUP_CREATE"]) <= 0)
	$arParams["PATH_TO_GROUP_CREATE"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group_create&".$arParams["USER_VAR"]."=#user_id#");

$arParams["ITEMS_COUNT"] = IntVal($arParams["ITEMS_COUNT"]);
if ($arParams["ITEMS_COUNT"] <= 0)
	$arParams["ITEMS_COUNT"] = 20;

$arParams["DATE_TIME_FORMAT"] = Trim($arParams["DATE_TIME_FORMAT"]);
$arParams["DATE_TIME_FORMAT"] = ((StrLen($arParams["DATE_TIME_FORMAT"]) <= 0) ? $DB->DateFormatToPHP(CSite::GetDateFormat("FULL")) : $arParams["DATE_TIME_FORMAT"]);

$arParams["SUBJECT_ID"] = IntVal($arParams["SUBJECT_ID"]);

$arResult["~q"] = trim($_REQUEST["q"]);
$arResult["~tags"] = trim($_REQUEST["tags"]);
$arResult["~subject"] = trim(array_key_exists("subject", $_REQUEST) ? $_REQUEST["subject"] : ($arParams["SUBJECT_ID"] > 0 ? $arParams["SUBJECT_ID"] : ""));
$arResult["~how"] = trim($_REQUEST["how"]);

$arResult["q"] = htmlspecialcharsbx($arResult["~q"]);
$arResult["tags"] = htmlspecialcharsbx($arResult["~tags"]);
$arResult["subject"] = htmlspecialcharsbx($arResult["~subject"]);
$arResult["how"] = htmlspecialcharsbx($arResult["~how"]);

if ($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(GetMessage("SONET_C24_PAGE_TITLE"));

if ($arParams["SET_NAV_CHAIN"] != "N")
	$APPLICATION->AddChainItem(GetMessage("SONET_C24_PAGE_TITLE"));

$arResult["Urls"]["GroupSearch"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_SEARCH"], array());
$arResult["Urls"]["GroupCreate"] = "";
$arResult["ALLOW_CREATE_GROUP"] = false;
if ($GLOBALS["USER"]->IsAuthorized())
{
	$arResult["Urls"]["GroupCreate"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_CREATE"], array("user_id" => $GLOBALS["USER"]->GetID()));
	$arResult["ALLOW_CREATE_GROUP"] = (CSocNetUser::IsCurrentUserModuleAdmin() || $GLOBALS["APPLICATION"]->GetGroupRight("socialnetwork", false, "Y", "Y", array(SITE_ID, false)) >= "K");
}

$arResult["SEARCH_RESULT"] = Array();

if ($bSearchInstalled && (strlen($arResult["~q"]) > 0 || strlen($arResult["~tags"]) > 0))
{
	$arFilter = array(
		"SITE_ID" => SITE_ID,
		"QUERY" => $arResult["~q"],
		array(
			"=MODULE_ID" => "socialnetwork",
			"ITEM_ID" => "G%",
		),
		"CHECK_DATES" => "Y",
		"TAGS" => $arResult["~tags"],
	);
	if (strlen($arResult["~subject"]) > 0)
		$arFilter["PARAM1"] = $arResult["~subject"];
	if ($arResult["~how"] == "d")
		$aSort = array("DATE_CHANGE" => "DESC", "CUSTOM_RANK" => "DESC", "RANK" => "DESC");
	else
		$aSort = array("CUSTOM_RANK" => "DESC", "RANK" => "DESC", "DATE_CHANGE" => "DESC");

	$obSearch = new CSearch();
	$obSearch->Search($arFilter, $aSort);
	if ($obSearch->errorno == 0)
	{
		$obSearch->NavStart($arParams["ITEMS_COUNT"]);
		$arResult["NAV_STRING"] = $obSearch->GetPageNavString(GetMessage("SONET_C24_GROUPS"), "");

		while ($arSearch = $obSearch->GetNext())
		{
			$arGroup = CSocNetGroup::GetByID($arSearch["PARAM2"]);

			$arSearch["URL"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP"], array("group_id" => $arGroup["ID"]));

			if (intval($arGroup["IMAGE_ID"]) <= 0)
				$arGroup["IMAGE_ID"] = COption::GetOptionInt("socialnetwork", "default_group_picture", false, SITE_ID);

			$arImage = CSocNetTools::InitImage($arGroup["IMAGE_ID"], 100, "/bitrix/images/socialnetwork/nopic_group_100.gif", 100, $arSearch["URL"], true);

			$arSearch["IMAGE_FILE"] = $arImage["FILE"];
			$arSearch["IMAGE_IMG"] = $arImage["IMG"];

			$arSearch["SUBJECT_NAME"] = $arGroup["SUBJECT_NAME"];
			$arSearch["NUMBER_OF_MEMBERS"] = $arGroup["NUMBER_OF_MEMBERS"];

			$arSearch["FULL_DATE_CHANGE_FORMATED"] = date($arParams["DATE_TIME_FORMAT"], MakeTimeStamp($arSearch["FULL_DATE_CHANGE"], CSite::GetDateFormat("FULL")));

			$arSearch["ARCHIVE"] = $arGroup["CLOSED"];
			$arResult["SEARCH_RESULT"][] = $arSearch;
		}

		if (count($arResult["SEARCH_RESULT"]) > 0)
		{
			if (strlen($arResult["~tags"]) > 0)
				$arResult["ORDER_LINK"] = $APPLICATION->GetCurPageParam("tags=".$arResult["tags"], Array("tags", "how"));
			else
				$arResult["ORDER_LINK"] = $APPLICATION->GetCurPageParam("q=".$arResult["q"], Array("q", "how"));

			if ($arResult["~how"] != "d")
				$arResult["ORDER_LINK"] .= "&how=d";
		}
		else
		{
			$arResult["ERROR_MESSAGE"] = GetMessage("SONET_C24_EMPTY");
		}
	}
	else
	{
		$arResult["ERROR_MESSAGE"] = GetMessage("SONET_C24_ERROR").$obSearch->error;
	}
}
else
{
	$arNavParams = array("nPageSize" => $arParams["ITEMS_COUNT"], "bDescPageNumbering" => false);
	$arNavigation = CDBResult::GetNavParams($arNavParams);

	$arFilterTmp = array("SITE_ID" => SITE_ID, "ACTIVE" => "Y");
	if (!CSocNetUser::IsCurrentUserModuleAdmin())
		$arFilterTmp["CHECK_PERMISSIONS"] = $GLOBALS["USER"]->GetID();

	if ($arParams["SUBJECT_ID"] > 0)
	{
		$arFilterTmp["SUBJECT_ID"] = $arParams["SUBJECT_ID"];
		
		$arCurrentSubject = CSocNetGroupSubject::GetByID($arParams["SUBJECT_ID"]);
		if ($arCurrentSubject && $arParams["SET_TITLE"] == "Y")
			$APPLICATION->SetTitle($arCurrentSubject["NAME"]);
	}

	if ($arParams["SUBJECT_ID"] == -1)
	{
		$arFilterTmp["CLOSED"] = "Y";
		if ($arParams["SET_TITLE"] == "Y")
			$APPLICATION->SetTitle(GetMessage("SONET_C24_PAGE_TITLE_ARCHIVE"));
	}
	else
		$arFilterTmp["CLOSED"] = "N";

	if (strlen($arResult["~q"]) > 0)
		$arFilterTmp["~NAME"] = "%".$arResult["~q"]."%";

	$dbGroups = CSocNetGroup::GetList(
		array("DATE_ACTIVITY" => "DESC", "NAME" => "ASC"),
		$arFilterTmp,
		false,
		$arNavParams,
		array("ID", "NAME", "DESCRIPTION", "DATE_ACTIVITY", "IMAGE_ID", "NUMBER_OF_MEMBERS", "SUBJECT_NAME", "CLOSED")
	);

	while ($arGroup = $dbGroups->GetNext())
	{
		$arGroup["TITLE_FORMATED"] = $arGroup["NAME"];
		$arGroup["BODY_FORMATED"] = $arGroup["DESCRIPTION"];

		$arGroup["URL"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP"], array("group_id" => $arGroup["ID"]));

		$arImage = CSocNetTools::InitImage($arGroup["IMAGE_ID"], 100, "/bitrix/images/socialnetwork/nopic_group_100.gif", 100, $arGroup["URL"], true);

		$arGroup["IMAGE_FILE"] = $arImage["FILE"];
		$arGroup["IMAGE_IMG"] = $arImage["IMG"];

		$arGroup["FULL_DATE_CHANGE_FORMATED"] = date($arParams["DATE_TIME_FORMAT"], MakeTimeStamp($arGroup["DATE_ACTIVITY"], CSite::GetDateFormat("FULL")));

		$arGroup["ARCHIVE"] = $arGroup["CLOSED"];
		$arResult["SEARCH_RESULT"][] = $arGroup;
	}

	$arResult["NAV_STRING"] = $dbGroups->GetPageNavStringEx($navComponentObject, GetMessage("SONET_C24_NAV"), "", false);
}

$arResult["Subjects"] = array();
$dbSubjects = CSocNetGroupSubject::GetList(
	array("SORT"=>"ASC", "NAME" => "ASC"),
	array("SITE_ID" => SITE_ID),
	false,
	false,
	array("ID", "NAME")
);
while ($arSubject = $dbSubjects->GetNext())
	$arResult["Subjects"][$arSubject["ID"]] = $arSubject["NAME"];

if (CSocNetUser::IsCurrentUserModuleAdmin() && CModule::IncludeModule('intranet')):
	global $INTRANET_TOOLBAR;

	$INTRANET_TOOLBAR->AddButton(array(
		'HREF' => "/bitrix/admin/socnet_subject.php?lang=".LANGUAGE_ID,
		"TEXT" => GetMessage('SONET_C24_EDIT_ENTRIES'),
		'ICON' => 'settings',
		"SORT" => 1000,
	));
endif;

$this->IncludeComponentTemplate();
?>