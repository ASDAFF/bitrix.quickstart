<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;


$aMenuLinksExt=$APPLICATION->IncludeComponent("bestrank:menu.sectionswithelements", "", array(
	"ID" => "",
	"IBLOCK_TYPE" => "services",
	"IBLOCK_ID" => "#IBLOCK_ID_MANUFACTURERS#",
	"SECTION_URL" => "/manufacturers/",
	"DETAIL_URL" => "/manufacturers/#CODE#/",
	"DEPTH_LEVEL" => "2",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000"
	),
	false,
	array(
	"HIDE_ICONS" => "N"
	)
);


$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);

?>