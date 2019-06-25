<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true)?>
<?global $options;?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	$options['CATALOG_VIEW']['ACTIVE_VALUE'],
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "SECTION_ID" => false,
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
		"TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
        "ROOT_SECTIONS" => "Y",
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
        "GRID_CATALOG_SECTIONS_COUNT" => $arParams["GRID_CATALOG_ROOT_SECTIONS_COUNT"],
		"ADAPTABLE" => $arParams['ADAPTABLE']
	),
	$component
);
?>