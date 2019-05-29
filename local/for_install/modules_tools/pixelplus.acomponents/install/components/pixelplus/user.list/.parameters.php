<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if (!CModule::IncludeModule('iblock')) return;

$arProperty_UF = array();
$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER");
foreach($arUserFields as $FIELD_NAME=>$arUserField) {
	$arProperty_UF[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME;
}

$rsGroup = CGroup::GetList(
 ($by = "c_sort"),
 ($order = "asc"),
 array(),
 "N"
);
while ($arRes = $rsGroup->GetNext()) {
	$arGroups[$arRes['ID']] = "[".$arRes['ID']."] ".$arRes['NAME'];
}


$arAscDesc = array(
	"asc" => GetMessage("CP_PXUL_USER_SORT_ASC"),
	"desc" => GetMessage("CP_PXUL_USER_SORT_DESC"),
);




$arUserFields = Array(
	"ID" => GetMessage("CP_PXUL_USER_FIELD_ID"),
	"ACTIVE" => GetMessage("CP_PXUL_USER_FIELD_ACTIVE"),
	"LAST_LOGIN" => GetMessage("CP_PXUL_USER_FIELD_LAST_LOGIN"),
	"LOGIN" => GetMessage("CP_PXUL_USER_FIELD_LOGIN"),
	"EMAIL" => GetMessage("CP_PXUL_USER_FIELD_EMAIL"),
	"NAME" => GetMessage("CP_PXUL_USER_FIELD_NAME"),	
	"LAST_NAME" => GetMessage("CP_PXUL_USER_FIELD_LAST_NAME"),
	"SECOND_NAME" => GetMessage("CP_PXUL_USER_FIELD_SECOND_NAME"),
	"TIMESTAMP_X" => GetMessage("CP_PXUL_USER_FIELD_TIMESTAMP_X"),
	"PERSONAL_BIRTHDAY" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_BIRTHDAY"),
	"DATE_REGISTER" => GetMessage("CP_PXUL_USER_FIELD_DATE_REGISTER"),
	"PERSONAL_PROFESSION" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_PROFESSION"),
	"PERSONAL_WWW" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_WWW"),
	"PERSONAL_ICQ" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_ICQ"),
	"PERSONAL_GENDER" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_GENDER"),
	"PERSONAL_PHOTO" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_PHOTO"),
	"PERSONAL_PHONE" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_PHONE"),
	"PERSONAL_FAX" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_FAX"),
	"PERSONAL_MOBILE" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_MOBILE"),
	"PERSONAL_PAGER" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_PAGER"),
	"PERSONAL_STREET" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_STREET"),
	"PERSONAL_MAILBOX" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_MAILBOX"),
	"PERSONAL_CITY" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_CITY"),
	"PERSONAL_STATE" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_STATE"),
	"PERSONAL_ZIP" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_ZIP"),
	"PERSONAL_COUNTRY" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_COUNTRY"),
	"PERSONAL_NOTES" => GetMessage("CP_PXUL_USER_FIELD_PERSONAL_NOTES"),
	"WORK_COMPANY" => GetMessage("CP_PXUL_USER_FIELD_WORK_COMPANY"),
	"WORK_DEPARTMENT" => GetMessage("CP_PXUL_USER_FIELD_WORK_DEPARTMENT"),
	"WORK_POSITION" => GetMessage("CP_PXUL_USER_FIELD_WORK_POSITION"),
	"WORK_WWW" => GetMessage("CP_PXUL_USER_FIELD_WORK_WWW"),
	"WORK_PHONE" => GetMessage("CP_PXUL_USER_FIELD_WORK_PHONE"),
	"WORK_FAX" => GetMessage("CP_PXUL_USER_FIELD_WORK_FAX"),
	"WORK_PAGER" => GetMessage("CP_PXUL_USER_FIELD_WORK_PAGER"),
	"WORK_STREET" => GetMessage("CP_PXUL_USER_FIELD_WORK_STREET"),
	"WORK_MAILBOX" => GetMessage("CP_PXUL_USER_FIELD_WORK_MAILBOX"),
	"WORK_CITY" => GetMessage("CP_PXUL_USER_FIELD_WORK_CITY"),
	"WORK_STATE" => GetMessage("CP_PXUL_USER_FIELD_WORK_STATE"),
	"WORK_ZIP" => GetMessage("CP_PXUL_USER_FIELD_WORK_ZIP"),
	"WORK_COUNTRY" => GetMessage("CP_PXUL_USER_FIELD_WORK_COUNTRY"),
	"WORK_PROFILE" => GetMessage("CP_PXUL_USER_FIELD_WORK_PROFILE"),
	"WORK_NOTES" => GetMessage("CP_PXUL_USER_FIELD_WORK_NOTES"),
	"ADMIN_NOTES" => GetMessage("CP_PXUL_USER_FIELD_ADMIN_NOTES"),
	"XML_ID" => GetMessage("CP_PXUL_USER_FIELD_XML_ID")
);

$arComponentParameters = array(
	"GROUPS" => array(
		"FORUM_GROUPS" => array(
			"NAME" => GetMessage("CP_PXUL_FORUM_GROUPS")
		)
	),
	"PARAMETERS" => array(
		"AJAX_MODE" => array(),
		"USER_USER_S_FIELDS" =>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_USER_USER_S_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arUserFields,
			"REFRESH" => "Y"
		),	
		"USER_USER_F_FIELDS" =>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("USER_USER_F_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => Array()
		),
		"USER_USER_S_PROPERTIES" =>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_USER_USER_S_PROPERTIES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProperty_UF,
			"REFRESH" => "Y"
		),
		"USER_USER_F_PROPERTIES" =>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_USER_USER_F_PROPERTIES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => Array()
		),
		"USER_SORT_FIELD" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_USER_SORT_FIELD"),
			"TYPE" => "LIST",		
			"VALUES" => $arUserFields,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "ID",
		),
		"USER_SORT_ORDER" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_USER_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"USER_SORT_FIELD_2" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_USER_SORT_FIELD_2"),
			"TYPE" => "LIST",		
			"VALUES" => $arUserFields,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "NAME",
		),
		"USER_SORT_ORDER_2" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_USER_SORT_ORDER_2"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),	
		"SELECT_USER_IN_GROUPS"=>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_SELECT_USER_IN_GROUPS"),
			"TYPE" => "LIST",
			"VALUES" => $arGroups,
			"ADDITIONAL_VALUES" => "Y",
			"MULTIPLE"=>"Y"
		),
		"FILTER_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_FILTER_NAME_IN"),
			"TYPE" => "STRING",
			"DEFAULT" => "arrFilter",
		),
		"DETAIL_URL" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_USER_DETAIL_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"PAGE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_PXUL_PAGE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "30",
		),
		"LINE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_PXUL_LINE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "3",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_FILTER" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_PXUL_CACHE_FILTER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_PXUL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_CATALOG"), true, true);

