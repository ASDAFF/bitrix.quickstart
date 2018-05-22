<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

if($arParams['VIEW']=='showcase') {
	$arParams['VIEW'] = 'showcase';
} elseif($arParams['VIEW']=='gallery') {
	$arParams['VIEW'] = 'gallery';
} else{ 
	$arParams['VIEW'] = 'table';
}

$params = array();
switch($arParams['VIEW']) {
	case 'showcase': //////////////////////////////////////// showcase ////////////////////////////////////////
		if( is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0 ) {
			foreach($arResult['ITEMS'] as $key1 => $arItem) {
				if(is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0) {
					// Get sorted properties
					$arResult['ITEMS'][$key1]['OFFERS_EXT'] = RSDevFuncOffersExtension::GetSortedProperties($arItem['OFFERS'],$arParams['PROPS_ATTRIBUTES']);
					// /Get sorted properties
				}
				// compare URL fix
				$arResult['ITEMS'][$key1]['COMPARE_URL'] = htmlspecialcharsbx($APPLICATION->GetCurPageParam('action=ADD_TO_COMPARE_LIST&id='.$arItem['ID'], array('action', 'id', 'ajaxpages', 'ajaxpagesid')));
				// /compare URL fix
			}
		}
		// get other data
		$max_width_size = 210;
		$max_height_size = 170;
		$params = array(
			'PROP_MORE_PHOTO' => $arParams['PROP_MORE_PHOTO'],
			'PROP_SKU_MORE_PHOTO' => $arParams['PROP_SKU_MORE_PHOTO'],
			'MAX_WIDTH' => $max_width_size,
			'MAX_HEIGHT' => $max_height_size,
		);
		// /get other data
		break;
	case 'gallery': //////////////////////////////////////// gallery ////////////////////////////////////////
		// get other data
		$max_width_size = 50;
		$max_height_size = 50;
		$params = array(
			'PROP_MORE_PHOTO' => $arParams['PROP_MORE_PHOTO'],
			'PROP_SKU_MORE_PHOTO' => $arParams['PROP_SKU_MORE_PHOTO'],
			'MAX_WIDTH' => $max_width_size,
			'MAX_HEIGHT' => $max_height_size,
		);
		break;
	default: //////////////////////////////////////// table ////////////////////////////////////////
		// ...
}

// get other data
RSDevFunc::GetDataForProductItem($arResult['ITEMS'],$params);
// /get other data

// QB and DA2
if( is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0 ) {
	foreach($arResult['ITEMS'] as $key1 => $arItem) {
		$arResult['ITEMS'][$key1]['HAVE_DA2'] = 'N';
		$arResult['ITEMS'][$key1]['HAVE_QB'] = 'N';
		$arResult['ITEMS'][$key1]['FULL_CATALOG_QUANTITY'] = ( IntVal($arItem['CATALOG_QUANTITY'])>0 ? $arItem['CATALOG_QUANTITY'] : 0 );
		if(is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0) {
			foreach($arItem['OFFERS'] as $arOffer) {
				if( isset($arOffer['DAYSARTICLE2']) ) {
					$arResult['ITEMS'][$key1]['HAVE_DA2'] = 'Y';
				}
				if( isset($arOffer['QUICKBUY']) ) {
					$arResult['ITEMS'][$key1]['HAVE_QB'] = 'Y';
				}
				$arResult['ITEMS'][$key1]['FULL_CATALOG_QUANTITY'] = $arResult['ITEMS'][$key1]['FULL_CATALOG_QUANTITY'] + $arOffer['CATALOG_QUANTITY'];
			}
		}
		if( isset($arItem['DAYSARTICLE2']) ) {
			$arResult['ITEMS'][$key1]['HAVE_DA2'] = 'Y';
		}
		if( isset($arItem['QUICKBUY']) ) {
			$arResult['ITEMS'][$key1]['HAVE_QB'] = 'Y';
		}
	}
}
// /QB and DA2

// get no photo
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>$max_width_size,'MAX_HEIGHT'=>$max_height_size));
// /get no photo

// ADD AJAX URL
$arResult['AJAXPAGE_URL'] = $APPLICATION->GetCurPageParam('',array('ajaxpages', 'ajaxpagesid', 'get', 'AJAX_CALL', 'PAGEN_'.($arResult['NAV_RESULT']->NavNum)));