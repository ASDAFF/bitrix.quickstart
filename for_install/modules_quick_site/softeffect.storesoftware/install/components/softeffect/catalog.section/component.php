<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!$this->InitComponentTemplate())
	return;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 86400;

$arParams["CATALOG_SECTION_CODE"] = trim($arParams["CATALOG_SECTION_CODE"]);
if (strlen($arParams["CATALOG_SECTION_CODE"])<=0) {
	ShowError(GetMessage('SE_CATALOGSECTION_NOSECTION'));
	return;
}

function CatalogSectionSort($a, $b) {
	if ($a['SORT'] == $b['SORT']) {
		return 0;
	}
	return ($a['SORT'] < $b['SORT']) ? -1 : 1;
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
	
	$arResult = array('RESULT'=>array(), 'RESULT_ALL'=>array(), 'ACTIONS'=>array(), 'resCatGoods'=>array(), 'resTop'=>array(), 'resActions'=>array(), 'arSec'=>array());

	// kategorii tovarov
	$dbCatGoods = CIBLockElement::GetList(array('NAME'=>'ASC'), array('IBLOCK_ID'=>$arParams["IBLOCK_TYPEG_ID"], 'ACTIVE'=>'Y'), FALSE, FALSE, array('IBLOCK_ID', 'ID', 'NAME'));
	while ($arCatGoods = $dbCatGoods->GetNext(TRUE, FALSE)) {
		$resCatGoods[$arCatGoods['ID']]=$arCatGoods['NAME'];
	}
	
	// poluchaem sekciyu
	$dbSec = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$arParams["IBLOCK_CATALOG_ID"], 'CODE'=>$arParams['CATALOG_SECTION_CODE'], 'DEPTH_LEVEL'=>'1'), FALSE, array('UF_*'));
	if ($arSec = $dbSec->GetNext()) {
		$name_rus=($arSec['UF_NAME_RUS']) ?' ('.$arSec['UF_NAME_RUS'].')': FALSE;
		$h1 = (strlen($arSec['UF_H1'])>0) ? $arSec['UF_H1'] : $arSec['NAME'].$name_rus;
		$title = (strlen($arSec['UF_TITLE'])>0) ? $arSec['UF_TITLE'] : $h1 ;
		$desrc = (strlen($arSec['UF_DESCR'])>0) ? $arSec['NAME'].$arSec['UF_DESCR'] : $h1;
		$keyw = (strlen($arSec['UF_KEYW'])>0) ? $arSec['UF_KEYW'] : $h1;
		
		// poluchaem elementy
		$dbCat = CIBlockElement::GetList(array('SORT'=>'ASC'), array('IBLOCK_ID'=>$arParams["IBLOCK_CATALOG_ID"], 'ACTIVE'=>'Y', 'SECTION_ID'=>$arSec['ID'], 'INCLUDE_SUBSECTIONS'=>'Y'), FALSE, FALSE, array('IBLOCK_ID', 'ID', 'NAME', 'IBLOCK_SECTION_ID','PROPERTY_TYPE_LIC_OWNER', 'PROPERTY_CATEGORY'));
		while ($arCat = $dbCat->GetNext(true, false)) {
			if (!$arCat['PROPERTY_CATEGORY_VALUE']) {
				//continue;
				$arCat['PROPERTY_CATEGORY_VALUE'] = GetMessage('SE_CATALOGSECTION_NOTCAT');
			} elseif ($resCatGoods[$arCat['PROPERTY_CATEGORY_VALUE']]) {
				$arCat['PROPERTY_CATEGORY_VALUE']=$resCatGoods[$arCat['PROPERTY_CATEGORY_VALUE']];
			} else {
				continue;
			}

			// poluchaem sekciyu vtorogo urovnya
			$dbSecL2 = CIBlockSection::GetNavChain(FALSE, $arCat['IBLOCK_SECTION_ID']);
			while ($arSecL2 = $dbSecL2->GetNext()) {
				if ($arSecL2['DEPTH_LEVEL']=='2') {	//echo "<pre>"; print_r($arSecL2); echo "</pre>";
					$url = ($arSecL2['CODE']!='') ? $APPLICATION->GetCurDir().$arSecL2['CODE'].'/' : $APPLICATION->GetCurDir().$arSecL2['ID'].'/';
					$arResult['RESULT_ALL'][$arCat['PROPERTY_CATEGORY_VALUE']][$arSecL2['NAME']]=array('NAME'=>$arSecL2['NAME'], 'PROP_NAME'=>$arCat['PROPERTY_TYPE_LIC_OWNER_VALUE'], 'PROP_ID' => $arCat['PROPERTY_TYPE_LIC_OWNER_ENUM_ID'], 'URL' => $url);
					
					if ($arCat['PROPERTY_TYPE_LIC_OWNER_VALUE']=='') {
						continue;
						$arCat['PROPERTY_TYPE_LIC_OWNER_VALUE'] = GetMessage('SE_CATALOGSECTION_NOTUSE');
					}
					
					if ($arCat['PROPERTY_TYPE_LIC_OWNER_VALUE']!=GetMessage('SE_CATALOGSECTION_NOTCAT')) {
						$url .= '?PROPERTY[TYPE_LIC_OWNER][]='.$arCat['PROPERTY_TYPE_LIC_OWNER_ENUM_ID'];
					}
					
					// dlya ruchnoy sortirovki vkladok po SORT znacheniya spiska
					$arProp = CIBlockPropertyEnum::GetByID($arCat['PROPERTY_TYPE_LIC_OWNER_ENUM_ID']);
					$arResult['RESULT'][$arCat['PROPERTY_TYPE_LIC_OWNER_VALUE']]['SORT'] = $arProp['SORT'];
					$arResult['RESULT'][$arCat['PROPERTY_TYPE_LIC_OWNER_VALUE']]['NAME'] = $arCat['PROPERTY_TYPE_LIC_OWNER_VALUE'];
					
					$arResult['RESULT'][$arCat['PROPERTY_TYPE_LIC_OWNER_VALUE']][$arCat['PROPERTY_CATEGORY_VALUE']][$arSecL2['NAME']]=array('NAME'=>$arSecL2['NAME'], 'PROP_NAME'=>$arCat['PROPERTY_TYPE_LIC_OWNER_VALUE'], 'PROP_ID' => $arCat['PROPERTY_TYPE_LIC_OWNER_ENUM_ID'], 'URL' => $url);
				}
			}
		}
	} else {
		$this->AbortResultCache();
		CHTTP::SetStatus("404 Not Found");
		LocalRedirect('/404.php', FALSE, '404 Not Found');
	}
	
	// topovye produkty
	$resTop=array();
	$dbEl = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>$arParams["IBLOCK_TOPPROD_ID"], 'ACTIVE'=>'Y', 'PROPERTY_SECTION'=>$arSec['ID']), FALSE, FALSE, array('IBLOCK_ID', 'ID','PROPERTY_CML2_ARTICLE'));
	while ($arEl = $dbEl->GetNextElement()) {
		$arProps = $arEl->GetProperties();
		foreach ($arProps['ELEMENT']['VALUE'] as $key => $value) {
			$dbCat = CIBlockElement::GetList(array('SORT'=>'ASC'), array('IBLOCK_ID'=>$arParams["IBLOCK_CATALOG_ID"], 'ACTIVE'=>'Y', 'ID'=>$value), FALSE, FALSE, array('IBLOCK_ID', 'ID', 'NAME', 'IBLOCK_SECTION_ID', 'PREVIEW_PICTURE','DETAIL_PAGE_URL','PROPERTY_TYPE_LIC','PROPERTY_USERS_QUANTITY'));
			while ($arCat = $dbCat->GetNext()) {
				$arPrice = CCatalogProduct::GetOptimalPrice($arCat['ID'], '1');
				$tmp = array(
					'NAME' => $arCat['NAME'],
					'TYPE_LIC'=>$arCat['PROPERTY_TYPE_LIC_VALUE'],
					'USERS_QUANTITY'=>$arCat['PROPERTY_USERS_QUANTITY_VALUE'],
					'PICTURE' => ($arCat['PREVIEW_PICTURE']) ? $arCat['PREVIEW_PICTURE'] : FALSE,
					'URL' => $arCat['DETAIL_PAGE_URL'],
					'PRICE' => $arPrice["DISCOUNT_PRICE"]
				);
				if (!$tmp['PICTURE']) {
					$dbSec = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$arParams["IBLOCK_CATALOG_ID"], 'ID'=>$arCat['IBLOCK_SECTION_ID']));
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

				$renderImage = CFile::ResizeImageGet($tmp["PICTURE"], Array("width" => "50", "height" => "99"));
				$tmp["PICTURE"] = $renderImage['src'];

				$resTop[] = $tmp;
			}
		}
	}
	
	// Akcii po razdelu
	$resActions=array();
	$dbEl = CIBlockElement::GetList(array("SORT"=>"ASC"), array('IBLOCK_ID'=>$arParams["IBLOCK_ACTIONS_ID"], 'ACTIVE'=>'Y',">ACTIVE_DATE" => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT"))),'PROPERTY_SECTION'=>$arSec['ID']), FALSE, FALSE, array('DETAIL_PAGE_URL','NAME','IBLOCK_ID', 'ID','PROPERTY_CML2_ARTICLE','PROPERTY_PICTURE_BREND'));while ($arEl = $dbEl->GetNext()) {
		$renderImage= CFile::ResizeImageGet($arEl['PROPERTY_PICTURE_BREND_VALUE'], Array("width" => 300,"height" => 91),BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
		
		$tmp = array(
			'NAME' => $arEl['NAME'],
			'URL' => $arEl['DETAIL_PAGE_URL'],
			'PICTURE_BREND'=>$renderImage['src'],
		);
		$resActions[] = $tmp;
	}
	
	usort($arResult['RESULT'], 'CatalogSectionSort');
	
	$arResult['ACTIONS'] = $resActions;
	$arResult['resCatGoods'] = $resCatGoods;
	$arResult['resTop'] = $resTop;
	$arResult['resActions'] = $resActions;
	$arResult['arSec'] = $arSec;
	$arResult['h1'] = $h1;
	$arResult['title'] = $title;
	$arResult['desrc'] = $desrc;
	$arResult['keyw'] = $keyw;
	$arResult['arSecL2'] = $arSecL2;
	
	$this->IncludeComponentTemplate();
}

// добавляем в цепочку элемент и ставим тайтл
$APPLICATION->AddChainItem(GetMessage('SE_CATALOGSECTION_VARIANTS').' '.$arResult['arSec']['NAME']);

$APPLICATION->SetTitle($arResult['title']);
if ($arResult['desrc']) {
	$APPLICATION->SetPageProperty("description", $arResult['desrc']);
}

if ($arResult['keyw']) {
	$APPLICATION->SetPageProperty("keywords", $arResult['keyw']);
}
?>
