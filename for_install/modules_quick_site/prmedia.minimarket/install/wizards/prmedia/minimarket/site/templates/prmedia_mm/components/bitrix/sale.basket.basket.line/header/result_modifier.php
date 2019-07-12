<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CCacheManager $CACHE_MANAGER
 * @global CDatabase $DB
 * @param array $arParams
 * @param array $arResult
 */

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

// include required modules
if (!\Bitrix\Main\Loader::includeModule('sale'))
{
	ShowError(Loc::getMessage('PRMEDIA_MM_SBBL_ORDER_SUM'));
	$arResult = false;
	return;
}

// get basket sum
$arResult['SUM'] = 0;
$selectParams = array(
	'filter' => array(
		'FUSER_ID' => CSaleBasket::GetBasketUserID(),
		'LID' => SITE_ID,
		'ORDER_ID' => 'NULL'
	)
);
$rsBasket = CSaleBasket::GetList(array(), $selectParams['filter']);
while ($arBasket = $rsBasket->Fetch())
{
	$arResult['SUM'] += $arBasket['PRICE'] * $arBasket['QUANTITY'];
}

// get format
$arResult['CURRENCY'] = CSaleLang::GetLangCurrency(SITE_ID);
$currencyFormat = CCurrencyLang::GetCurrencyFormat($arResult['CURRENCY']);
$arResult['CURRENCY_FORMAT'] = $currencyFormat['FORMAT_STRING'];

// format sum
$arResult['SUM_FORMATTED'] = CCurrencyLang::CurrencyFormat($arResult['SUM'], $arResult['CURRENCY'], true);
if ($arResult['SUM'] > 0)
{
	$priceTemplate = '/' . str_replace('#', '(.+)', $arResult['CURRENCY_FORMAT']) . '/';
	$priceTemplate = str_replace('$', '\$', $priceTemplate);
	$arResult['SUM_FORMATTED'] = preg_replace_callback($priceTemplate, function ($matches) {
		return $matches[1];
	}, $arResult['SUM_FORMATTED']);
	$sumLink = '<a href="' . $arParams['PATH_TO_BASKET'] . '">';
	$sumLink .= $arResult['SUM_FORMATTED'];
	$sumLink .= '</a>';
	$arResult['SUM_FORMATTED'] = str_replace('#', $sumLink, $arResult['CURRENCY_FORMAT']);
}