<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

$aMenuLinksExt = $APPLICATION->IncludeComponent(
	"bitrix:menu.sections",
	"",
	array(
		"IS_SEF" => "Y",
		"SEF_BASE_URL" => "",
		"SECTION_PAGE_URL" => "",
		"DETAIL_PAGE_URL" => "",
		"IBLOCK_TYPE" => "",
		"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
		"DEPTH_LEVEL" => "3",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
	false,
	Array('HIDE_ICONS' => "Y")
);

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>