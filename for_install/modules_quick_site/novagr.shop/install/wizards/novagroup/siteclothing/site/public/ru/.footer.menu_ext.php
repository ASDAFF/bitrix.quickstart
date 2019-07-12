<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

if (!function_exists("GetTreeRecursive")) //Include from main.map component
{
	$aMenuLinksExt=$APPLICATION->IncludeComponent("novagroup:eshop.menu.sections", "", array(
		"IBLOCK_TYPE_ID" => "novagr_standard_products",
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => "36000000"
		),
		false,
		Array('HIDE_ICONS' => 'Y')
	);
	
	$aMenuLinks = array_merge( $aMenuLinksExt, $aMenuLinks);
}
//deb($aMenuLinks);
?>