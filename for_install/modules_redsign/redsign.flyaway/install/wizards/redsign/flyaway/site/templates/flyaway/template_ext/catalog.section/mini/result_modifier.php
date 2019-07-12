<?php 
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$max_width_size = 300;
$max_height_size = 300;
$params = array(
	'PROP_MORE_PHOTO' => $arParams['RSMONOPOLY_PROP_MORE_PHOTO'],
	'PROP_SKU_MORE_PHOTO' => $arParams['RSMONOPOLY_PROP_SKU_MORE_PHOTO'],
	'MAX_WIDTH' => $max_width_size,
	'MAX_HEIGHT' => $max_height_size,
);

RSDevFunc::GetDataForProductItem($arResult['ITEMS'],$params);

if( is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0 ) {
	foreach($arResult['ITEMS'] as $key1 => $arItem) {
		// QB and DA2
		$arResult['ITEMS'][$key1]['HAVE_DA2'] = 'N';
		$arResult['ITEMS'][$key1]['HAVE_QB'] = 'N';
		
		if(is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0) {
			foreach($arItem['OFFERS'] as $arOffer) {
				if( isset($arOffer['DAYSARTICLE2']) ) {
					$arResult['ITEMS'][$key1]['HAVE_DA2'] = 'Y';
				}
				if( isset($arOffer['QUICKBUY']) ) {
					$arResult['ITEMS'][$key1]['HAVE_QB'] = 'Y';
				}
			}
		}
		if( isset($arItem['DAYSARTICLE2']) ) {
			$arResult['ITEMS'][$key1]['HAVE_DA2'] = 'Y';
		}
		if( isset($arItem['QUICKBUY']) ) {
			$arResult['ITEMS'][$key1]['HAVE_QB'] = 'Y';
		}
		// /QB and DA2

	}
}

$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>$max_width_size,'MAX_HEIGHT'=>$max_height_size));