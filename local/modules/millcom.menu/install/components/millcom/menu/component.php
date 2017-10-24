<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if (!CModule::IncludeModule("iblock")) return false;
if (!CModule::IncludeModule("millcom.menu")) return false;

 
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;
	
$arParams["DEPTH_LEVEL"] = intval($arParams["DEPTH_LEVEL"]);
if($arParams["DEPTH_LEVEL"]<=0)
	$arParams["DEPTH_LEVEL"]=1;

if ($this->StartResultCache()) {
	$arOrder = Array("SORT" => "ASC");
	$arSelect = Array("ID", "NAME", "IBLOCK_ID", "SECTION_PAGE_URL", "SORT", "IBLOCK_SECTION_ID");
	$arFilter = Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "GLOBAL_ACTIVE" => "Y", "IBLOCK_ACTIVE" => "Y");
	$rsSections = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);
	$arResult['ITEMS'] = array();
	while ($arSection = $rsSections->GetNext()) {
		$IBLOCK_SECTION_ID = $arSection['IBLOCK_SECTION_ID'] ? $arSection['IBLOCK_SECTION_ID'] : 0;
		$arResult['ITEMS'][$IBLOCK_SECTION_ID][] = $arSection;
	}

	$arOrder = Array("SORT" => "ASC");
	$arSelect = Array("ID", "NAME", "IBLOCK_ID", "DETAIL_PAGE_URL", "SORT", "SECTION_ID");
	$arFilter = Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y");
	$rsElements = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
	while ($arElement = $rsElements->GetNext()) {
		$IBLOCK_SECTION_ID = $arElement['IBLOCK_SECTION_ID'] ? $arElement['IBLOCK_SECTION_ID'] : 0;
		$arResult['ITEMS'][$IBLOCK_SECTION_ID][] = $arElement;
	}
  $this->EndResultCache();
}

if ($arParams["SORT"] == 'Y') {
	foreach ($arResult['ITEMS'] as &$arMenuItem)
		usort($arMenuItem, 'MillcomMenu::sort');
}

MillcomMenu::display($arResult['ITEMS'], 0, 1, $aMenuLinks, $arParams);


return $aMenuLinks;
?>