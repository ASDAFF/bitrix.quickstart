<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$pageId = "";
include("util_group_menu.php");
?>

<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.group_delete", 
	"", 
	Array(
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
		"SET_NAV_CHAIN" => $arResult["SET_NAV_CHAIN"],
		"SET_TITLE" => $arResult["SET_TITLE"],
		"GROUP_ID" => $arResult["VARIABLES"]["group_id"],
	),
	$component 
);
?>
