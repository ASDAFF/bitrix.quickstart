<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;

$context = (array)$arParams['CONTEXT'];
$sourceTypeList = Market\Export\Entity\Manager::getSourceTypeList();

$arResult['SOURCE_ENUM'] = [];
$arResult['FIELD_ENUM'] = [];
$arResult['VALUE_ENUM'] = [];

foreach ($sourceTypeList as $sourceType)
{
	$source = Market\Export\Entity\Manager::getSource($sourceType);

	if ($source->isFilterable())
	{
		$fields = $source->getFields($context);
		$hasFields = false;

		foreach ($fields as $fieldKey => $field)
		{
			if ($field['FILTERABLE'])
			{
				$hasFields = true;
				$fieldFullId = $sourceType . '.' . $field['ID'];
				$enum = $source->getFieldEnum($field, $context);

				$field['ID'] = $fieldFullId;
				$field['SOURCE'] = $sourceType;

				$arResult['FIELD_ENUM'][$fieldFullId] = $field;

				if (!empty($enum))
				{
					$arResult['VALUE_ENUM'][$fieldFullId] = $enum;
				}
			}
		}

		if ($hasFields)
		{
			$arResult['SOURCE_ENUM'][$sourceType] = [
				'ID' => $sourceType,
				'VALUE' => $source->getTitle()
			];
		}
	}
}