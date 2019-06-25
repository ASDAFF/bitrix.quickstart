<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;
$aMenuLinksExt = $APPLICATION->IncludeComponent(
	"bitrix:menu.sections",
	"",
	array(
		"IS_SEF" => "N",
		"SEF_BASE_URL" => "#CATALOG_PATH#",
		"SECTION_PAGE_URL" => "?SECTION_ID=#SECTION_ID#/",
		"DETAIL_PAGE_URL" => "?SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
		"IBLOCK_TYPE" => "#CATALOG_IBLOCK_TYPE#",
		"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
		"DEPTH_LEVEL" => "2",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
	false
);
$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);?>