//Поля пользователя для вывода
$bunsetuserffields = true;
if (count($arCurrentValues['USER_USER_S_FIELDS'])) {
	$arUserFieldsF = Array();
	foreach ($arCurrentValues['USER_USER_S_FIELDS'] as $k=>$v) {
		if($v!=="" && $arUserFields[$v]) {
			$arUserFieldsF[$v] = $arUserFields[$v];
		}
	}
	if (count($arUserFieldsF)) {
		$bunsetuserffields = false;
		$arComponentParameters["PARAMETERS"]["USER_USER_F_FIELDS"] = array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_USER_USER_F_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arUserFieldsF,
			"SORT"=>10
		);
	}
}

if ($bunsetuserffields === true) unset($arComponentParameters["PARAMETERS"]["USER_USER_F_FIELDS"]);


//Свойства пользователя для вывода
$bunsetuserfproperties = true;
if (count($arCurrentValues['USER_USER_S_PROPERTIES'])) {
	$arUserPropertiesF = Array();
	foreach ($arCurrentValues['USER_USER_S_PROPERTIES'] as $k=>$v) {
		if($v!=="" && $arProperty_UF[$v]) {
			$arUserPropertiesF[$v] = $arProperty_UF[$v];
		}
	}
	if (count($arUserPropertiesF)) {
		$bunsetuserfproperties = false;
		$arComponentParameters["PARAMETERS"]["USER_USER_F_PROPERTIES"] = array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXUL_USER_USER_F_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arUserPropertiesF,
			"SORT"=>10
		);
	}
}

if ($bunsetuserfproperties === true) unset($arComponentParameters["PARAMETERS"]["USER_USER_F_PROPERTIES"]);

if (CheckVersion(SM_VERSION,"11.0.13") === false) {
	unset($arComponentParameters["PARAMETERS"]["USER_SORT_FIELD_2"]);
	unset($arComponentParameters["PARAMETERS"]["USER_SORT_ORDER_2"]);
}


if (IsModuleInstalled('forum')) {
	$arComponentParameters["PARAMETERS"]['USE_FORUM'] = Array(
		"PARENT" => "FORUM_GROUPS",
		"NAME" => GetMessage("CP_PXUL_USE_FORUM"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y"
	);
	$arForumUserFields = Array(
		"ID" => GetMessage("CP_PXUL_FORUM_ID"),
		"DESCRIPTION" => GetMessage("CP_PXUL_FORUM_DESCRIPTION"),
		"IP_ADDRESS" => GetMessage("CP_PXUL_FORUM_IP_ADDRESS"),
		"AVATAR" => GetMessage("CP_PXUL_FORUM_AVATAR"),
		"INTERESTS" => GetMessage("CP_PXUL_FORUM_INTERESTS"),
		"NUM_POSTS" => GetMessage("CP_PXUL_FORUM_NUM_POSTS"),
		"LAST_VISIT" => GetMessage("CP_PXUL_FORUM_LAST_VISIT"),
		"DATE_REG" => GetMessage("CP_PXUL_FORUM_DATE_REG"),
		"REAL_IP_ADDRESS" => GetMessage("CP_PXUL_FORUM_REAL_IP_ADDRESS"),
		"SIGNATURE" => GetMessage("CP_PXUL_FORUM_SIGNATURE"),
	);
	
	if ($arCurrentValues['USE_FORUM'] == "Y") {
		$arComponentParameters["PARAMETERS"]['FORUM_USER_FIELDS'] = array(
			"PARENT" => "FORUM_GROUPS",
			"NAME" => GetMessage("CP_PXUL_USER_USER_F_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arForumUserFields,
		);
	}
}
?>