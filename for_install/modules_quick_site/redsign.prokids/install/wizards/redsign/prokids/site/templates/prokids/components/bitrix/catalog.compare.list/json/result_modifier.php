<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arResult['COMPARE_CNT'] = count($arResult);
$arResult['RIGHT_WORD'] = RSDevFunc::BasketEndWord( $arResult['COMPARE_CNT'] );