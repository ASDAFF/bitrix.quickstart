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

// Events
$arEventTemplates = array();
$resEventTemplates = CEventMessage::GetList($by="ID", $order="DESC", array("TYPE_ID"=>"WEBDEBUG_REVIEWS"));
while($arEventTemplate = $resEventTemplates->GetNext()) {
	$arEventTemplates[$arEventTemplate["ID"]] = "[".$arEventTemplate["ID"]."] ".$arEventTemplate["SUBJECT"];	
}

// Fields list
$arAllFields = array(
	"NAME" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_NAME"),
	"EMAIL" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_EMAIL"),
	"WWW" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_WWW"),
	"TEXT_PLUS" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_TEXT_PLUS"),
	"TEXT_MINUS" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_TEXT_MINUS"),
	"TEXT_COMMENTS" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELD_TEXT_COMMENTS"),
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

$arAllFieldsRequired = array();
foreach ($arAllFields as $Key => $Field) {
	if (substr($Key,0,5)=="VOTE_") continue;
	$arAllFieldsRequired[$Key] = $Field;
}

$arComponentParameters = array(
	"GROUPS" => array(
		"SOURCE" => array(
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_SOURCE"),
			"SORT" => "90",
		),
		"FIELDS" => array(
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_FIELDS"),
			"SORT" => "320",
		),
	),
	"PARAMETERS" => array(
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
		// BASE
		"SUCCESS_MESSAGE"  =>  array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_SUCCESS_MESSAGE"),
			"TYPE" => "TEXT",
			"DEFAULT" => GetMessage("WEBDEBUG_REVIEWS_SUCCESS_MESSAGE_DEFAULT"),
			"COLS" => "50",
			"ROWS" => "3",
		),
		"EVENT_TEMPLATES" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_EVENT_TEMPLATES"),
			"TYPE" => "LIST",
			"VALUES" => $arEventTemplates,
			"MULTIPLE" => "Y",
			"SIZE" => "4",
		),
		"USE_CAPTCHA"  =>  array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_USE_CAPTCHA"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
		),
		"USE_MODERATE"  =>  array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_USE_MODERATE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
		),
		// FIELDS
		"DISPLAY_FIELDS" => array(
			"PARENT" => "FIELDS",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_FIELDS"),
			"TYPE" => "LIST",
			"VALUES" => $arAllFields,
			"MULTIPLE" => "Y",
			"SIZE" => "8",
			"DEFAULT" => array("NAME", "EMAIL", "TEXT_PLUS", "TEXT_MINUS", "TEXT_COMMENTS", "VOTE_0", "VOTE_1", "VOTE_2"),
		),
		"REQUIRED_FIELDS" => array(
			"PARENT" => "FIELDS",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_REQUIRED_FIELDS"),
			"TYPE" => "LIST",
			"VALUES" => $arAllFieldsRequired,
			"MULTIPLE" => "Y",
			"SIZE" => "8",
			"DEFAULT" => "",
		),
		"EMAIL_PUBLIC"  =>  array(
			"PARENT" => "FIELDS",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_DISPLAY_EMAIL_PUBLIC"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N", 
		),
		"AJAX_MODE" => array(),
		"DELETE_PARAMETERS"  =>  array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_DELETE_PARAMETERS"),
			"TYPE" => "TEXT",
			"DEFAULT" => "",
			"COLS" => "50",
			"ROWS" => "3",
		),
	),
);

?>