<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule('webdebug.reviews')) {
	return;
}

$InterfaceID = IntVal($arParams['INTERFACE_ID']);
if (!is_numeric($InterfaceID) || $InterfaceID<=0) {
	CWD_Reviews2::ShowError(GetMessage('WDR2_ERROR_INTERFACE_EMPTY'));
	return;
}

$arResult['RATING_UNIQ_ID'] = ToLower(strlen($arParams['UNIQ_ID'])?$arParams['UNIQ_ID']:$this->randString());

if (isset($GLOBALS['WD_REVIEWS2_INTERFACE_'.$InterfaceID])) {
	$arResult['INTERFACE'] = $GLOBALS['WD_REVIEWS2_INTERFACE_'.$InterfaceID];
} else {
	$resInterface = CWD_Reviews2_Interface::GetByID($InterfaceID);
	$arResult['INTERFACE'] = $resInterface->GetNext();
}

if (isset($arParams['VALUE']) && IntVal($arParams['VALUE'])>0 && $arResult['INTERFACE']['RATING_HALF_SHOW']!='Y') {
	$arParams['VALUE'] = Round($arParams['VALUE']);
}

if (is_array($arResult['INTERFACE'])) {
	$GLOBALS['WD_REVIEWS2_GET_INTERFACE'.$InterfaceID] = $arResult['INTERFACE'];
	if (isset($arResult['INTERFACE']['~RATING_IMG_RANGE'])) {
		$arResult['INTERFACE']['RATING_IMG_RANGE'] = unserialize($arResult['INTERFACE']['~RATING_IMG_RANGE']);
	}
	$arPath = pathinfo($arResult['INTERFACE']['RATING_IMG_ACTIVE']);
	$arResult['STAR_IMAGE_PATH'] = $arPath['dirname'];
	$Score = 0;
	if(is_numeric($arParams['VALUE']) && $arParams['VALUE']>=0 && $arParams['VALUE']<=$arResult['INTERFACE']['RATING_STARS_COUNT']) {
		$Score = $arParams['VALUE'];
	} elseif($arResult['INTERFACE']['RATING_STARS_COUNT_DEF']>0 && $arResult['INTERFACE']['RATING_STARS_COUNT_DEF']<=$arResult['INTERFACE']['RATING_STARS_COUNT']) {
		$Score = $arResult['INTERFACE']['RATING_STARS_COUNT_DEF'];
	}
	$arResult['SCORE'] = $Score;
	$arResult['COUNT'] = IntVal($arParams['COUNT']);
	
	// Hints
	$arResult['HINTS'] = explode("\n", trim($arResult['INTERFACE']['RATING_STARS_HINTS']));
	if (count($arResult['HINTS'])<$arResult['INTERFACE']['RATING_STARS_COUNT']) {
		for($i=count($arResult['HINTS']);$i<$arResult['INTERFACE']['RATING_STARS_COUNT'];$i++) {
			$arResult['HINTS'][] = '';
		}
	}
	$arResult['HINTS'] = array_map(function($Value){$Value=trim($Value); return "'{$Value}'";}, $arResult['HINTS']);
	$arResult['HINTS_VALUE'] = '['.implode(',',$arResult['HINTS']).']';
	
	// Range
	$arResult['USE_RANGE'] = $arResult['INTERFACE']['RATING_USE_RANGE']=='Y' && is_array($arResult['INTERFACE']['RATING_IMG_RANGE']) && !empty($arResult['INTERFACE']['RATING_IMG_RANGE']);
	$arResult['RANGE_VALUE'] = '';
	if ($arResult['USE_RANGE']) {
		$arRanges = array();
		foreach($arResult['INTERFACE']['RATING_IMG_RANGE'] as $RangeID => $arRange) {
			$arPath = pathinfo($arRange[0]);
			$arResult['STAR_IMAGE_PATH'] = $arPath['dirname'];
			$On = $arPath['basename'];
			$arPath = pathinfo($arRange[1]);
			$Off = $arPath['basename'];
			$arRanges[] = "{range:{$RangeID},on:'{$On}',off:'{$Off}'}";
		}
		$arResult['RANGE_VALUE'] = '['.implode(','."\n",$arRanges).']';
	}
	
	$this->IncludeComponentTemplate();
}
?>