<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div style="margin-bottom: 1em;">
<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.menu", 
	"", 
	Array(
		"MAX_ITEMS" => $arParams["MAX_ITEMS"],
		"ENTITY_TYPE" => SONET_ENTITY_GROUP,
		"ENTITY_ID" => $arParams["GROUP_ID"],
		"PAGE_ID" => $arParams["PAGE_ID"],
		"USE_MAIN_MENU" => $arParams["USE_MAIN_MENU"],
		"MAIN_MENU_TYPE" => $arParams["MAIN_MENU_TYPE"],		
		"arResult" => $arResult,
		"GeneralName" => GetMessage("SONET_UM_GENERAL"),
		"UsersName" => GetMessage("SONET_UM_USERS"),
	),
	$component,
	array("HIDE_ICONS" => "Y")
);
?>
</div>