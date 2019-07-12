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

$arParams["BLOCK_COUNT"] = intval($arParams["BLOCK_COUNT"]);
if($arParams["BLOCK_COUNT"]<=0)
	$arParams["BLOCK_COUNT"] = 8;

if($this->StartResultCache(FALSE, FALSE))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("CATALOG_MODULE_NOT_INSTALLED"));
		return;
	}
	
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("CATALOG_MODULE_NOT_INSTALLED"));
		return;
	}
	
	$arResult=array();

	$arOrder = Array("SORT"=>"ASC", 'NAME'=>'ASC');
	$arFilter = array('IBLOCK_ID'=>$arParams["IBLOCK"], 'ACTIVE'=>'Y');
	$arSelect = array('IBLOCK_ID', 'ID', 'NAME', 'PROPERTY_ELEMENT', 'PROPERTY_BLOCK_NAME', 'PROPERTY_CATEGORY_NAME', 'PROPERTY_CATEGORY_LINK');
	$arNavStartParams = array('nTopCount'=>$arParams["BLOCK_COUNT"]);
	
	$res = CIBlockElement::GetList($arOrder, $arFilter, FALSE, $arNavStartParams, $arSelect);
	while($ob_res = $res->GetNext()) {
		$dbCat = CIBlockElement::GetList(array(), array('ID'=>$ob_res['PROPERTY_ELEMENT_VALUE'], 'ACTIVE'=>'Y'), FALSE, FALSE, array('IBLOCK_ID', 'ID', 'NAME', 'PREVIEW_PICTURE', 'PREVIEW_TEXT', 'DETAIL_PAGE_URL', 'IBLOCK_SECTION_ID','PROPERTY_DELIVERY_TIME','PROPERTY_OLD_PRICE'));
		if ($arCat = $dbCat->GetNext()) {
			$arPrice = CCatalogProduct::GetOptimalPrice($arCat['ID'], '1', $USER->GetUserGroupArray());
			if ($arPrice['PRICE']['CURRENCY']!='RUB') {
				$priceNoDiscount = CCurrencyRates::ConvertCurrency($arPrice['PRICE']['PRICE'], $arPrice['PRICE']['CURRENCY'], "RUB");
			} else {
				$priceNoDiscount = $arPrice['PRICE']['PRICE'];
			}
			
			if (substr($ob_res['PROPERTY_CATEGORY_LINK_VALUE'], 0, 1)=='/') {
				$ob_res['PROPERTY_CATEGORY_LINK_VALUE'] = SITE_DIR.substr($ob_res['PROPERTY_CATEGORY_LINK_VALUE'], 1, strlen($ob_res['PROPERTY_CATEGORY_LINK_VALUE']));
			}
			
			$tmp = array(
				'NAME'=>$arCat['NAME'],
				'NAME_CAT' => $ob_res['NAME'],
				'ID'=>$arCat['ID'],
				'PIC' => ($arCat['PREVIEW_PICTURE']) ? $arCat['PREVIEW_PICTURE'] : FALSE,
				'TEXT'=>TruncateText($arCat['PREVIEW_TEXT'], 50),
				'PRICE' => (intval($arPrice["DISCOUNT_PRICE"]>0)) ? intval($arPrice["DISCOUNT_PRICE"]) : 0,
				'OLD_PRICE' => ($arCat['PROPERTY_OLD_PRICE_VALUE']) ? intval($arCat['PROPERTY_OLD_PRICE_VALUE']) : intval($priceNoDiscount),
				'DISCOUNT'=>intval($arPrice['DISCOUNT']["VALUE"]),
				'URL' => $arCat['DETAIL_PAGE_URL'],
				'DELIVERY_TIME' => $arCat['PROPERTY_DELIVERY_TIME_VALUE'],
				'CATEGORY_NAME' => $ob_res['PROPERTY_CATEGORY_NAME_VALUE'],
				'CATEGORY_LINK' => $ob_res['PROPERTY_CATEGORY_LINK_VALUE'],
			);
	
			if (!$tmp['PIC']) {
				$dbSec = CIBlockSection::GetList(array(), array('ID'=>$arCat['IBLOCK_SECTION_ID']));
				if ($arSec2 = $dbSec->GetNext()) {
					$dbSecPar = CIBlockSection::GetNavChain(false, $arSec2['ID']);
					$arSecPar = $dbSecPar->GetNext();
					$manufCode = $arSecPar['CODE'];
	
					if ($arSec2['PICTURE']) {
						$tmp['PIC'] = $arSec2['PICTURE'];
					} else {
						$dbSecPar = CIBlockSection::GetNavChain(false, $arSec2['ID']);
						if ($arSecPar = $dbSecPar->GetNext()) {
							$tmp['PIC'] = $arSecPar['PICTURE'];
						}
					}
				}
			}

			$renderImage = CFile::ResizeImageGet($tmp['PIC'], Array("width" => '100', "height" => '100', BX_RESIZE_IMAGE_EXACT));
			$tmp['PIC'] = $renderImage['src'];
			$arResult["SECTIONS"][] = $tmp;
		}
	}
	
	$this->IncludeComponentTemplate();
}
?>
