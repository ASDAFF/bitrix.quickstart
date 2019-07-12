<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if($arParams["FILTER_ELEMENT"] > 0){
	foreach($arResult["SECTIONS"] as $k => $arSection){
		$filter = Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "PROPERTY_MANUFACTURER" => $arParams["FILTER_ELEMENT"], "SECTION_ID" => $arSection["ID"], "INCLUDE_SUBSECTIONS" => "Y");
		$res = CIBlockElement::GetList(
			Array(), 
			$filter,
			Array(), 
			false, 
			$arSelect);
		
		$arResult["SECTIONS"][$k]["ELEMENT_CNT"] = $res;
	}
}
?>