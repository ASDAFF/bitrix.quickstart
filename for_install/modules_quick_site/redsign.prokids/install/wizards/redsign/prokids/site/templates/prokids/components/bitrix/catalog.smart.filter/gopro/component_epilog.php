<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, ${$arParams['FILTER_NAME']};

$APPLICATION->SetAdditionalCSS($templateFolder.'/slider/slider.css');
$APPLICATION->AddHeadScript($templateFolder.'/slider/jquery.color.js');
$APPLICATION->AddHeadScript($templateFolder.'/slider/jquery.ui.core.js');
$APPLICATION->AddHeadScript($templateFolder.'/slider/jquery.ui.widget.js');
$APPLICATION->AddHeadScript($templateFolder.'/slider/jquery.ui.mouse.js');
$APPLICATION->AddHeadScript($templateFolder.'/slider/jquery.ui.slider.js');

$index = 0;
$arPrepFilter = array();
foreach($arResult['ITEMS'] as $code => $arItem) {
	if($arItem['PROPERTY_TYPE'] == 'N' || isset($arItem['PRICE'])) {
		if(intval($arItem['VALUES']['MIN']['VALUE'])<1 && intval($arItem['VALUES']['MAX']['VALUE'])<1) {
			continue;
		}
		if(in_array($arItem['CODE'],$arParams['FILTER_PRICE_GROUPED'])) {
			foreach($arItem['GROUP_VALUES']['PRICE_GROUP_DIAPAZONS'] as $k => $v) {
				if($_REQUEST[$v['CONTROL_NAME']]=='y' || $_REQUEST[$v['CONTROL_NAME']]=='Y') {
					$arPrepFilter[$index][] = array('><CATALOG_PRICE_'.$arItem['ID'] => array($v['MIN'],$v['MAX']));
				}
			}
			if($arPrepFilter[$index]>0) {
				$arPrepFilter[$index]['LOGIC'] = 'OR';
			}
			$index++;
		}
	}
}

if(count($arPrepFilter) > 0 && empty($_REQUEST['del_filter'])) {
	if($arParams['FILTER_PRICE_GROUPED_FOR']=='sku') {
		foreach($arPrepFilter as $k => $v) {
			${$arParams['FILTER_NAME']}['OFFERS'][] = $v;
		}
	} else {
		foreach($arPrepFilter as $k => $v) {
			${$arParams['FILTER_NAME']}[] = $v;
		}
	}
}