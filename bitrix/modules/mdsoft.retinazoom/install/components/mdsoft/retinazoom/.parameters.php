<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-" => " "));

$arEffects = array('' => GetMessage("MDSOFT_RETINA_EFFECT_1"), 'transparent' => GetMessage("MDSOFT_RETINA_EFFECT_2"), 'blur' => GetMessage("MDSOFT_RETINA_EFFECT_3"), 'grayscale' => GetMessage("MDSOFT_RETINA_EFFECT_4"));

$arIBlocks = Array();
$db_iblock = CIBlock::GetList(Array("SORT" => "ASC"), Array("SITE_ID" => $_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : "")));
while ($arRes = $db_iblock->Fetch()) {
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(

		"IBLOCK_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_IBLOCK_TYPE_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_IBLOCK_ID_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '={$_REQUEST["ID"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),

		"CACHE_TIME" => Array("DEFAULT" => 3600),

		"INCLUDE_JQUERY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_INCLUDE_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),

		"BOX_SIZE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_BOX_SIZE_NAME"),
			"TYPE" => "INTEGER",
			"DEFAULT" => "100",
		),

		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_BOX_ELEMENT_ID"),
			"TYPE" => "INTEGER",
			"DEFAULT" => "",
		),

		"ZOOM" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_ZOOM_NAME"),
			"TYPE" => "INTEGER",
			"DEFAULT" => "2",

		),
		"EFFECT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MDSOFT_RETINA_EFFECT_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arEffects,
		),
	),
);
?>