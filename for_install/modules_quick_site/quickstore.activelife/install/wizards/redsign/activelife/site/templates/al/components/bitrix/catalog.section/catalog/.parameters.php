<?php

use \Bitrix\Main\Loader;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
    return;
}

$arIBlock = array();
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'));
while ($arr = $rsIBlock->Fetch()){
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
}

$arrPopupDetailVariable = array(
    'ON_IMAGE' => getMessage('RS_SLINE.POPUP_DETAIL_VARIABLE_IMAGE'),
    'ON_LUPA' => getMessage('RS_SLINE.POPUP_DETAIL_VARIABLE_LUPA'),
    'ON_NONE' => getMessage('RS_SLINE.POPUP_DETAIL_VARIABLE_NONE'),
);

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

$IBLOCK_ID = $arCurrentValues['IBLOCK_ID'];
$arProperty = array();
if(0 < intval($IBLOCK_ID)){
    $rsProp = CIBlockProperty::GetList(Array('sort'=>'asc', 'name'=>'asc'), Array('IBLOCK_ID'=>$IBLOCK_ID, 'ACTIVE'=>'Y'));
    while($arr=$rsProp->Fetch()){
        $arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
    }
}

$arPrice = array();
$rsPrice = CCatalogGroup::GetList($v1='sort', $v2='asc');
while($arr = $rsPrice->Fetch()){
    $arPrice[$arr['NAME']] = '['.$arr['NAME'].'] '.$arr['NAME_LANG'];
}

