<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["ITEMS"] as &$arItem)
{
	$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat("M Y", MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
	$arItem["~DISPLAY_ACTIVE_FROM"] = ParseDateTime($arItem["ACTIVE_FROM"], "DD.MM.YYYY HH:MI:SS");	
}
?>