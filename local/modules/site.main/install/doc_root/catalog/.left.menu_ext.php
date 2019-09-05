<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
	die();
}

global $APPLICATION;

$menuAdd = $APPLICATION->IncludeComponent(
	"bitrix:menu.sections",
	"",
	array(
		"IS_SEF" => "Y",
		"SEF_BASE_URL" => "",
		"SECTION_PAGE_URL" => "",
		"DETAIL_PAGE_URL" => "",
		"IBLOCK_TYPE" => 'catalog',
		"IBLOCK_ID" => (int) constant('\Site\Main\Iblock\ID_catalog'),
		"DEPTH_LEVEL" => 1,
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => 3600000,
	),
	false,
	array(
		"HIDE_ICONS" => "Y"
	)
);

$aMenuLinks = array_merge($aMenuLinks, $menuAdd);
