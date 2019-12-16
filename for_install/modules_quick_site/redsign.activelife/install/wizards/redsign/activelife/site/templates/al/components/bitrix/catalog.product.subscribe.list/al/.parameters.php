<?php

use \Bitrix\Main\Loader;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!\Bitrix\Main\Loader::includeModule('iblock')
    || !\Bitrix\Main\Loader::includeModule('catalog')){
    return;
}

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

$arTemplateParameters = array(
    'SECTION_TITLE' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.SECTION_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => getMessage('RS_SLINE.SECTION_TITLE_SAMPLE'),
    ),
    'NOVELTY_TIME' => array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.NOVELTY_TIME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '720',
    ),

    'TEMPLATE_AJAXID' => array(
        'PARENT' => 'PAGER_SETTINGS',
        'NAME' => getMessage('RS_SLINE.TEMPLATE_AJAXID'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'ajaxpages_catalog_identifier',
    ),
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
    "LINE_ELEMENT_COUNT" => array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage("RS_SLINE.LINE_ELEMENT_COUNT"),
        "TYPE" => "STRING",
        "DEFAULT" => "5",
    ),
);
    
$arIBlock = array();
$rsIBlocks = CIBlock::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'));
while($IBlock = $rsIBlocks->fetch()){
    $iblockMap[$iblock['ID']] = $iblock;
}

$catalogs = array();
$productsCatalogs = array();
$skuCatalogs = array();
$catalogIterator = CCatalog::GetList(
    array('IBLOCK_ID' => 'ASC'),
    array('@IBLOCK_ID' => array_keys($iblockMap)),
    false,
    false,
    array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID', 'SKU_PROPERTY_ID')
);
$iblockMap = array();
$iblockIterator = CIBlock::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'));
while ($iblock = $iblockIterator->fetch()){
    $iblockMap[$iblock['ID']] = $iblock;
}
$catalogs = array();
$catalogIterator = CCatalog::GetList(
    array('IBLOCK_ID' => 'ASC'),
    array('@IBLOCK_ID' => array_keys($iblockMap)),
    false,
    false,
    array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID', 'SKU_PROPERTY_ID')
);
while($catalog = $catalogIterator->fetch()){
    if((int)$catalog['PRODUCT_IBLOCK_ID'] > 0){
        $catalogs[] = $catalog;
    }
    else{
        $catalogs[] = $catalog;
    }
}
foreach($catalogs as $catalog){
    $arProperty = array();
    if(0 < intval($catalog['IBLOCK_ID'])){
        $rsProp = CIBlockProperty::GetList(Array('sort'=>'asc', 'name'=>'asc'), Array('IBLOCK_ID'=>$catalog['IBLOCK_ID'], 'ACTIVE'=>'Y'));
        while($arr=$rsProp->Fetch()){
            $arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
        }
    }
    if(isset($arCurrentValues['SHOW_PRODUCTS_' . $catalog['IBLOCK_ID']]) &&    'Y' == $arCurrentValues['SHOW_PRODUCTS_' . $catalog['IBLOCK_ID']]){

        $arTemplateParameters['BRAND_PROP_'.$catalog['IBLOCK_ID']] = array(
            'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
            'NAME' => getMessage('RS_SLINE.BRAND_PROP'),
            'TYPE' => 'LIST',
            'VALUES' => array_merge($defaultListValues, $arProperty),
            'DEFAULT' => '-',
            'REFRESH' => 'Y',
        );

        if ($arCurrentValues['BRAND_PROP_'.$catalog['IBLOCK_ID']] != '-') { 
        $arTemplateParameters['CATALOG_FILTER_NAME_'.$catalog['IBLOCK_ID']] = array(
            "PARENT" => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
            "NAME" => getMessage("RS_SLINE.CATALOG_FILTER_NAME"),
            "TYPE" => "STRING",
            "DEFAULT" => "arrFilter",
        );
        }

        if($arCurrentValues["USE_LIKES"] == 'Y') {
            $arTemplateParameters['LIKES_COUNT_PROP_'.$catalog['IBLOCK_ID']] = array(
                'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
                'NAME' => getMessage('RS_SLINE.LIKES_COUNT_PROP'),
                'TYPE' => 'LIST',
                'VALUES' => array_merge($defaultListValues, $arProperty),
                'DEFAULT' => '-',
            );
        }
        

        $arTemplateParameters['ICON_NOVELTY_PROP_'.$catalog['IBLOCK_ID']] = array(
            'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
            'NAME' => getMessage('RS_SLINE.ICON_NOVELTY_PROP'),
            'TYPE' => 'LIST',
            'VALUES' => array_merge($defaultListValues, $arProperty),
            'DEFAULT' => '-',
        );

        $arTemplateParameters['ICON_DEALS_PROP_'.$catalog['IBLOCK_ID']] = array(
            'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
            'NAME' => getMessage('RS_SLINE.ICON_DEALS_PROP'),
            'TYPE' => 'LIST',
            'VALUES' => array_merge($defaultListValues, $arProperty),
            'DEFAULT' => '-',
        );

        $arTemplateParameters['ICON_DISCOUNT_PROP_'.$catalog['IBLOCK_ID']] = array(
            'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
            'NAME' => getMessage('RS_SLINE.ICON_DISCOUNT_PROP'),
            'TYPE' => 'LIST',
            'VALUES' => array_merge($defaultListValues, $arProperty),
            'DEFAULT' => '-',
        );

        $arTemplateParameters['ICON_MEN_PROP_'.$catalog['IBLOCK_ID']] = array(
            'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
            'NAME' => getMessage('RS_SLINE.ICON_MEN_PROP'),
            'TYPE' => 'LIST',
            'VALUES' => array_merge($defaultListValues, $arProperty),
            'DEFAULT' => '-',
        );

        $arTemplateParameters['ICON_WOMEN_PROP_'.$catalog['IBLOCK_ID']] = array(
            'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
            'NAME' => getMessage('RS_SLINE.ICON_WOMEN_PROP'),
            'TYPE' => 'LIST',
            'VALUES' => array_merge($defaultListValues, $arProperty),
            'DEFAULT' => '-',
        );
    }
    elseif(0 < (int)$catalog['SKU_PROPERTY_ID'] && isset($arCurrentValues['SHOW_PRODUCTS_' . $catalog['PRODUCT_IBLOCK_ID']]) &&    'Y' == $arCurrentValues['SHOW_PRODUCTS_' . $catalog['PRODUCT_IBLOCK_ID']]){
        $arTemplateParameters['OFFER_TREE_PROPS_'.$catalog['IBLOCK_ID']] = array(
            'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
            'NAME' => getMessage('RS_SLINE.OFFER_TREE_PROPS'),
            'TYPE' => 'LIST',
            'VALUES' => array_merge($defaultListValues, $arProperty),
            'MULTIPLE' => 'Y',
            'DEFAULT' => '-',
        );
        $arTemplateParameters['OFFER_TREE_COLOR_PROPS_'.$catalog['IBLOCK_ID']] = array(
            'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
            'NAME' => getMessage('RS_SLINE.OFFER_TREE_COLOR_PROPS'),
            'TYPE' => 'LIST',
            'VALUES' => array_merge($defaultListValues, $arProperty),
            'MULTIPLE' => 'Y',
            'DEFAULT' => '-',
        );
    }
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