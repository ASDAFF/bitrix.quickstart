<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Bitrix\Main\Localization\Loc;
use Yandex\Market\Utils;

$arResult['SUMMARY_LIST'] = [];
$arResult['HAS_SUMMARY'] = false;

$summaryValues = $arParams['MULTIPLE'] ? $arParams['VALUE'] : array( $arParams['VALUE'] );
$lang = $arResult['LANG'];

foreach ($summaryValues as $itemIndex => $itemValue)
{
	if (!empty($itemValue['PLACEHOLDER'])) { continue; }

	$textParts = [];
	$hasPeriodFrom = ((int)$itemValue['PERIOD_FROM'] >= 0 && trim($itemValue['PERIOD_FROM']) !== '');
	$hasPeriodTo = ((int)$itemValue['PERIOD_TO'] >= 0 && trim($itemValue['PERIOD_TO']) !== '');
	$hasPrice = ((int)$itemValue['PRICE'] >= 0 && trim($itemValue['PRICE']) !== '');

	if (!$hasPrice || !($hasPeriodFrom || $hasPeriodTo)) { continue; }

	if (strlen($itemValue['DELIVERY_TYPE']) > 0)
	{
		$textParts[] = $lang['DELIVERY_TYPE_' . strtoupper($itemValue['DELIVERY_TYPE'])];
	}

	if (strlen($itemValue['NAME']) > 0)
	{
		$textParts[] = str_replace('#NAME#', $itemValue['NAME'], $lang['NAME']);
	}

	if (strlen($itemValue['ORDER_BEFORE']))
	{
		$textParts[] = str_replace(
			[ '#ORDER_BEFORE#', '#HOUR_LABEL#' ],
			[
				$itemValue['ORDER_BEFORE'],
		        Utils::sklon($itemValue['ORDER_BEFORE'], [
					$lang['HOUR_1'],
					$lang['HOUR_2'],
					$lang['HOUR_5'],
				])
		    ],
			$lang['ORDER_BEFORE_LANG']
		);
	}

	if ($hasPeriodFrom || $hasPeriodTo)
	{
		$period = '';
		$periodForSklon = null;

		if ($hasPeriodFrom && $hasPeriodTo)
		{
			$period = $itemValue['PERIOD_FROM'] . '-' . $itemValue['PERIOD_TO'];
			$periodForSklon = $itemValue['PERIOD_TO'];
		}
		else if ($hasPeriodFrom)
		{
			$period = $itemValue['PERIOD_FROM'];
			$periodForSklon = $itemValue['PERIOD_FROM'];
		}
		else
		{
			$period = $itemValue['PERIOD_TO'];
			$periodForSklon = $itemValue['PERIOD_TO'];
		}

		$textParts[] = str_replace(
			[ '#DAY_PERIOD#', '#DAY_LABEL#' ],
			[
				$period,
		        Utils::sklon($periodForSklon, [
					$lang['DAY_1'],
					$lang['DAY_2'],
					$lang['DAY_5'],
				])
		    ],
		    $lang['PERIOD_LANG']
		);
	}

	if ($hasPrice)
	{
		$textParts[] = str_replace(
			[ '#PRICE#', '#PRICE_CURRENCY#' ],
			[
				$itemValue['PRICE'],
				Utils::sklon($itemValue['PRICE'], [
					$lang['PRICE_CURRENCY_1'],
					$lang['PRICE_CURRENCY_2'],
					$lang['PRICE_CURRENCY_5'],
				])
			],
			$lang['PRICE_LANG']
		);
	}

	$text = trim(implode(' ', $textParts));
	$text = ToUpper(substr($text, 0, 1)) . substr($text, 1);

	$arResult['HAS_SUMMARY'] = true;
	$arResult['SUMMARY_LIST'][$itemIndex] = [
		'TEXT' => $text,
		'PLACEHOLDER' => false
	];
}

if (!$arResult['HAS_SUMMARY'])
{
	$arResult['SUMMARY_LIST'][] = [
		'TEXT' => '',
		'PLACEHOLDER' => true
	];
}
