<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader;

if (!Loader::includeModule('redsign.devfunc')) {
	return;
}

$arResult['JSON_EXT'] = RSDevFuncOffersExtension::GetJSONElement(
	$arResult,
	$arParams['OFFER_TREE_PROPS'],
	$arParams['PRICE_CODE'],
	array(
        'SKU_MORE_PHOTO_CODE'=>$arParams['RSFLYAWAY_PROP_SKU_MORE_PHOTO'],
        'SIZES'=> array(
            'WIDTH' => 300,
            'HEIGHT' => 300
        ),
        'SKU_ARTICLE_CODE' => $arParams['RSFLYAWAY_PROP_ARTICLE']
    )
);

$arResult['JSON_EXT']['CAT_PRICES'] = $arResult['CAT_PRICES'];

if(!empty($arResult['OFFERS']) && is_array($arResult['OFFERS'])) {

    foreach($arResult['OFFERS'] as $arOffer) {

        if(empty($arResult['JSON_EXT']['OFFERS'][$arOffer['ID']])) {
            continue;
        }

        $arResult['JSON_EXT']['OFFERS'][$arOffer['ID']]['QUANTITY'] = $arOffer['CATALOG_QUANTITY'];
    }

}
