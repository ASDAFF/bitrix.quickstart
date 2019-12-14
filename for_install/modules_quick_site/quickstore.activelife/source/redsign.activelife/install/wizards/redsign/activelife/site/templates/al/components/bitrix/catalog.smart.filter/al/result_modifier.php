<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;


if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if (Loader::includeModule('redsign.activelife')){

    if (!empty($arParams['OFFER_TREE_COLOR_PROPS'])) {
        $c = Option::get('redsign.activelife', 'color_table_count', 0);
        $arrColors = array();
        for ($i = 0; $i < $c; $i++) {
            $name = Option::get('redsign.activelife', 'color_table_name_'.$i, '');
            $rgb = Option::get('redsign.activelife', 'color_table_rgb_'.$i, '');
            if ($name != '' && $rgb != '') {
                $arrColors[ToUpper($name)] = array(
                    'NAME' => $name,
                    'RGB' => $rgb,
                );
            }
        }
    }

    if (is_array($arParams['OFFER_TREE_COLOR_PROPS'])) {

        foreach($arResult['ITEMS'] as $key => $arItem){
            // prop_code color
            if(in_array($arItem['CODE'], $arParams['OFFER_TREE_COLOR_PROPS'])){
                foreach($arItem['VALUES'] as $propValue => $arrProp){
                    $arResult['ITEMS'][$key]['VALUES'][$propValue]['RGB'] = $arrColors[$arrProp['UPPER']]['RGB'];
                }
            }
        }
    }
}

if(!Bitrix\Main\Loader::includeModule('redsign.devfunc')){
    return;
}

$arParams['RS_SLINE_FILTER_NAME'] = 'PRICE_GROUPS';

$arResult = RSDevFuncResultModifier::CatalogSmartFilter($arResult);

