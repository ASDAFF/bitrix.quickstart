<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;
$aMenuLinksExt = $APPLICATION->IncludeComponent(
	"bitrix:menu.sections",
	"",
	Array(
		"ID" => $_REQUEST["ELEMENT_ID"],
		"IBLOCK_TYPE" => "clinic",
		"IBLOCK_ID" => "#DOCTORS_IBLOCK_ID#",
		"SECTION_URL" => SITE_DIR."/doctors/#CODE#/",
		"CACHE_TIME" => "3600"
	)
);
$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);			
?>