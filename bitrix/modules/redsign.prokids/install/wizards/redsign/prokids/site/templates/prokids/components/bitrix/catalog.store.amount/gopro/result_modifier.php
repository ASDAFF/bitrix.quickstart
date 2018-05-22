<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

function RSGoPro_GetResult($amount,$arParams) {
	$return = 0;
	if($arParams['GOPRO_USE_MIN_AMOUNT']=='Y') {
		if( $amount<1 ) {
			$return = '<span class="empty">'.GetMessage('GOPRO_QUANTITY_EMPTY').'</span>';
		} elseif( $amount<$arParams['MIN_AMOUNT'] ) {
			$return = GetMessage('GOPRO_QUANTITY_LOW');
		} else {
			$return = '<span class="isset">'.GetMessage('GOPRO_QUANTITY_ISSET').'</span>';
		}
	} else {
		$return = $amount;
	}
	return $return;
}

if( is_array($arResult['STORES']) && count($arResult['STORES'])>0 ) {
	foreach($arResult['STORES'] as $key => $arStore) {
		$arResult['STORES'][$key]['RESULT'] = RSGoPro_GetResult($arStore['NUM_AMOUNT'],$arParams);
	}
}

if( is_array($arResult['SKU']) && count($arResult['SKU'])>0 ) {
	foreach($arResult['SKU'] as $key => $arStore) {
		$arResult['SKU'][$key]['RESULT'] = RSGoPro_GetResult($arStore['NUM_AMOUNT'],$arParams);
	}
}