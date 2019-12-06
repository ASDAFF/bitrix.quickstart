<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(empty($arResult["ITEMS"]))
	return;

$arSections = array();
$rsSections = CIBlockSection::GetList(array(), array("ACTIVE" => "Y","GLOBAL_ACTIVE" => "Y","IBLOCK_ID" => $arParams["IBLOCK_ID"],"CNT_ACTIVE" => "Y"), false,array("IBLOCK_ID", "IBLOCK_SECTION_ID", "ID", "NAME", "LEFT_MARGIN", "RIGHT_MARGIN", "DEPTH_LEVEL" ));
while($ar_result = $rsSections->GetNext())
	$arSections[$ar_result["ID"]] = $ar_result;

foreach ($arResult["ITEMS"] as $key => &$arItem)
{
	if(!empty($arItem["PROPERTIES"]["ACTIVE_FROM"]["VALUE"]))
		$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat("d F Y", MakeTimeStamp($arItem["PROPERTIES"]["ACTIVE_FROM"]["VALUE"], CSite::GetDateFormat()));
	
	if(!empty($arItem["PROPERTIES"]["ACTIVE_TO"]["VALUE"]))
		$arItem["DISPLAY_ACTIVE_TO"] = CIBlockFormatProperties::DateFormat("d F Y", MakeTimeStamp($arItem["PROPERTIES"]["ACTIVE_TO"]["VALUE"], CSite::GetDateFormat()));
	
	//$arItem["~DISPLAY_ACTIVE_FROM"]["DD"] = CIBlockFormatProperties::DateFormat("d", MakeTimeStamp($arItem["PROPERTIES"]["ACTIVE_FROM"]["VALUE"], CSite::GetDateFormat()));
	//$arItem["~DISPLAY_ACTIVE_FROM"]["MM"] = CIBlockFormatProperties::DateFormat("M", MakeTimeStamp($arItem["PROPERTIES"]["ACTIVE_FROM"]["VALUE"], CSite::GetDateFormat()));

	$arItem["IBLOCK_SECTION"] = $arSections[$arItem["IBLOCK_SECTION_ID"]];	
}
?>