$arTemplateParameters = array(
    // PRICES
    'SHOW_OLD_PRICE' => array(
        'PARENT' => 'PRICES',
        'NAME' => getMessage('RS_SLINE.SHOW_OLD_PRICE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
    ),
    'SHOW_DISCOUNT_PERCENT' => array(
        'PARENT' => 'PRICES',
        'NAME' => getMessage('RS_SLINE.SHOW_DISCOUNT_PERCENT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y'
    ),
    
    // ajaxpages id
    'USE_AJAXPAGES' => array(
        'PARENT' => 'PAGER_SETTINGS',
        'NAME' => getMessage('RS_SLINE.USE_AJAXPAGES'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y',
    ),
    'AJAX_FILTER_PROPS' => array(
        'PARENT' => 'PAGER_SETTINGS',
        'NAME' => getMessage('RS_SLINE.AJAX_FILTER_PROPS'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge(
            $defaultListValues,
            $arProperty,
            array(
                'ALL_PRODUCT' => getMessage('RS_SLINE.TAB_ALL'),
                'VIEWED_PRODUCT' => getMessage('RS_SLINE.TAB_VIEWED_PRODUCT'),
                'FAVORITE_PRODUCT' => getMessage('RS_SLINE.TAB_FAVORITE_PRODUCT'),
                'BESTSELLER_PRODUCT' => getMessage('RS_SLINE.TAB_BESTSELLER_PRODUCT'),
                'BIGDATA_PRODUCT' => getMessage('RS_SLINE.TAB_BIGDATA_PRODUCT'),
            )
        ),
        'MULTIPLE' => 'Y',
        'DEFAULT' => '-',
    ),
    // man & women
    'ICON_MEN_PROP' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.ICON_MEN_PROP'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty),
        'DEFAULT' => '-',
    ),
    'ICON_WOMEN_PROP' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.ICON_WOMEN_PROP'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty),
        'DEFAULT' => '-',
    ),
    // new & discount
    'ICON_NOVELTY_PROP' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.ICON_NOVELTY_PROP'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty),
        'DEFAULT' => '-',
    ),
    'NOVELTY_TIME' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.NOVELTY_TIME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '720',
    ),
    'ICON_DISCOUNT_PROP' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.ICON_DISCOUNT_PROP'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty),
        'DEFAULT' => '-',
    ),
    'ICON_DEALS_PROP' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.ICON_DEALS_PROP'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty),
        'DEFAULT' => '-',
    ),
    'ADDITIONAL_PICT_PROP' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.ADDITIONAL_PICT_PROP'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty),
        'DEFAULT' => '-',
    ),
    'BRAND_PROP' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.BRAND_PROP'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty),
        'DEFAULT' => '-',
        'REFRESH' => 'Y'
    ),
    'LIKES_COUNT_PROP' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.LIKES_COUNT_PROP'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty),
        'DEFAULT' => '-',
    ),
    /*
    'USE_DELETE' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.USE_DELETE'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => '',
    ),
    'DELETE_FROM' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.DELETE_FROM'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ),
    */
    'USE_LIKES' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.USE_LIKES'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'Y',
    ),
    'USE_SHARE' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.USE_SHARE'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'Y',
    ),
    'POPUP_DETAIL_VARIABLE' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.POPUP_DETAIL_VARIABLE'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'N',
        'VALUES' => $arrPopupDetailVariable,
        'REFRESH' => 'N',
    ),
    'ERROR_EMPTY_ITEMS' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.ERROR_EMPTY_ITEMS'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'N',
    ),
    // show empty products error
    'COMPOSITE_MODE_REQUEST' => array(
        'NAME' => getMessage('RS_SLINE.COMPOSITE_MODE_REQUEST'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'Y',
    ),
    "PREVIEW_TRUNCATE_LEN" => array(
        'PARENT' => 'VISUAL',
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("RS_SLINE.PREVIEW_TRUNCATE_LEN"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    ),
    
    "USE_SLIDER_MODE" => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.USE_SLIDER_MODE'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'N',
    ),
    "DISPLAY_COMPARE" => array(
        "PARENT" => "COMPARE",
        "NAME" => GetMessage("RS_SLINE.DISPLAY_COMPARE"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "REFRESH" => "Y",
    ),
);

if ($arCurrentValues['BRAND_PROP'] != '-') {
/*
	$arTemplateParameters['BRAND_IBLOCK_ID'] = array(
        'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.BRAND_IBLOCK_ID'),
		'TYPE' => 'LIST',
		'VALUES' => $arIBlock,
		'DEFAULT' => '',
        'REFRESH' => 'Y',
	);
	$BRAND_IBLOCK_ID = intval($arCurrentValues['BRAND_IBLOCK_ID']);
	if($BRAND_IBLOCK_ID){
		$arBrandProperty = array();
		if(0 < intval($IBLOCK_ID)){
			$rsBrandProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $BRAND_IBLOCK_ID, 'ACTIVE' => 'Y'));
			while($arr = $rsBrandProp->Fetch()){
				$arBrandProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
			}
		}
		$arTemplateParameters['BRAND_IBLOCK_BRAND_PROP'] = array(
            'PARENT' => 'VISUAL',
			'NAME' => getMessage('RS_SLINE.BRAND_IBLOCK_BRAND_PROP'),
			'TYPE' => 'LIST',
			'VALUES' => array_merge($defaultListValues, $arBrandProperty),
			'DEFAULT' => '-',
		);
	}
*/    
    $arTemplateParameters['CATALOG_FILTER_NAME'] = array(
        "PARENT" => "VISUAL",
        "NAME" => getMessage("RS_SLINE.CATALOG_FILTER_NAME"),
        "TYPE" => "STRING",
        "DEFAULT" => "arrFilter",
    );
}

if ($arCurrentValues['USE_AJAXPAGES'] == 'Y' || $arCurrentValues['AJAX_FILTER_PROPS'] == 'Y') {
    $arTemplateParameters['TEMPLATE_AJAXID'] = array(
        'PARENT' => 'PAGER_SETTINGS',
        'NAME' => getMessage('RS_SLINE.TEMPLATE_AJAXID'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'ajaxpages_catalog_identifier',
    );
}

if ($arCurrentValues['USE_AJAXPAGES'] == 'Y') {
    $arTemplateParameters['USE_AUTO_AJAXPAGES'] = array(
        'PARENT' => 'PAGER_SETTINGS',
        'NAME' => getMessage('RS_SLINE.USE_AUTO_AJAXPAGES'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'N',
    );
}

$arOffers = CIBlockPriceTools::GetOffersIBlock($IBLOCK_ID);
$OFFERS_IBLOCK_ID = is_array($arOffers) ? $arOffers['OFFERS_IBLOCK_ID']: 0;

if($OFFERS_IBLOCK_ID){
    $arProperty_Offers = array();
    $rsProp = CIBlockProperty::GetList(array('sort'=>'asc', 'name'=>'asc'), array('IBLOCK_ID'=>$OFFERS_IBLOCK_ID, 'ACTIVE'=>'Y'));
    while($arr=$rsProp->Fetch()){
        $arr['ID'] = intval($arr['ID']);
        if ($arOffers['OFFERS_PROPERTY_ID'] == $arr['ID'])
            continue;
        $strPropName = '['.$arr['ID'].']'.('' != $arr['CODE'] ? '['.$arr['CODE'].']' : '').' '.$arr['NAME'];
        if ('' == $arr['CODE'])
            $arr['CODE'] = $arr['ID'];
        $arProperty_Offers[$arr['CODE']] = $strPropName;
    }

    $arTemplateParameters['OFFER_ADDITIONAL_PICT_PROP'] = array(
        'PARENT' => 'OFFERS_SETTINGS',
        'NAME' => getMessage('RS_SLINE.OFFER_ADDITIONAL_PICT_PROP'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
        'DEFAULT' => '-',
    );
    $arTemplateParameters['OFFER_TREE_PROPS'] = array(
        'PARENT' => 'OFFERS_SETTINGS',
        'NAME' => getMessage('RS_SLINE.OFFER_TREE_PROPS'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
        'MULTIPLE' => 'Y',
        'DEFAULT' => '-',
    );
    $arTemplateParameters['OFFER_TREE_BTN_PROPS'] = array(
        'PARENT' => 'OFFERS_SETTINGS',
        'NAME' => getMessage('RS_SLINE.OFFER_TREE_BTN_PROPS'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
        'MULTIPLE' => 'Y',
        'DEFAULT' => '-',
    );
    $arTemplateParameters['OFFER_TREE_COLOR_PROPS'] = array(
        'PARENT' => 'OFFERS_SETTINGS',
        'NAME' => getMessage('RS_SLINE.OFFER_TREE_COLOR_PROPS'),
        'TYPE' => 'LIST',
        'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
        'MULTIPLE' => 'Y',
        'DEFAULT' => '-',
    );
}

if ($arCurrentValues['USE_SHARE'] == 'Y') {

    $arSocialServices = array(
        'blogger' => getMessage('RS_SLINE.SOCIAL_SERVICES.BLOGGER'),
        'delicious' => getMessage('RS_SLINE.SOCIAL_SERVICES.DELICIOUS'),
        'digg' => getMessage('RS_SLINE.SOCIAL_SERVICES.DIGG'),
        'evernote' => getMessage('RS_SLINE.SOCIAL_SERVICES.EVERNOTE'),
        'facebook' => getMessage('RS_SLINE.SOCIAL_SERVICES.FACEBOOK'),
        'gplus' => getMessage('RS_SLINE.SOCIAL_SERVICES.GPLUS'),
        'linkedin' => getMessage('RS_SLINE.SOCIAL_SERVICES.LINKEDIN'),
        'lj' => getMessage('RS_SLINE.SOCIAL_SERVICES.LJ'),
        'moimir' => getMessage('RS_SLINE.SOCIAL_SERVICES.MOIMIR'),
        'odnoklassniki' => getMessage('RS_SLINE.SOCIAL_SERVICES.ODNOKLASSNIKI'),
        'pinterest' => getMessage('RS_SLINE.SOCIAL_SERVICES.PINTEREST'),
        'pocket' => getMessage('RS_SLINE.SOCIAL_SERVICES.POCKET'),
        'qzone' => getMessage('RS_SLINE.SOCIAL_SERVICES.QZONE'),
        'reddit' => getMessage('RS_SLINE.SOCIAL_SERVICES.REDDIT'),
        'renren' => getMessage('RS_SLINE.SOCIAL_SERVICES.RENREN'),
        'sinaWeibo ' => getMessage('RS_SLINE.SOCIAL_SERVICES.SINA_WEIBO'),
        'surfingbird' => getMessage('RS_SLINE.SOCIAL_SERVICES.SURFINGBIRD'),
        'telegram' => getMessage('RS_SLINE.SOCIAL_SERVICES.TELEGRAM'),
        'tencentWeibo' => getMessage('RS_SLINE.SOCIAL_SERVICES.TENCENT_WEIBO'),
        'tumblr' => getMessage('RS_SLINE.SOCIAL_SERVICES.TUMBLR'),
        'twitter' => getMessage('RS_SLINE.SOCIAL_SERVICES.TWITTER'),
        'viber' => getMessage('RS_SLINE.SOCIAL_SERVICES.VIBER'),
        'vkontakte' => getMessage('RS_SLINE.SOCIAL_SERVICES.VKONTAKTE'),
        'whatsapp' => getMessage('RS_SLINE.SOCIAL_SERVICES.WHATSAPP'),
    );

    $arSocialCopy = array(
        'first' => getMessage('RS_SLINE.SOCIAL_COPY.FIRST'),
        'last' => getMessage('RS_SLINE.SOCIAL_COPY.LAST'),
        'hidden' => getMessage('RS_SLINE.SOCIAL_COPY.HIDDEN'),
    );
    $arSocialSize = array(
        'm' => getMessage('RS_SLINE.SOCIAL_SIZE.M'),
        's' => getMessage('RS_SLINE.SOCIAL_SIZE.S'),
    );
	$arTemplateParameters['SOCIAL_SERVICES'] = array(
        'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.SOCIAL_SERVICES'),
		'TYPE' => 'LIST',
		'VALUES' => $arSocialServices,
		'MULTIPLE' => 'Y',
		'DEFAULT' => '',
		'ADDITIONAL_VALUES' => 'Y',
	);
	$arTemplateParameters['SOCIAL_COUNTER'] = array(
        'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.SOCIAL_COUNTER'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['SOCIAL_COPY'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.SOCIAL_COPY'),
        'TYPE' => 'LIST',
        'VALUES' => $arSocialCopy
	);
	$arTemplateParameters['SOCIAL_LIMIT'] = array(
        'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.SOCIAL_LIMIT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	);
	$arTemplateParameters['SOCIAL_SIZE'] = array(
        'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.SOCIAL_SIZE'),
        'TYPE' => 'LIST',
        'VALUES' => $arSocialSize
	);
}