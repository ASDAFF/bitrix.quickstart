<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// �������� ������� ������
$baseCurrency = CCurrency::GetBaseCurrency();
$arResult["CURRENCY"] = getCurrencyAbbr($baseCurrency);
$arResult["SUM"] = number_format($arResult["SUM"], 0, ".", " ");
