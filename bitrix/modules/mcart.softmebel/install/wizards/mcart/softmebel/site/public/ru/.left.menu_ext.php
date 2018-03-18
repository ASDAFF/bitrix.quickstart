<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

/*
$aMenuLinksExt=$APPLICATION->IncludeComponent("bitrix:menu.sections", "", array(
	"IS_SEF" => "Y",
	"SEF_BASE_URL" => "#SITE_DIR#/catalog/",
	"SECTION_PAGE_URL" => "#SECTION_ID#/",
	"DETAIL_PAGE_URL" => "#SECTION_ID#/#ELEMENT_ID#/",
	"IBLOCK_TYPE" => "catalogs",
	"IBLOCK_ID" => "4",
	"DEPTH_LEVEL" => "100",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "3600"
	),
	false
);*/

$aMenuLinksExt=$APPLICATION->IncludeComponent("mcart:menu.sections", "", array(
	"IS_SEF" => "Y",
	"SEF_BASE_URL" => "#SITE_DIR#/catalog/",
	"SECTION_PAGE_URL" => "#SECTION_ID#/",
	"DETAIL_PAGE_URL" => "#SECTION_ID#/#ELEMENT_ID#/",
	"IBLOCK_TYPE" => "catalogs",
	"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
	"DEPTH_LEVEL" => "100",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "3600"
	),
	false
);

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>