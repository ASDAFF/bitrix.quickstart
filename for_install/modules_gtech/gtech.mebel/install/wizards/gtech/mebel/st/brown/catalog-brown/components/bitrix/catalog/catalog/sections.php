<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent("g-tech:catalog.section.list", "maincatalog", array(
	"IBLOCK_TYPE" => "catalog",
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"SECTION_ID" => "",
	"SECTION_CODE" => "",
	"SECTION_COUNT" => "",
	"TOP_DEPTH" => "2",
	"SECTION_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => $arParams["CACHE_TIME"],
	"CACHE_GROUPS" => "N",
	"ADD_SECTIONS_CHAIN" => "Y"
	),
	false
);
?>