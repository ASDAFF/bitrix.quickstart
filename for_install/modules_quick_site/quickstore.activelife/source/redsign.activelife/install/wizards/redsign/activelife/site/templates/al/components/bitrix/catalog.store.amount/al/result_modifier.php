<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!empty($arResult['JS']['SKU'])) {
	foreach ($arResult['JS']['SKU'] as $iSkuId => $arSku) {
		$arResult['JS']['SKU'][$iSkuId] = array('STORES' => $arSku);
		$arResult['JS']['SKU'][$iSkuId]['MAX_AMOUNT'] = 0;
		foreach ($arSku as $arStore) {
			if ($arStore > $arResult['JS']['SKU'][$iSkuId]['MAX_AMOUNT']) {
				$arResult['JS']['SKU'][$iSkuId]['MAX_AMOUNT'] = $arStore;
			}
			$arResult['JS']['SKU'][$iSkuId]['TOTAL_AMOUNT'] += $arStore;
		}
	}
} else {
	$arResult['MAX_AMOUNT'] = 0;
	foreach ($arResult['STORES'] as $iStore => $arStore) {
		if ($arStore['AMOUNT'] > $arResult['MAX_AMOUNT']) {
			$arResult['MAX_AMOUNT'] = $arStore['AMOUNT'];
		}
		$arResult['TOTAL_AMOUNT'] += $arStore['AMOUNT'];
	}
}