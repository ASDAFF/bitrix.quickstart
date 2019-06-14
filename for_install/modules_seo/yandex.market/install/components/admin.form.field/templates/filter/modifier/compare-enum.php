<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;

$compareList = Market\Export\Entity\Data::getCompareList();
$arResult['COMPARE_ENUM'] = [];

foreach ($compareList as $compare => $compareData)
{
	$compareOption = [
		'ID' => $compare,
		'VALUE' => Market\Export\Entity\Data::getCompareTitle($compare),
		'TYPE_LIST' => (array)Market\Export\Entity\Data::getCompareTypes($compare),
		'MULTIPLE' => $compareData['MULTIPLE'],
	];

	if (isset($compareData['ENUM']))
	{
		$compareOption['ENUM'] = $compareData['ENUM'];
	}

	if (isset($compareData['DEFINED']))
	{
		$compareOption['DEFINED'] = $compareData['DEFINED'];
	}

	$arResult['COMPARE_ENUM'][$compare] = $compareOption;
}