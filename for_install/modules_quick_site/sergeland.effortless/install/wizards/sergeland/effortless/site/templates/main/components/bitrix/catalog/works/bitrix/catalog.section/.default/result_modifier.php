<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (empty($arResult["ITEMS"]))
	return;

foreach ($arResult["ITEMS"] as $key => &$arItem)
	$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat("d F Y", MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
	//$arItem["~DISPLAY_ACTIVE_FROM"]["DD"] = CIBlockFormatProperties::DateFormat("d", MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
	//$arItem["~DISPLAY_ACTIVE_FROM"]["MM"] = CIBlockFormatProperties::DateFormat("M", MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
?>