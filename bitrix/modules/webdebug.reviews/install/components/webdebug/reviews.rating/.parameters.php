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
$arVotes = array(
	"0" => COption::GetOptionString("webdebug.reviews", "vote_name_0"),
	"1" => COption::GetOptionString("webdebug.reviews", "vote_name_1"),
	"2" => COption::GetOptionString("webdebug.reviews", "vote_name_2"),
	"3" => COption::GetOptionString("webdebug.reviews", "vote_name_3"),
	"4" => COption::GetOptionString("webdebug.reviews", "vote_name_4"),
	"5" => COption::GetOptionString("webdebug.reviews", "vote_name_5"),
	"6" => COption::GetOptionString("webdebug.reviews", "vote_name_6"),
	"7" => COption::GetOptionString("webdebug.reviews", "vote_name_7"),
	"8" => COption::GetOptionString("webdebug.reviews", "vote_name_8"),
	"9" => COption::GetOptionString("webdebug.reviews", "vote_name_9"),
);

$arComponentParameters = array(
	"GROUPS" => array(
		"SOURCE" => array(
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_SOURCE"),
			"SORT" => "90",
		),
	),
	"PARAMETERS" => array(
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
		// FIELDS
		"VOTE" => array(
			"PARENT" => "SOURCE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_VOTE"),
			"TYPE" => "LIST",
			"VALUES" => $arVotes,
			"MULTIPLE" => "Y",
			"SIZE" => "8",
			"DEFAULT" => "VOTE_1",
		),
		"MAX_RATING"  =>  array(
			"PARENT" => "SOURCE",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_MAX_RATING"),
			"TYPE" => "TEXT",
			"DEFAULT" => "5",
		),
	),
);

?>