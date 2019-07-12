<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!$this->InitComponentTemplate())
	return;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 86400;

$arParams["CATALOG_CATEGORY_CODE"] = trim($arParams["CATALOG_CATEGORY_CODE"]);
if (strlen($arParams["CATALOG_CATEGORY_CODE"])<=0) {
	ShowError(GetMessage('SE_CATALOGCATEGORY_NOSECTION'));
	return;
}

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
	
	$arResult["TABS"]=array();
	
	$dbCatGoods = CIBLockElement::GetList(array('sort'=>'ASC'), array('IBLOCK_ID'=>$arParams["IBLOCK_TYPEG_ID"], 'ACTIVE'=>'Y'), FALSE, FALSE, array('IBLOCK_ID', 'ID', 'NAME', 'CODE'));
	while ($arCatGoods = $dbCatGoods->GetNextElement(TRUE, FALSE)) {
		$arF = $arCatGoods->GetFields();
		$arP = $arCatGoods->GetProperties();
		
		if ($arF['CODE']==$_REQUEST['CATEGORY']) {
			$idType = $arF['ID'];
			$arResult["H1"] = (strlen($arP['SEO_H1']['VALUE'])>0) ? $arP['SEO_H1']['VALUE'] : $arF['NAME'];
			$arResult["TITLE"] = (strlen($arP['SEO_TITLE']['VALUE'])>0) ? $arP['SEO_TITLE']['VALUE'] : $arF['NAME'];
			$arResult["DESCR"] = (strlen($arP['SEO_DESCR']['VALUE'])>0) ? $arP['SEO_DESCR']['VALUE'] : FALSE;
			$arResult["KEYW"] = (strlen($arP['SEO_KEYW']['VALUE'])>0) ? $arP['SEO_KEYW']['VALUE'] : FALSE;
		}
		$arResult["resCatGoods"][$arF['ID']]=$arF['NAME'];
	}

	$dbCat = CIBlockElement::GetList(array('SORT'=>'ASC'), array('IBLOCK_ID'=>$arParams["IBLOCK_CATALOG_ID"], 'ACTIVE'=>'Y', 'PROPERTY_CATEGORY'=>$idType, 'INCLUDE_SUBSECTIONS'=>'Y'), FALSE, FALSE, array('IBLOCK_ID', 'ID', 'NAME', 'IBLOCK_SECTION_ID','PROPERTY_TYPE_LIC_OWNER', 'PROPERTY_CATEGORY'));
	while ($arCat = $dbCat->GetNext(true, false)) {
		if (!$arCat['PROPERTY_CATEGORY_VALUE']) {
			//continue;
			$arCat['PROPERTY_CATEGORY_VALUE'] = GetMessage('SE_CATALOGCATEGORY_NOTCAT');
		} elseif ($arResult["resCatGoods"][$arCat['PROPERTY_CATEGORY_VALUE']]) {
			$arCat['PROPERTY_CATEGORY_VALUE']=$arResult["resCatGoods"][$arCat['PROPERTY_CATEGORY_VALUE']];
		} else {
			continue;
		}
		
		if ($arCat['PROPERTY_TYPE_LIC_OWNER_VALUE']=='') {
			$arCat['PROPERTY_TYPE_LIC_OWNER_VALUE'] = GetMessage('SE_CATALOGCATEGORY_NOTUSE');
		}
		
		$url='/catalog/';
		$dbSecL2 = CIBlockSection::GetNavChain($arParams["IBLOCK_CATALOG_ID"], $arCat['IBLOCK_SECTION_ID']);
		while ($arSecL2 = $dbSecL2->GetNext()) {
			if ($arSecL2['DEPTH_LEVEL']=='1') {	
				$arSecL2D1 = $arSecL2['NAME'];
				$url .= $arSecL2['CODE'].'/';
			} elseif ($arSecL2['DEPTH_LEVEL']=='2') {
				$url .= $arSecL2['CODE'].'/';
				$urlAll = $url;
				
				if ($arCat['PROPERTY_TYPE_LIC_OWNER_VALUE']!=GetMessage('SE_CATALOGCATEGORY_NOTCAT')) {
					$url .= '?PROPERTY[TYPE_LIC_OWNER][]='.$arCat['PROPERTY_TYPE_LIC_OWNER_ENUM_ID'];
				}
				$arResult["TABS"][$arCat['PROPERTY_TYPE_LIC_OWNER_VALUE']][$arSecL2D1][$arSecL2['ID']]=array('NAME'=>$arSecL2['NAME'], 'URL' => $url);
				$arResult["TABS"][GetMessage('SE_CATALOGCATEGORY_ALL')][$arSecL2D1][$arSecL2['ID']]=array('NAME'=>$arSecL2['NAME'], 'URL' => $urlAll);
			}
		}
	}
	$all = $arResult["TABS"][GetMessage('SE_CATALOGCATEGORY_ALL')]; unset($arResult["TABS"][GetMessage('SE_CATALOGCATEGORY_ALL')]); $arResult["TABS"][GetMessage('SE_CATALOGCATEGORY_ALL')] = $all;
	
	$this->IncludeComponentTemplate();
}

$APPLICATION->AddChainItem(GetMessage('SE_CATALOGCATEGORY_CATEGORY').' &laquo;'.$arResult["TITLE"].'&raquo;');

$APPLICATION->SetTitle($arResult["TITLE"]);
if ($arResult["DESCR"]) {
	$APPLICATION->SetPageProperty("description", $arResult["DESCR"]);
}

if ($arResult["KEYW"]) {
	$APPLICATION->SetPageProperty("keywords", $arResult["KEYW"]);
}

?>
