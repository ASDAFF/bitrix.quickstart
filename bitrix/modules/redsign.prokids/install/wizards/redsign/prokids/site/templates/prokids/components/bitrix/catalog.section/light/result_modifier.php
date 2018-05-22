<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

// get other data
$params = array(
	'PROP_MORE_PHOTO' => $arParams['PROP_MORE_PHOTO'],
	'PROP_SKU_MORE_PHOTO' => $arParams['PROP_SKU_MORE_PHOTO'],
	'MAX_WIDTH' => 220,
	'MAX_HEIGHT' => 220,
);
RSDevFunc::GetDataForProductItem($arResult['ITEMS'],$params);
// /get other data

// ADD AJAX URL
$arResult['AJAXPAGE_URL'] = $APPLICATION->GetCurPageParam('',array('ajaxpages', 'ajaxpagesid', 'get', 'AJAX_CALL', 'PAGEN_'.($arResult['NAV_RESULT']->NavNum)));