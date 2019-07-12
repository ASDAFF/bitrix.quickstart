<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

// we dont trust input params, so validation is required
$legalColors = array(
	'green' => true,
	'yellow' => true,
	'red' => true,
	'gray' => true
);
// default colors in case parameters unset
$defaultColors = array(
	'N' => 'green',
	'P' => 'yellow',
	'F' => 'gray',
	'PSEUDO_CANCELLED' => 'red'
);

foreach ($arParams as $key => $val)
	if(strpos($key, "STATUS_COLOR_") !== false && !$legalColors[$val])
		unset($arParams[$key]);

// to make orders follow in right status order
if(is_array($arResult['INFO']) && !empty($arResult['INFO']))
{
	foreach($arResult['INFO']['STATUS'] as $id => $stat)
	{
		$arResult['INFO']['STATUS'][$id]["COLOR"] = $arParams['STATUS_COLOR_'.$id] ? $arParams['STATUS_COLOR_'.$id] : (isset($defaultColors[$id]) ? $defaultColors[$id] : 'gray');
		$arResult["ORDER_BY_STATUS"][$id] = array();
	}
}
$arResult["ORDER_BY_STATUS"]["PSEUDO_CANCELLED"] = array();

$arResult["INFO"]["STATUS"]["PSEUDO_CANCELLED"] = array(
	"NAME" => Loc::getMessage('SPOL_PSEUDO_CANCELLED'),
	"COLOR" => $arParams['STATUS_COLOR_PSEUDO_CANCELLED'] ? $arParams['STATUS_COLOR_PSEUDO_CANCELLED'] : (isset($defaultColors['PSEUDO_CANCELLED']) ? $defaultColors['PSEUDO_CANCELLED'] : 'gray')
);
