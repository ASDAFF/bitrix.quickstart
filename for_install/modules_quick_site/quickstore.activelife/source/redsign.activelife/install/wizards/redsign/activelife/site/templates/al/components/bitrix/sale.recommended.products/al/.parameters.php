<?php

use \Bitrix\Main\Loader;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

$arTemplateParameters = array(
    // ajaxpages id
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
/*
    'USE_AJAXPAGES' => array(
        'PARENT' => 'PAGER_SETTINGS',
        'NAME' => getMessage('RS_SLINE.USE_AJAXPAGES'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'Y',
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
*/
);

if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
    return;
}

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
        $arTemplateParameters['BRAND__PROP_'.$catalog['IBLOCK_ID']] = array(
            'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
            'NAME' => getMessage('RS_SLINE.BRAND__PROP'),
            'TYPE' => 'LIST',
            'VALUES' => array_merge($defaultListValues, $arProperty),
            'DEFAULT' => '-',
        );
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
            'DEFAULT' => '',
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