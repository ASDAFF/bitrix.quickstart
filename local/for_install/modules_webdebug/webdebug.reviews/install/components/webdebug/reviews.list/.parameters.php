<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// Info blocks
$arIBlockTypes = array();
$arIBlocks = array();
if (CModule::IncludeModule("iblock")) {
	$arIBlockTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));
	$resIBlock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
	while($arIBlock = $resIBlock->GetNext(false,false)) {
		$arIBlocks[$arIBlock["ID"]] = "[".$arIBlock["ID"]."] ".$arIBlock["NAME"];
	}
}

// Fields list
$arAllFields = array(
	"NAME" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_NAME"),
	"EMAIL" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_EMAIL"),
	"WWW" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_WWW"),
	"TEXT_PLUS" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_TEXT_PLUS"),
	"TEXT_MINUS" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_TEXT_MINUS"),
	"TEXT_COMMENTS" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_TEXT_COMMENTS"),
	"DATETIME" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_DATETIME"),
	"VOTE_0" => COption::GetOptionString("webdebug.reviews", "vote_name_0"),
	"VOTE_1" => COption::GetOptionString("webdebug.reviews", "vote_name_1"),
	"VOTE_2" => COption::GetOptionString("webdebug.reviews", "vote_name_2"),
	"VOTE_3" => COption::GetOptionString("webdebug.reviews", "vote_name_3"),
	"VOTE_4" => COption::GetOptionString("webdebug.reviews", "vote_name_4"),
	"VOTE_5" => COption::GetOptionString("webdebug.reviews", "vote_name_5"),
	"VOTE_6" => COption::GetOptionString("webdebug.reviews", "vote_name_6"),
	"VOTE_7" => COption::GetOptionString("webdebug.reviews", "vote_name_7"),
	"VOTE_8" => COption::GetOptionString("webdebug.reviews", "vote_name_8"),
	"VOTE_9" => COption::GetOptionString("webdebug.reviews", "vote_name_9"),
);

$arComponentParameters = array(
	"GROUPS" => array(
		"SOURCE" => array(
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_SOURCE"),
			"SORT" => "90",
		),
		"FIELDS" => array(
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_FIELDS"),
			"SORT" => "350",
		),
	),
	"PARAMETERS" => array(
		// BASE
		"EMAIL_PUBLIC"  =>  array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_EMAIL_PUBLIC"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
		),
		"USE_MODERATE"  =>  array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_USE_MODERATE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
		),
		// SOURCE
		"IBLOCK_TYPE" => array(
			"PARENT" => "SOURCE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockTypes,
			"DEFAULT" => "",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "SOURCE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
			"ADDITIONAL_VALUES" => "Y",
		),
		"ELEMENT_ID"  =>  array(
			"PARENT" => "SOURCE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_ELEMENT_ID"),
			"TYPE" => "TEXT",
			"DEFAULT" => $_REQUEST["ELEMENT_ID"],
		),
		"ELEMENT_CODE"  =>  array(
			"PARENT" => "SOURCE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_ELEMENT_CODE"),
			"TYPE" => "TEXT",
			"DEFAULT" => $_REQUEST["ELEMENT_CODE"],
		),
		// PAGER_SETTINGS
		"REVIEWS_COUNT"  =>  array(
			"PARENT" => "PAGER_SETTINGS",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_REVIEWS_COUNT"),
			"TYPE" => "TEXT",
			"DEFAULT" => 10,
		),
		// FIELDS
		"DISPLAY_FIELDS" => array(
			"PARENT" => "FIELDS",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELDS"),
			"TYPE" => "LIST",
			"VALUES" => $arAllFields,
			"MULTIPLE" => "Y",
			"SIZE" => "8",
			"DEFAULT" => array("NAME", "DATETIME", "EMAIL", "TEXT_PLUS", "TEXT_MINUS", "TEXT_COMMENTS", "VOTE_0", "VOTE_1", "VOTE_2"),
		),
	),
);

CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_NEWS"), true, true);

?>