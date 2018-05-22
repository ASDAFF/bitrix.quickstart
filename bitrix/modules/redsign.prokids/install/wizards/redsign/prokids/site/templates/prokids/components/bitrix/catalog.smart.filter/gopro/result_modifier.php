<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arParams['FILTER_PROP_SCROLL_CNT'] = 10;
$arParams['RSGOPRO_FILTER_NAME'] = 'PRICE_GROUPS';

$arResult = RSDevFuncResultModifier::CatalogSmartFilter($arResult);

foreach($arResult['ITEMS'] as $key => $arItem)
{
	if( IntVal($arItem['VALUES']['MIN']['VALUE'])<1 && IntVal($arItem['VALUES']['MAX']['VALUE'])<1)
	{
		continue;
	}
	if(in_array($arItem['CODE'],$arParams['FILTER_PRICE_GROUPED']))
	{
		$arrDiapazons = array(
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_FIRST') => array(
				'MIN' => 0,
				'MAX' => 99,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_FIRST'),
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_100_250') => array(
				'MIN' => 100,
				'MAX' => 249,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_100_250')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_250_500') => array(
				'MIN' => 250,
				'MAX' => 499,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_250_500')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_500_10000') => array(
				'MIN' => 500,
				'MAX' => 999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_500_10000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_1000_2500') => array(
				'MIN' => 1000,
				'MAX' => 2499,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_1000_2500')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_2500_5000') => array(
				'MIN' => 2500,
				'MAX' => 4999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_2500_5000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_5000_10000') => array(
				'MIN' => 5000,
				'MAX' => 9999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_5000_10000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_10000_25000') => array(
				'MIN' => 10000,
				'MAX' => 24999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_10000_25000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_25000_50000') => array(
				'MIN' => 25000,
				'MAX' => 49999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_25000_50000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_50000_100000') => array(
				'MIN' => 50000,
				'MAX' => 99999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_50000_100000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_100000_250000') => array(
				'MIN' => 100000,
				'MAX' => 249999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_100000_250000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_250000_500000') => array(
				'MIN' => 250000,
				'MAX' => 499999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_250000_500000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_500000_750000') => array(
				'MIN' => 500000,
				'MAX' => 749999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_500000_750000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_750000_1000000') => array(
				'MIN' => 750000,
				'MAX' => 999999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_750000_1000000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_1000000_2500000') => array(
				'MIN' => 1000000,
				'MAX' => 2549999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_1000000_2500000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_2500000_5000000') => array(
				'MIN' => 2500000,
				'MAX' => 4999999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_2500000_5000000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_5000000_7500000') => array(
				'MIN' => 5000000,
				'MAX' => 7449999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_5000000_7500000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_7500000_10000000') => array(
				'MIN' => 7500000,
				'MAX' => 9999999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_7500000_10000000')
			),
			GetMessage('FILTR_PRICE_DIAPAZON_NAME_LAST') => array(
				'MIN' => 10000000,
				'MAX' => 999999999999,
				'NAME1' => GetMessage('FILTR_PRICE_DIAPAZON_NAME_LAST')
			),
		);
		if($arItem['PROPERTY_TYPE']=='N' || isset($arItem['PRICE'])) {
			$minCalculated = RSDevFuncFilterExtension::RoundCustom($arItem['VALUES']['MIN']['VALUE'], RSDevFuncFilterExtension::GetTo4round($arItem['VALUES']['MIN']['VALUE']), 'big');
			$maxCalculated = RSDevFuncFilterExtension::RoundCustom($arItem['VALUES']['MAX']['VALUE'], RSDevFuncFilterExtension::GetTo4round($arItem['VALUES']['MAX']['VALUE']), 'small');
			$putKey = false;
			$arControlsNAME = array();
			foreach($arrDiapazons as $kluch => $arDiapazon) {
				$arrDiapazons[$kluch]['CONTROL_ID'] = $arParams['RSGOPRO_FILTER_NAME'].'_'.abs(crc32($arItem['ID'].$kluch));
				$arrDiapazons[$kluch]['CONTROL_NAME'] = $arParams['RSGOPRO_FILTER_NAME'].'_'.abs(crc32($arItem['ID'].$kluch));
				$arControlsNAME[] = $arrDiapazons[$kluch]['CONTROL_NAME'];
				if($arDiapazon['MIN'] <= $arItem['VALUES']['MIN']['VALUE'] && $arDiapazon['MAX'] >= $arItem['VALUES']['MIN']['VALUE']) {
					$putKey = true;
				}
				if($putKey) {
					$arResult['ITEMS'][$key]['GROUP_VALUES']['FOR_TEMPLATE'][$arDiapazon['NAME1']] = $arDiapazon;
					if(empty($_REQUEST['del_filter'])) {
						$arResult['ITEMS'][$key]['GROUP_VALUES']['FOR_TEMPLATE'][$arDiapazon['NAME1']]['SELECTED'] = $_REQUEST[$arrDiapazons[$kluch]['CONTROL_NAME']]=='Y' ? 'Y' : 'N';
					} else {
						$arResult['ITEMS'][$key]['GROUP_VALUES']['FOR_TEMPLATE'][$arDiapazon['NAME1']]['SELECTED'] = 'N';
					}
				}
				if($arDiapazon['MAX'] >= $arItem['VALUES']['MAX']['VALUE']){
					$putKey = false;
				}
			}
			$arResult['ITEMS'][$key]['GROUP_VALUES']['PRICE_GROUP_DIAPAZONS'] = $arrDiapazons;
			$arResult['ITEMS'][$key]['GROUP_VALUES']['CONTROL_NAME_MIN'] = $arItem['VALUES']['MIN']['CONTROL_NAME'];
			$arResult['ITEMS'][$key]['GROUP_VALUES']['CONTROL_NAME_MAX'] = $arItem['VALUES']['MAX']['CONTROL_NAME'];
			$this->__component->arResult['ITEMS'][$key]['GROUP_VALUES'] = $arResult['ITEMS'][$key]['GROUP_VALUES'];
		}

		$arNewHidden = array();
		foreach($arResult['HIDDEN'] as $hk => $hv) {
			if(!in_array($hv['CONTROL_NAME'],$arControlsNAME)) {
				$arNewHidden[] = $hv;
				$arResult['FORM_ACTION'] = str_replace($arControlsNAME,'destroy',$arResult['FORM_ACTION']);
			}
		}
		$arResult['HIDDEN'] = $arNewHidden;
	}
}