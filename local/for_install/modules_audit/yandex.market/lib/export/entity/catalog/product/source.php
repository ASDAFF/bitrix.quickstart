<?php

namespace Yandex\Market\Export\Entity\Catalog\Product;

use Yandex\Market;
use Bitrix\Main;
use Bitrix\Catalog;

Main\Localization\Loc::loadMessages(__FILE__);

class Source extends Market\Export\Entity\Reference\Source
{
	public function getQuerySelect($select)
	{
		$result = [
			'CATALOG' => []
		];

		foreach ($select as $fieldName)
		{
			$result['CATALOG'][] = 'CATALOG_' . $fieldName;
		}

		return $result;
	}

	public function isFilterable()
	{
		return true;
	}

	public function getQueryFilter($filter, $select)
	{
		$result = [
			'ELEMENT' => [],
			'CATALOG' => []
		];

		foreach ($filter as $filterItem)
		{
			$sourceKey = 'CATALOG';

			if ($filterItem['FIELD'] === 'TYPE')
			{
				$sourceKey = 'ELEMENT';
			}

			$result[$sourceKey][$filterItem['COMPARE'] . 'CATALOG_' . $filterItem['FIELD']] = $filterItem['VALUE'];
		}

		return $result;
	}

	public function getElementListValues($elementList, $parentList, $select, $queryContext, $sourceValues)
	{
		$result = [];

		if (!empty($elementList))
		{
			foreach ($elementList as $elementId => $element)
			{
				$result[$elementId] = [];

				foreach ($select as $fieldName)
				{
					$elementKey = 'CATALOG_' . $fieldName;
					$elementValue = null;

					if (isset($element[$elementKey]))
					{
						$originalValue = $element[$elementKey];

						switch ($fieldName)
						{
							case 'WEIGHT':
								if ((float)$originalValue > 0)
								{
									$elementValue = $originalValue;
								}
							break;

							default:
								$elementValue = $originalValue;
							break;
						}
					}

					$result[$elementId][$fieldName] = $elementValue;
				}
			}
		}

		return $result;
	}

	public function getFields(array $context = [])
	{
		$result = [];

		if ($context['HAS_CATALOG'])
		{
			$result = $this->buildFieldsDescription([
				'WEIGHT' => [
					'TYPE' => Market\Export\Entity\Data::TYPE_NUMBER
				],
				'LENGTH' => [
					'TYPE' => Market\Export\Entity\Data::TYPE_NUMBER
				],
				'HEIGHT' => [
					'TYPE' => Market\Export\Entity\Data::TYPE_NUMBER
				],
				'WIDTH' => [
					'TYPE' => Market\Export\Entity\Data::TYPE_NUMBER
				],
				'AVAILABLE' => [
					'TYPE' => Market\Export\Entity\Data::TYPE_BOOLEAN
				],
				'QUANTITY' => [
					'TYPE' => Market\Export\Entity\Data::TYPE_NUMBER
				],
				'MEASURE' => [
					'TYPE' => Market\Export\Entity\Data::TYPE_STRING
				],
				'VAT' => [
					'TYPE' => Market\Export\Entity\Data::TYPE_NUMBER
				],
				'TYPE' => [
					'TYPE' => Market\Export\Entity\Data::TYPE_ENUM,
					'SELECTABLE' => false
				]
			]);
		}

		return $result;
	}

	public function getFieldEnum($field, array $context = [])
	{
		$result = null;

		switch ($field['ID'])
		{
			case 'TYPE':
				$result = $this->getCatalogProductTypes();
			break;

			default:
				$result = parent::getFieldEnum($field, $context);
			break;
		}

		return $result;
	}

	protected function getLangPrefix()
	{
		return 'CATALOG_PRODUCT_';
	}

	protected function getCatalogProductTypes()
	{
		$result = [];

		if (Main\Loader::includeModule('catalog'))
		{
			$types = [
				'TYPE_PRODUCT',
				'TYPE_SET',
				'TYPE_SKU'
			];

			foreach ($types as $type)
			{
				$constantName = '\CCatalogProduct::' . $type;

				if (defined($constantName))
				{
					$result[] = [
						'ID' => constant($constantName),
						'VALUE' => Market\Config::getLang($this->getLangPrefix() . 'FIELD_TYPE_ENUM_' . $type)
					];
				}
			}
		}

		return $result;
	}
}