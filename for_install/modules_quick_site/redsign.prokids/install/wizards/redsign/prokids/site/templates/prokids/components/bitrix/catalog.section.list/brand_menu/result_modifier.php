<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(is_array($arResult['SECTIONS']) && count($arResult['SECTIONS'])>0) {
	foreach($arResult['SECTIONS'] as $key => $arSection) {
		if( !in_array($arSection['ID'],$arParams['IDS']) )
		{
			unset($arResult['SECTIONS'][$key]);
		} elseif( $arParams['FILTER_CONTROL_NAME']!='' ) {
			$arResult['SECTIONS'][$key]['SECTION_PAGE_URL'] = $arSection['SECTION_PAGE_URL'].'?'.$arParams['FILTER_CONTROL_NAME'];
		}
	}
}