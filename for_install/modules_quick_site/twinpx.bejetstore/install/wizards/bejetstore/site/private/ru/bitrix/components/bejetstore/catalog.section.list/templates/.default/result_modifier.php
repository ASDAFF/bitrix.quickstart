<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (0 < $arResult['SECTIONS_COUNT'])
{
	$arSections = array();

	foreach ($arResult["SECTIONS"] as $key => $arSection) {
		if(intval($arSection["IBLOCK_SECTION_ID"])){
			$arSections[ $arSection["IBLOCK_SECTION_ID"] ]["SUBSECTIONS"][] = $arSection;
			$arSections[ $arSection["IBLOCK_SECTION_ID"] ]["SUB_ID"][] = $arSection["ID"];
		}else{
			$arSections[ $arSection["ID"] ] = $arSection;
		}
	}
	
	$arResult["SECTIONS"] = $arSections;
}?>