if (is_array($arParams['PRICES_GROUPED']) && count($arParams['PRICES_GROUPED']) > 0) {
    foreach($arResult['ITEMS'] as $key => $arItem){
        if (
            in_array($arItem['CODE'], $arParams['PRICES_GROUPED'])
            && 0 < intval($arItem['VALUES']['MIN']['VALUE']) && 1 <= IntVal($arItem['VALUES']['MAX']['VALUE'])
        ){
            $arrDiapazons = array(
                getMessage('FILTR_PRICE_DIAPAZON_NAME_FIRST') => array(
                    'MIN' => 0,
                    'MAX' => 99,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_FIRST'),
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_100_250') => array(
                    'MIN' => 100,
                    'MAX' => 249,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_100_250')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_250_500') => array(
                    'MIN' => 250,
                    'MAX' => 499,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_250_500')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_500_10000') => array(
                    'MIN' => 500,
                    'MAX' => 999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_500_10000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_1000_2500') => array(
                    'MIN' => 1000,
                    'MAX' => 2499,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_1000_2500')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_2500_5000') => array(
                    'MIN' => 2500,
                    'MAX' => 4999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_2500_5000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_5000_10000') => array(
                    'MIN' => 5000,
                    'MAX' => 9999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_5000_10000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_10000_25000') => array(
                    'MIN' => 10000,
                    'MAX' => 24999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_10000_25000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_25000_50000') => array(
                    'MIN' => 25000,
                    'MAX' => 49999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_25000_50000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_50000_100000') => array(
                    'MIN' => 50000,
                    'MAX' => 99999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_50000_100000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_100000_250000') => array(
                    'MIN' => 100000,
                    'MAX' => 249999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_100000_250000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_250000_500000') => array(
                    'MIN' => 250000,
                    'MAX' => 499999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_250000_500000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_500000_750000') => array(
                    'MIN' => 500000,
                    'MAX' => 749999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_500000_750000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_750000_1000000') => array(
                    'MIN' => 750000,
                    'MAX' => 999999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_750000_1000000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_1000000_2500000') => array(
                    'MIN' => 1000000,
                    'MAX' => 2549999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_1000000_2500000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_2500000_5000000') => array(
                    'MIN' => 2500000,
                    'MAX' => 4999999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_2500000_5000000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_5000000_7500000') => array(
                    'MIN' => 5000000,
                    'MAX' => 7449999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_5000000_7500000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_7500000_10000000') => array(
                    'MIN' => 7500000,
                    'MAX' => 9999999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_7500000_10000000')
                ),
                getMessage('FILTR_PRICE_DIAPAZON_NAME_LAST') => array(
                    'MIN' => 10000000,
                    'MAX' => 999999999999,
                    'NAME1' => getMessage('FILTR_PRICE_DIAPAZON_NAME_LAST')
                ),
            );

            if ('N' == $arItem['PROPERTY_TYPE'] || isset($arItem['PRICE'])) {
                $minCalculated = RSDevFuncFilterExtension::RoundCustom($arItem['VALUES']['MIN']['VALUE'], RSDevFuncFilterExtension::GetTo4round($arItem['VALUES']['MIN']['VALUE']), 'simple');
                $maxCalculated = RSDevFuncFilterExtension::RoundCustom($arItem['VALUES']['MAX']['VALUE'], RSDevFuncFilterExtension::GetTo4round($arItem['VALUES']['MAX']['VALUE']), 'simple');

                $puKey = false;
                $arControlsNAME = array();

                foreach ($arrDiapazons as $kluch => $arDiapazon) {
                    $arrDiapazons[$kluch]['CONTROL_ID'] = $arParams['RS_SLINE_FILTER_NAME'].'_'.abs(crc32($arItem['ID'].$kluch));
                    $arrDiapazons[$kluch]['CONTROL_NAME'] = $arParams['RS_SLINE_FILTER_NAME'].'_'.abs(crc32($arItem['ID'].$kluch));
                    $arControlsNAME[] = $arrDiapazons[$kluch]['CONTROL_NAME'];
                    if ($arDiapazon['MIN'] <= $arItem['VALUES']['MIN']['VALUE'] && $arDiapazon['MAX'] >= $arItem['VALUES']['MIN']['VALUE']) {
                        $putKey = true;
                    }
                    if ($putKey) {
                        $arResult['ITEMS'][$key]['GROUP_VALUES']['FOR_TEMPLATE'][$arDiapazon['NAME1']] = $arDiapazon;
                        if (empty($_REQUEST['del_filter'])) {
                            $arResult['ITEMS'][$key]['GROUP_VALUES']['FOR_TEMPLATE'][$arDiapazon['NAME1']]['SELECTED'] = $_REQUEST[$arrDiapazons[$kluch]['CONTROL_NAME']] == 'Y' ? 'Y' : 'N';
                        } else {
                            $arResult['ITEMS'][$key]['GROUP_VALUES']['FOR_TEMPLATE'][$arDiapazon['NAME1']]['SELECTED'] = 'N';
                        }
                    }
                    if ($arDiapazon['MAX'] >= $arItem['VALUES']['MAX']['VALUE']) {
                        $putKey = false;
                    }
                }
                $arResult['ITEMS'][$key]['GROUP_VALUES']['PRICE_GROUP_DIAPAZONS'] = $arrDiapazons;
                $arResult['ITEMS'][$key]['GROUP_VALUES']['CONTROL_NAME_MIN'] = $arItem['VALUES']['MIN']['CONTROL_NAME'];
                $arResult['ITEMS'][$key]['GROUP_VALUES']['CONTROL_NAME_MAX'] = $arItem['VALUES']['MAX']['CONTROL_NAME'];
                $this->__component->arResult['ITEMS'][$key]['GROUP_VALUES'] = $arResult['ITEMS'][$key]['GROUP_VALUES'];
            }

            $arNewHidden = array();
            foreach($arResult['HIDDEN'] as $hk => $hv){
                if(!in_array($hv['CONTROL_NAME'], $arControlsNAME)){
                    $arNewHidden[] = $hv;
                }
            }
            $arResult['HIDDEN'] = $arNewHidden;
        }
    }
} else {
    $arParams['PRICES_GROUPED'] = array();
}
