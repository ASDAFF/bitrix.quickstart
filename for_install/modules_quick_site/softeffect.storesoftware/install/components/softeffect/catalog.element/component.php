<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!$this->InitComponentTemplate())
	return;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 86400;

$arParams["CATALOG_ELEMENT_CODE"] = trim($arParams["CATALOG_ELEMENT_CODE"]);
if (strlen($arParams["CATALOG_ELEMENT_CODE"])<=0) {
	ShowError(GetMessage('SE_CATALOGELEMENT_NOELEMENT'));
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
	
	$arResult=array();
	$arResult['widthP'] = '140'; // макс. ширина картинки
	$arResult['heightP'] = '250'; // макс. высота картинки
	
	$dbEl = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>$arParams['IBLOCK_CATALOG_ID'], 'CODE'=>trim($_REQUEST['ELEMENT'])), FALSE,  FALSE,  array('IBLOCK_ID', 'ID', 'NAME', 'IBLOCK_SECTION_ID', 'PREVIEW_PICTURE', 'DETAIL_TEXT', 'PREVIEW_TEXT', 'DETAIL_PAGE_URL','PROPERTY_USERS_QUANTITY','PROPERTY_PERIOD','PROPERTY_FORMAT','PROPERTY_RANGE_ORDER','PROPERTY_DELIVERY_TIME','PROPERTY_FREE_DELIVERY','PROPERTY_EDITION','PROPERTY_OLD_PRICE','PROPERTY_SKLAD_MIN','PROPERTY_SKLAD_MAX','PROPERTY_TYPE_LIC'));
	if ($arEl = $dbEl->GetNextElement()) {
		$arFields = $arEl->GetFields();
		$arProp = $arEl->GetProperties();

		$dbSec = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$arParams['IBLOCK_CATALOG_ID'], 'ID'=>$arFields['IBLOCK_SECTION_ID']), FALSE, array('UF_*'));
		
		$platform = CSofteffect::getPlatform($arFields['NAME']);
		if ($platform=='mac') {
			$platform_name = 'Macintosh';
		} elseif ($platform=='windows') {
			$platform_name = 'Windows';
		} else {
			$platform_name = '';
		}
		
		$arPrice = CCatalogProduct::GetOptimalPrice($arFields['ID'], '1', $USER->GetUserGroupArray());
		if ($arPrice['PRICE']['CURRENCY']!='RUB') {
			$priceNoDiscount = CCurrencyRates::ConvertCurrency($arPrice['PRICE']['PRICE'], $arPrice['PRICE']['CURRENCY'], "RUB");
		} else {
			$priceNoDiscount = $arPrice['PRICE']['PRICE'];
		}

		$arResult = array(
			'NAME' => $arFields['NAME'],
			'IBLOCK_ID' => $arFields['IBLOCK_ID'],
			'ID' => $arFields['ID'],
			'USERS_QUANTITY' => $arFields['PROPERTY_USERS_QUANTITY_VALUE'],
			'RANGE_ORDER' => $arFields['PROPERTY_RANGE_ORDER_VALUE'],
			'DELIVERY_TIME' => $arFields['PROPERTY_DELIVERY_TIME_VALUE'],
			'FREE_DELIVERY' => $arFields['PROPERTY_FREE_DELIVERY_VALUE'],
			'PERIOD' => $arFields['PROPERTY_PERIOD_VALUE'],
			'FORMAT' => $arFields['PROPERTY_FORMAT_VALUE'],
			'TYPE_LIC' => $arFields['PROPERTY_TYPE_LIC_VALUE'],
			'PLATFORM' => $platform,
			'PLATFORM_NAME' => $platform_name,
			'ARTICLE' => ($arProp['CML2_ARTICLE']['VALUE']!='') ? $arProp['CML2_ARTICLE']['VALUE'] : '-',
			'PREVIEW_TEXT'=> $arFields['PREVIEW_TEXT'],
			'EDITION' => $arFields['PROPERTY_EDITION_VALUE'],
			'TEXT' => (strlen($arFields['DETAIL_TEXT'])>0) ? $arFields['DETAIL_TEXT'] : FALSE,
			'PRICE' => ($arPrice["DISCOUNT_PRICE"]>0) ? intval($arPrice["DISCOUNT_PRICE"]) : 0,
			'OLD_PRICE' => (intval($arFields['PROPERTY_OLD_PRICE_VALUE'])>0) ? intval($arFields['PROPERTY_OLD_PRICE_VALUE']) : intval($priceNoDiscount),
			'DISCOUNT'=>intval($arPrice['DISCOUNT']["VALUE"]),
			'CHAK' => htmlspecialchars_decode($arProp['CHAK']['VALUE']['TEXT']),
			'CAN' => htmlspecialchars_decode($arProp['CAN']['VALUE']['TEXT']),
			'widthP' => 140,
			'heightP' => 250,
		);
		
		// poluchaem pervuyu i vtoruyu sekcii (proizvoditel')
		$dbSec = CIBlockSection::GetNavChain($arParams['IBLOCK_CATALOG_ID'], $arFields['IBLOCK_SECTION_ID']);
		while ($arSec = $dbSec->GetNext()) {
			if ($arSec['DEPTH_LEVEL']==1) {
				$arResult['MANUF']['NAME'] = $arSec['NAME'];
				$arResult['MANUF']['URL'] = SITE_DIR.'catalog/'.$arSec['CODE'].'/';
				$arResult['MANUF']['TEXT'] = htmlspecialchars_decode($arSec['DESCRIPTION']);
				$manufCode = $arSec['CODE'];
				
				$APPLICATION->AddChainItem($arResult['MANUF']['NAME'], $arResult['MANUF']['URL']);
			}
			if ($arSec['DEPTH_LEVEL']==2) {
				$dbSecTmp = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$arParams['IBLOCK_CATALOG_ID'], 'ID'=>$arSec['ID']), FALSE, array('UF_*'));
				$arSecTmp = $dbSecTmp->GetNext();
				
				$arResult['UF_NAME_RUS']=$arSecTmp['UF_NAME_RUS'];
				$arResult['UF_BOOKS']=htmlspecialchars_decode($arSecTmp['UF_BOOKS']);
				$arResult['UF_KEYW']=$arSecTmp['UF_KEYW'];
				$arResult['MORE']['NAME'] = $arSec['NAME'];
				$arResult['MORE']['URL'] = SITE_DIR.'catalog/'.$manufCode.'/'.$arSec['CODE'].'/';
				$arResult['MORE']['TEXT'] = htmlspecialchars_decode($arSec['DESCRIPTION']);
				$arResult['MORE']['TEXT_MINI'] = htmlspecialchars_decode($arSecTmp['UF_TEXT_MINI']);
				
				if ($arFields['PREVIEW_PICTURE']) { //esli v elemente kartinka est', to
						$renderImage = CFile::ResizeImageGet($arFields['PREVIEW_PICTURE'], Array("width" => $arResult['widthP'], "height" => $arResult['heightP']));
						$arResult['PICTURE'][0]['MINI'] = $renderImage['src'];
						$arResult['PICTURE'][0]['FULL'] = CFile::GetPath($arFields['PREVIEW_PICTURE']);
				} else { //inache smotrim snachala v roditel'skoy sekcii, esli netu to na uroven' vyshe
					$dbSecPar = CIBlockSection::GetNavChain(false, $arSec['ID']);
					if ($arSecPar = $dbSecPar->GetNext()) {
						$renderImage = CFile::ResizeImageGet($arSec['PICTURE'], Array("width" => $arResult['widthP'], "height" => $arResult['heightP']));
						$arResult['PICTURE'][0]['MINI'] = $renderImage['src'];
						$arResult['PICTURE'][0]['FULL'] = CFile::GetPath($arSec['PICTURE']);
					} else { 
						$renderImage = CFile::ResizeImageGet($arSecPar['PICTURE'], Array("width" => $arResult['widthP'], "height" => $arResult['heightP']));
						$arResult['PICTURE'][0]['MINI'] = $renderImage['src'];
						$arResult['PICTURE'][0]['FULL'] = CFile::GetPath($arSecPar['PICTURE']);
					}
				}
	
				// dlya dop. fotok pridetsya sdelat' vyborku s UF polyami
				$dbSecTMP = CIBlockSection::GetList(array(), array('ID'=>$arSec['ID'], 'IBLOCK_ID'=>$arParams['IBLOCK_CATALOG_ID']), FALSE, array('UF_*'));
				$arSecTMP = $dbSecTMP->GetNext();
				
				// sobiraem ostal'nye fotki
				foreach ($arSecTMP['UF_MORE_PHOTO'] as $photo) {
					$renderImage = CFile::ResizeImageGet($photo, Array("width" => $arResult['widthP'], "height" => $arResult['heightP']));
					$arResult['PICTURE'][]=array('MINI'=>$renderImage['src'], 'FULL'=>CFile::GetPath($photo));
				}
	
				if (strlen($arResult['CHAK'])<=0) {
					$arResult['CHAK'] = $arSecTMP['UF_CHAK'];
				}
				
				if (strlen($arResult['CAN'])<=0) {
					$arResult['CAN'] = $arSecTMP['UF_CAN'];
				}
				
				break; // bol'she ne smotrim
			}
		}
		
		// smotrim otzyvy po dannomu produktu
		$arResult['RATING']['LIST']=array();
		$arResult['RATING']['CNT']['NETRAL']=0;
		$arResult['RATING']['CNT']['LIKE']=0;
		$arResult['RATING']['CNT']['DISLIKE']=0;
		
		$ratingDBL = CIBlockElement::GetList(
			array('PROPERTY_DATE'=>'DESC'),
			array(
				'IBLOCK_ID'=>$arParams['IBLOCK_REVIEWS_GOODS_ID'],
				array(
					"LOGIC"=>"OR",
					array('PROPERTY_ELEMENT'=>$arFields['ID']),
					array('PROPERTY_SECTION'=>$arSec['ID']),
				)
			),
			FALSE,
			FALSE,
			array('PROPERTY_PRODUCT','NAME','ID','PREVIEW_TEXT','DETAIL_TEXT','PROPERTY_AUTOR','PROPERTY_USER','PROPERTY_RATING','PROPERTY_DATE','PROPERTY_CITY','PROPERTY_ALL')
		);
					
		
		while ($rating=$ratingDBL->GetNext()) {
			$arResult['RATING']['LIST'][]=$rating;
			if ( is_null($rating['PROPERTY_RATING_ENUM_ID'])) $arResult['RATING']['CNT']['NETRAL']++;
			if ($rating['PROPERTY_RATING_VALUE']==GetMessage('SE_CATALOGELEMENT_LIKE')) $arResult['RATING']['CNT']['LIKE']++;
			if ($rating['PROPERTY_RATING_VALUE']==GetMessage('SE_CATALOGELEMENT_DISLIKE')) $arResult['RATING']['CNT']['DISLIKE']++;
		}
	
		$arResult['RATING_OBJ'] = new CDBResult;
		$arResult['RATING_OBJ']->InitFromArray($arResult['RATING']['LIST']);
		$arResult['RATING_OBJ']->NavStart(6);
		
		// sravnenie redakciy
		$compareEl = CIBlockElement::GetList(array('SORT'=>'ASC'),array( "ACTIVE"=>"Y","IBLOCK_ID"=>$arParams['IBLOCK_COMPARE_ID'],"PROPERTY_SECTION" => $arSec['ID'] ) , FALSE, FALSE, array('DETAIL_TEXT','NAME','PROPERTY_TAB_NAME','PROPERTY_SECTION'));
		while ($arCompare = $compareEl->GetNext()) {
			$tmp = array(
				'NAME' => $arCompare['NAME'],
				'DETAIL_TEXT' => htmlspecialchars_decode($arCompare['DETAIL_TEXT']),
				'PROPERTY_TAB_NAME' => $arCompare['PROPERTY_TAB_NAME_VALUE'],
			);
			$arResult['resCompare'] = $tmp;
		}
		
		// varianty pokupki, masivy v ramkah redakciy
		$sameEl = CIBlockElement::GetList(array('RAND'=>'ASC'), array('IBLOCK_ID'=>$arParams['IBLOCK_CATALOG_ID'], 'ACTIVE'=>'Y', 'SECTION_ID'=>$arSec['ID'],'PROPERTY_EDITION_VALUE'=>$arResult['EDITION']), FALSE, array('nTopCount'=>10), array());
		while ($arElProp = $sameEl->GetNextElement()) {
			$arItem = $arElProp->GetFields();
			$arItem["PROPERTIES"] = $arElProp->GetProperties();
			
			$arPrice = CCatalogProduct::GetOptimalPrice($arItem['ID'], '1');
			$renderImage = CFile::ResizeImageGet($pic, Array("width" => '50', "height" => '150'));
			
			foreach ($arItem["PROPERTIES"]['TYPE_LIC']['VALUE'] as $key => $value) {
				$arResult['arResultType'][$value][]=array(
					'ID' => $arItem['ID'],
					'NAME' => $arItem['NAME'],
					'USERS_QUANTITY' => $arItem["PROPERTIES"]['USERS_QUANTITY']['VALUE'],
					'PERIOD' => $arItem["PROPERTIES"]['PERIOD']['VALUE'],
					'FORMAT' => $arItem["PROPERTIES"]['FORMAT']['VALUE'],
					'PICTURE' => $renderImage['src'],
					'URL' => $arItem['DETAIL_PAGE_URL'],
					'ARTICLE' => $arItem["PROPERTIES"]['CML2_ARTICLE']['VALUE'],
					'PRICE' => SaleFormatCurrency(($arPrice["DISCOUNT_PRICE"]>0) ? intval($arPrice["DISCOUNT_PRICE"]) : 0, "RUB"),
					'PLATFORM' => CSofteffect::getPlatform($arItem['NAME']),
					'UF_NAME_RUS' => $name_rus,
					'NAME_SEC' => $arSecL2['NAME'],
					'TYPE_LIC' => $arItem["PROPERTIES"]['TYPE_LIC']['VALUE']
				);
			}
		}
	
		// SEO
		if ($arResult['UF_NAME_RUS']) {
			$arResult['H1'] = (strlen($arProp['SEO_H1']['VALUE'])>0) ? $arProp['SEO_H1']['VALUE'] : $arFields['NAME']." (".$arResult['UF_NAME_RUS'].")";
		} else{
			$arResult['H1'] = (strlen($arProp['SEO_H1']['VALUE'])>0) ? $arProp['SEO_H1']['VALUE'] : $arFields['NAME'];
		}
	
		$arResult['TITLE'] = (strlen($arProp['SEO_TITLE']['VALUE'])>0) ? $arProp['SEO_TITLE']['VALUE'].$arFields['NAME'] : $arFields['NAME'];
		$arResult['DESCR'] = (strlen($arProp['SEO_DESCR']['VALUE'])>0) ? $arProp['SEO_DESCR']['VALUE'] : $arResult['H1'];
		
		if ($arResult['UF_KEYW']) {
			$arResult['KEYW'] = $SEO_KEYW.$arResult['H1'].','.$arResult['UF_KEYW'];
		} else {
			$arResult['KEYW'] = (strlen($arProp['SEO_KEYW']['VALUE'])>0) ? $arProp['SEO_KEYW']['VALUE'] : $arFields['NAME'];
		}
		
		// pohojie produkty
		$dbRelated = CIBlockElement::GetList(array('rand'=>'asc'), array('IBLOCK_ID'=>$arParams['IBLOCK_CATALOG_ID'], 'SECTION_ID'=>$arSec['ID'], 'INCLUDE_SUBSECTIONS'=>'Y', '!ID'=>array($arFields['ID'])), FALSE, array('nTopCount'=>'4'), array('IBLOCK_ID', 'DETAIL_PAGE_URL', 'ID', 'NAME', 'PREVIEW_PICTURE', 'IBLOCK_SECTION_ID'));
		while ($arRelated = $dbRelated->GetNext()) {
			$arPrice= CCatalogProduct::GetOptimalPrice($arRelated['ID'], '1');
			
			$tmp = array(
				'NAME' => $arRelated['NAME'],
				'PICTURE' => ($arRelated['PREVIEW_PICTURE']) ? CFile::GetPath($arRelated['PREVIEW_PICTURE']) : FALSE,
				'URL' => $arRelated['DETAIL_PAGE_URL'],
				'PRICE' => (intval($arPrice["DISCOUNT_PRICE"])>0) ? intval($arPrice["DISCOUNT_PRICE"]) : 0,
			);
				
			if (!$tmp['PICTURE']) {
				$dbSec = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$arParams['IBLOCK_CATALOG_ID'], 'ID'=>$arRelated['IBLOCK_SECTION_ID']));
				if ($arSec2 = $dbSec->GetNext()) {
					if ($arSec2['PICTURE']) {
						$tmp['PICTURE'] = CFile::GetPath($arSec2['PICTURE']);
					} else {
						$dbSecPar = CIBlockSection::GetNavChain(false, $arSec2['ID']);
						if ($arSecPar = $dbSecPar->GetNext()) {
							$tmp['PICTURE'] = CFile::GetPath($arSecPar['PICTURE']);
						}
					}
				}
			}
			$arResult['RELATED'][] = $tmp;
		}
	} else {
		$this->AbortResultCache();
		CHTTP::SetStatus("404 Not Found");
		LocalRedirect(SITE_DIR.'404.php', FALSE, '404 Not Found');
	}

	$this->IncludeComponentTemplate();
}

// vy nedavno smotreli
if ($_REQUEST['clear_viewed_products']!='Y') {
	$arFieldsSale = array(
		"PRODUCT_ID" => $arResult["ID"],
		"MODULE" => "catalog",
		"LID" => SITE_ID,
		"IBLOCK_ID" => $arResult["IBLOCK_ID"]
	);
	CSaleViewedProduct::Add($arFieldsSale);
}
			
$APPLICATION->SetTitle($arResult['TITLE']);
	
if ($arResult['KEYW']) {
	$APPLICATION->SetPageProperty("description", $arResult['KEYW']);
}
if ($arResult['DESCR']) {
	$APPLICATION->SetPageProperty("keywords", $arResult['DESCR']);
}

$APPLICATION->AddChainItem($arResult['MORE']['NAME'], $arResult['MORE']['URL']);
$APPLICATION->AddChainItem($arResult['NAME']);

?>