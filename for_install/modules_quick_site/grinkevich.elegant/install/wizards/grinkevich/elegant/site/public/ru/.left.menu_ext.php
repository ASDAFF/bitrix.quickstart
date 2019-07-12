<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

if (!function_exists("GetTreeRecursive")){

	$aMenuLinksExt = $APPLICATION->IncludeComponent("bitrix:menu.sections","",Array(
			"IS_SEF" => "Y",
			"SEF_BASE_URL" => "#SITE_DIR#catalog/",
			"SECTION_PAGE_URL" => "#SECTION_CODE#/",
			"DETAIL_PAGE_URL" => "#SECTION_CODE#/#ELEMENT_ID#",
			"IBLOCK_TYPE" => "catalog",
			"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
			"DEPTH_LEVEL" => "2",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600"
		)
	);


	$aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);
}
?>