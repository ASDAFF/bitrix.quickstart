<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;

$context = $arParams['CONTEXT'];
$arResult['SOURCE_FIELD_ENUM'] = [];

foreach ($arResult['SOURCE_TYPE_ENUM'] as $sourceEnumKey => $sourceEnum)
{
	if (!$sourceEnum['VARIABLE'] && !$sourceEnum['TEMPLATE'] && $sourceEnum['ID'] !== $arResult['RECOMMENDATION_TYPE'])
	{
		$source = Market\Export\Entity\Manager::getSource($sourceEnum['ID']);

		$fields = $source->getFields($context);
		$hasFields = false;

		foreach ($fields as $field)
		{
			if ($field['SELECTABLE'])
			{
				$hasFields = true;

				$arResult['SOURCE_FIELD_ENUM'][$sourceEnum['ID'] . '.' . $field['ID']] = [
					'ID' => $field['ID'],
					'VALUE' => $field['VALUE'],
					'TYPE' => $field['TYPE'],
					'SOURCE' => $sourceEnum['ID']
				];
			}
		}

		if (!$hasFields)
		{
			unset($arResult['SOURCE_TYPE_ENUM'][$sourceEnumKey]);
		}
	}
}