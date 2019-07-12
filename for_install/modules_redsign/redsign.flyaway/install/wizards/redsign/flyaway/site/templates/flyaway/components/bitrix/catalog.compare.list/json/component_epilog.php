<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();

use \Bitrix\Main\Localization\Loc;

global $APPLICATION, $JSON;

$JSON = array(
	'TYPE' => 'OK',
	'COUNT' => $arResult['COMPARE_CNT'],
	'COUNT_WITH_WORD' => '<span class="count">'.$arResult['COMPARE_CNT'].'</span><span class="hidden-xs">'.'&nbsp;'.Loc::getMessage('CATALOG_COMPARE_PRODUCT').$arResult["RIGHT_WORD"].'</span>',
);
