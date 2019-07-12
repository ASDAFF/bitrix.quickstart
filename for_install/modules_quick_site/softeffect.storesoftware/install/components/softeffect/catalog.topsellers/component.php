<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!$this->InitComponentTemplate())
	return;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
 	$arParams["IBLOCK_TYPE"] = "sw_catalog";

if($arParams["IBLOCK_TYPE"]=="-")
	$arParams["IBLOCK_TYPE"] = "";

if(!is_array($arParams["IBLOCK"]))
	$arParams["IBLOCK"] = array($arParams["IBLOCK"]);

foreach($arParams["IBLOCK"] as $k=>$v)
	if(!$v)
		unset($arParams["IBLOCK"][$k]);

$arParams["MAX_COUNT"] = intval($arParams["MAX_COUNT"]);
if($arParams["MAX_COUNT"]<=0)
	$arParams["MAX_COUNT"] = 5;

if($this->StartResultCache(FALSE, FALSE)) {
	if(!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	
	if(!CModule::IncludeModule("catalog")) {
		$this->AbortResultCache();
		ShowError(GetMessage("CATALOG_MODULE_NOT_INSTALLED"));
		return;
	}
	
	$arResult=array('GOODS'=>array());
	
	$dbEl = CIBlockElement::GetList(array('rand'=>'asc'), array('IBLOCK_ID'=>$arParams["IBLOCK"], 'ACTIVE'=>'Y', '!PROPERTY_ELEMENT'=>FALSE), FALSE, array('nTopCount'=>'5'), array('IBLOCK_ID', 'ID', 'PROPERTY_ELEMENT'));
	while ($arEl = $dbEl->GetNext()) {
		$dbCat = CIBlockElement::GetList(array(), array('ID'=>$arEl['PROPERTY_ELEMENT_VALUE'], 'ACTIVE'=>'Y'), FALSE, FALSE, array('IBLOCK_ID', 'ID', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PAGE_URL', 'IBLOCK_SECTION_ID'));
		if ($arCat = $dbCat->GetNext()) {
			$arPrice = CCatalogProduct::GetOptimalPrice($arCat['ID'], '1');

			$tmp = array(
				'NAME'=>$arCat['NAME'], 
				'URL'=>$arCat['DETAIL_PAGE_URL'], 
				'PRICE' => SaleFormatCurrency(($arPrice['DISCOUNT_PRICE']>0) ? intval($arPrice['DISCOUNT_PRICE']) : 0, "RUB"), 
				'PICTURE' => ($arCat['PREVIEW_PICTURE']) ? $arCat['PREVIEW_PICTURE'] : FALSE,
			);
	
			if (!$tmp['PICTURE']) {
				$dbSec = CIBlockSection::GetList(array(), array('ID'=>$arCat['IBLOCK_SECTION_ID']));
				if ($arSec2 = $dbSec->GetNext()) {
					if ($arSec2['PICTURE']) {
						$tmp['PICTURE'] = $arSec2['PICTURE'];
					} else {
						$dbSecPar = CIBlockSection::GetNavChain(false, $arSec2['ID']);
						if ($arSecPar = $dbSecPar->GetNext()) {
							$tmp['PICTURE'] = $arSecPar['PICTURE'];
						}
					}
				}
			}
	
			$renderImage = CFile::ResizeImageGet($tmp['PICTURE'], Array("width" => '50', "height" => '300'));
			$tmp['PICTURE'] = $renderImage['src'];
			
			$arResult['GOODS'][] = $tmp;
		}
	}

	$this->IncludeComponentTemplate();
}
?>
