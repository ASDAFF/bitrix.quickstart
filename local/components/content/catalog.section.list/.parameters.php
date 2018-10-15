<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
global $USER_FIELD_MANAGER;

if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty_UF = array();
$arUserFields = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION");
foreach($arUserFields as $FIELD_NAME=>$arUserField)
	$arProperty_UF[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME;

$depthTo = array("below" => GetMessage("CP_BCSL_DEPTH_BELOW"), "sub" => GetMessage("CP_BCSL_DEPTH_SUB"), "equal" => GetMessage("CP_BCSL_DEPTH_EQUAL"));

$arSort = array("left_margin"=>"asc",);
$rsSectionsAll = CIBlockSection::GetList($arSort, array("IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"], "ACTIVE" => "Y"), false, array("IBLOCK_ID","ID","NAME"));
while($arAllSection = $rsSectionsAll->GetNext())
{
	$sectionList['section_'.$arAllSection["ID"]] = $arAllSection["NAME"].'['.$arAllSection["ID"].']';
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"SECTION_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["SECTION_ID"]}',
		),
		"SECTION_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"SECTION_LIST" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_SECTION_LIST"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $sectionList,
		),
		"SECTION_URL" => CIBlockParameters::GetPathTemplateParam(
			"SECTION",
			"SECTION_URL",
			GetMessage("CP_BCSL_SECTION_URL"),
			"",
			"URL_TEMPLATES"
		),
		"COUNT_ELEMENTS" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCSL_COUNT_ELEMENTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => 'Y',
		),
		"DEPTH_TO" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCSL_TOP_DEPTH_TO"),
			"TYPE" => "LIST",
			"VALUES" => $depthTo,
		),
		"TOP_DEPTH" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCSL_TOP_DEPTH"),
			"TYPE" => "STRING",
			"DEFAULT" => '2',
		),
		"SECTION_FIELDS" => CIBlockParameters::GetSectionFieldCode(
			GetMessage("CP_BCSL_SECTION_FIELDS"),
			"DATA_SOURCE",
			array()
		),
		"SECTION_USER_FIELDS" =>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCSL_SECTION_USER_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProperty_UF,
		),
		"SECTION_USER_FIELDS_FILTER" =>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCSL_SECTION_USER_FIELDS_FILTER"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProperty_UF,
		),
		"ADD_SECTIONS_CHAIN" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_ADD_SECTIONS_CHAIN"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PICTURE_WIDTH" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_PICTURE_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => '100',
		),
		"PICTURE_HEIGHT" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_PICTURE_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => '100',
		),
		"INCLUDE_JQUERY" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_INCLUDE_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"INCLUDE_BXSLIDER" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_INCLUDE_BXSLIDER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"INCLUDE_CHOOSEN" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_INCLUDE_CHOOSEN"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);

?>