<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
global $APPLICATION, ${$arParams['FILTER_NAME']};
CJSCore::Init(array("fx"));

if (is_array($arParams['PRICES_GROUPED'])) {
    $index = 0;
    $arPrepFilter = array();
    foreach($arResult['ITEMS'] as $code => $arItem){
        if($arItem['PROPERTY_TYPE'] == 'N' || isset($arItem['PRICE'])){
            if(intval($arItem['VALUES']['MIN']['VALUE']) < 1 && intval($arItem['VALUES']['MAX']['VALUE']) < 1){
                continue;
            }
            if(in_array($arItem['CODE'], $arParams['PRICES_GROUPED'])){
                foreach($arItem['GROUP_VALUES']['PRICE_GROUP_DIAPAZONS'] as $k => $v){
                    if($_REQUEST[$v['CONTROL_NAME']] == 'y' || $_REQUEST[$v['CONTROL_NAME']] == 'Y'){
                        $arPrepFilter[$index][] = array('><CATALOG_PRICE_'.$arItem['ID'] => array($v['MIN'],$v['MAX']));
                    }
                }
                if(0 < $arPrepFilter[$index]){
                    $arPrepFilter[$index]['LOGIC'] = 'OR';
                }
                $index++;
            }
        }
    }

    if(0 < count($arPrepFilter) && empty($_REQUEST['del_filter'])){
        if('sku' == $arParams['PRICES_GROUPED_FOR']){
            foreach($arPrepFilter as $k => $v){
                ${$arParams['FILTER_NAME']}['OFFERS'][] = $v;
            }
        }
        else{
            foreach($arPrepFilter as $k => $v){
                ${$arParams['FILTER_NAME']}[] = $v;
            }
        }
    }
}