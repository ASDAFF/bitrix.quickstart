<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('catalog'))
	return;

global $APPLICATION;

$PRICE_ID = $arResult['PRICES'][$arParams['PRICE_CODE'][0]]['ID'];

// SET DIFFERENT
// echo"<pre>";print_r($arResult["SHOW_PROPERTIES"]);echo"</pre>";

if(!empty($arResult["SHOW_PROPERTIES"])) {
	foreach ($arResult["SHOW_PROPERTIES"] as $code => $arProp) {
		$showProp = true;
		if($arResult['DIFFERENT']) {
			$arCompare = array();
			foreach($arResult["ITEMS"] as $key0 => &$arElement) {
				$arPropertyValue = $arElement["DISPLAY_PROPERTIES"][$code]["VALUE"];
				if(is_array($arPropertyValue)) {
					sort($arPropertyValue);
					$arPropertyValue = implode(" / ", $arPropertyValue);
				}
				$arCompare[] = $arPropertyValue;
			}
			unset($arElement);
			$showProp = (count(array_unique($arCompare)) > 1);
		}
		$arResult["SHOW_PROPERTIES"][$code]['SHOW'] = ($showProp ? 'Y' : 'N');
	}
}

if(is_array($arResult['ITEMS']) & count($arResult['ITEMS'])>0) {
	// ADD OFFERS AND PICTURES
	$arItemsID = array();
	$arItems = array();
	$arrKeysByID = array();
	foreach($arResult['ITEMS'] as $key1 => $arItem) {
		$arItemsID[] = $arItem['ID'];
		$arItems[$arItem['ID']] = &$arResult['ITEMS'][$key1];
		$arrKeysByID[$arItem['ID']] = $key1;
		$arResult['ITEMS'][$key1]['DELETE_FROM_COMPARE_URL'] = $APPLICATION->GetCurPageParam('action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID='.$arParams['IBLOCK_ID'].'&ID[]='.$arItem['ID'].'', array('action', 'IBLOCK_ID', 'ID', 'ID[]'));
	}
	$dbRes = CIBlockElement::GetList(array(), array('ID' => $arItemsID), false, false, array('ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'));
	while($arElement = $dbRes->GetNext()){
		$arItems[$arElement['ID']]['PREVIEW_PICTURE'] = (0 < $arElement['PREVIEW_PICTURE'] ? CFile::GetFileArray($arElement['PREVIEW_PICTURE']) : false);
		$arItems[$arElement['ID']]['MIN_PRICE'] = false;
		if(!empty($arItems[$arElement['ID']]['PRICES'])){
			foreach($arItems[$arElement['ID']]['PRICES'] as &$arOnePrice){
				if('Y' == $arOnePrice['MIN_PRICE']){
					$arItems[$arElement['ID']]['MIN_PRICE'] = $arOnePrice;
					break;
				}
			}
			unset($arOnePrice);
		}
	}
	$arOffers = CIBlockPriceTools::GetOffersArray(
		array('IBLOCK_ID'=>$arParams['IBLOCK_ID']),
		$arItemsID,
		array('CATALOG_PRICE_'.$PRICE_ID=>'ASC'),
		array('ID'),
		array($arParams['PROPCODE_SKU_MORE_PHOTO']),
		0,
		$arResult['PRICES'],
		$arParams['PRICE_VAT_INCLUDE'],
		$arResult['CONVERT_CURRENCY']
	);
	if(!empty($arOffers)){
		foreach($arOffers as $arOffer){
			if(array_key_exists($arOffer['LINK_ELEMENT_ID'], $arItems)){
				$arOffer['ADD_URL'] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams['ACTION_VARIABLE'].'=ADD2BASKET&'.$arParams['PRODUCT_ID_VARIABLE'].'='.$arOffer['ID'], array($arParams['PRODUCT_ID_VARIABLE'], $arParams['ACTION_VARIABLE'])));
				$arItems[$arOffer['LINK_ELEMENT_ID']]['OFFERS'][] = $arOffer;
				if('Y' == $arParams['CONVERT_CURRENCY']){
					if(!empty($arOffer['PRICES'])){
						foreach ($arOffer['PRICES'] as &$arOnePrices){
							if (isset($arOnePrices['ORIG_CURRENCY']))
								$arCurrencyList[] = $arOnePrices['ORIG_CURRENCY'];
						}
						if(isset($arOnePrices))
							unset($arOnePrices);
					}
				}
			}
		}
	}

	// GROUPS
	if(CModule::IncludeModule('redsign.grupper')) {
		$arCodeUSED = array();
		$arGroups = array();
		$rsGroups = CRSGGroups::GetList(array('SORT'=>'ASC','ID'=>'ASC'),array());
		while($arGroup = $rsGroups->Fetch()){
			$arGroups[$arGroup['ID']]['GROUP'] = $arGroup;
			$rsBinds = CRSGBinds::GetList(array('ID'=>'ASC'),array('GROUP_ID'=>$arGroup['ID']));
			while($arBind = $rsBinds->Fetch()){
				$arGroups[$arGroup['ID']]['BINDS'][] = $arBind['IBLOCK_PROPERTY_ID'];
			}
		}
		if(is_array($arGroups) && count($arGroups)>0){
			foreach($arGroups as $key => $arGroup){
				$arGroups[$key]['SHOW'] = 'N';
				$arGroups[$key]['ALL_EMPTY'] = 'Y';
				if(is_array($arGroup['BINDS']) && is_array($arResult['SHOW_PROPERTIES']) && count($arResult['SHOW_PROPERTIES'])>0){
					foreach($arResult['SHOW_PROPERTIES'] as $code => $arProp){
						$arResult['SHOW_PROPERTIES'][$code]['ALL_SAME'] = 'Y';
						$arResult['SHOW_PROPERTIES'][$code]['ALL_EMPTY'] = 'Y';
						foreach($arResult['ITEMS'] as $iItemKey => $arItem){
							if(is_array($arItem['DISPLAY_PROPERTIES'][$code]['VALUE'])){
								$value = implode(' / ', $arItem['DISPLAY_PROPERTIES'][$code]['DISPLAY_VALUE']);
							}
							else{
								$value = $arItem['DISPLAY_PROPERTIES'][$code]['DISPLAY_VALUE'];
							}
							if($value!='')
								$arResult['SHOW_PROPERTIES'][$code]['ALL_EMPTY'] = 'N';
							$value = ($value == '' ? '&nbsp;' : $value);
							$arResult['ITEMS'][$iItemKey]['DISPLAY_PROPERTIES'][$code]['DISPLAY_PROPERTIES_FORMATED'] = $value;
							$arResult['ITEMS'][$iItemKey]['DISPLAY_PROPERTIES'][$code]['DISPLAY_PROPERTIES_INTVAL'] = IntVal($value);
							if($iItemKey == 0)
								$last_value = $value;
							if($value != $last_value)
								$arResult['SHOW_PROPERTIES'][$code]['ALL_SAME'] = 'N';
						}
						if(in_array($arProp['ID'],$arGroup['BINDS'])){
							$arGroups[$key]['BINDS_CODE'][] = $code;
							$arCodeUSED[] = $code;
							if($arResult['SHOW_PROPERTIES'][$code]['ALL_EMPTY'] == 'N'){
								$arGroups[$key]['ALL_EMPTY'] = 'N';
							}
							if($arProp['SHOW']=='Y') {
								$arGroups[$key]['SHOW'] = 'Y';
							}
						}
					}
				}
			}
			$arResult['PROPERTY_GROUPS'] = $arGroups;
		}
		if(is_array($arResult['SHOW_PROPERTIES']) && count($arResult['SHOW_PROPERTIES'])>0) {
			$arResult['NOT_GRUPED_PROPS']['SHOW'] = 'N';
			$arResult['NOT_GRUPED_PROPS']['ALL_EMPTY'] = 'Y';
			$arResult['NOT_GRUPED_PROPS']['CODES'] = array();
			foreach($arResult['SHOW_PROPERTIES'] as $arProp){
				if(!in_array($arProp['CODE'], $arCodeUSED)){
					$arResult['SHOW_PROPERTIES'][$arProp['CODE']]['ALL_SAME'] = 'Y';
					$arResult['SHOW_PROPERTIES'][$arProp['CODE']]['ALL_EMPTY'] = 'Y';
					$arResult['NOT_GRUPED_PROPS']['CODES'][] = $arProp['CODE'];
					foreach($arResult['ITEMS'] as $iItemKey => $arItem){
						if(is_array($arItem['DISPLAY_PROPERTIES'][$arProp['CODE']]['VALUE']) && $arItem['DISPLAY_PROPERTIES'][$arProp['CODE']]['USER_TYPE'] != 'HTML'){
							$value = implode(' / ', $arItem['DISPLAY_PROPERTIES'][$arProp['CODE']]['DISPLAY_VALUE']);
						}
						else{
							$value = $arItem['DISPLAY_PROPERTIES'][$arProp['CODE']]['DISPLAY_VALUE'];
						}
						if($value!='')
							$arResult['SHOW_PROPERTIES'][$arProp['CODE']]['ALL_EMPTY'] = 'N';
						$value = ($value == '' ? '&nbsp;' : $value);
						$arResult['ITEMS'][$iItemKey]['DISPLAY_PROPERTIES'][$arProp['CODE']]['DISPLAY_PROPERTIES_FORMATED'] = $value;
						$arResult['ITEMS'][$iItemKey]['DISPLAY_PROPERTIES'][$arProp['CODE']]['DISPLAY_PROPERTIES_INTVAL'] = IntVal($value);
						if($iItemKey == 0)
							$last_value = $value;
						if($value != $last_value)
							$arResult['SHOW_PROPERTIES'][$arProp['CODE']]['ALL_SAME'] = 'N';
					}
					if($arResult['SHOW_PROPERTIES'][$arProp['CODE']]['ALL_EMPTY'] == 'N'){
						$arResult['NOT_GRUPED_PROPS']['ALL_EMPTY'] = 'N';
					}
					if($arProp['SHOW']=='Y') {
						$arResult['NOT_GRUPED_PROPS']['SHOW'] = 'Y';
					}
				}
			}
		}
	}
}

$max_width_size = 210;
$max_height_size = 170;
// get no photo
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>$max_width_size,'MAX_HEIGHT'=>$max_height_size));
// /get no photo