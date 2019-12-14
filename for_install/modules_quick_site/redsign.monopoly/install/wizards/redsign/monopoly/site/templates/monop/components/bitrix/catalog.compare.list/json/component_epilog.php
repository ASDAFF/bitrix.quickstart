<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION,$JSON;

$JSON = array(
	'TYPE' => 'OK',
	'COUNT' => $arResult['COMPARE_CNT'],
	'COUNT_WITH_WORD' => $arResult['COMPARE_CNT'].'<span class="hidden-xs"> '.GetMessage('CATALOG_COMPARE_PRODUCT').$arResult["RIGHT_WORD"].'</span>',
);