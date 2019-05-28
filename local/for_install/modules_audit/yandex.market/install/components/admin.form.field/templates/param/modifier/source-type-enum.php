<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;

$sourceTypeList = Market\Export\Entity\Manager::getSourceTypeList();
$arResult['SOURCE_TYPE_ENUM'] = [];

// recommendation

$arResult['RECOMMENDATION_TYPE'] = Market\Export\ParamValue\Table::SOURCE_TYPE_RECOMMENDATION;

$arResult['SOURCE_TYPE_ENUM'][$arResult['RECOMMENDATION_TYPE']] = [
	'ID' => $arResult['RECOMMENDATION_TYPE'],
	'VALUE' => Market\Export\ParamValue\Table::getFieldEnumTitle('SOURCE_TYPE', $arResult['RECOMMENDATION_TYPE']),
	'VARIABLE' => false
];

// sources

foreach ($sourceTypeList as $sourceType)
{
	$source = Market\Export\Entity\Manager::getSource($sourceType);

	if ($source->isSelectable())
	{
		$arResult['SOURCE_TYPE_ENUM'][$sourceType] = [
			'ID' => $sourceType,
			'VALUE' => $source->getTitle(),
			'VARIABLE' => $source->isVariable(),
			'TEMPLATE' => $source->isTemplate()
		];
	}
}