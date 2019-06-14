<?php

namespace Yandex\Market\Export\Entity\Iblock\Element\Property;

use Bitrix\Highloadblock;
use Yandex\Market;
use Bitrix\Main;
use Bitrix\Iblock;
use Bitrix\Catalog;

Main\Localization\Loc::loadMessages(__FILE__);

class Source extends Market\Export\Entity\Reference\Source
{
	protected $highloadDataClassCache = [];

	public function isFilterable()
	{
		return true;
	}

	public function getQueryFilter($filter, $select)
	{
		return [
			'ELEMENT' => $this->buildQueryFilter($filter)
		];
	}

	public function getOrder()
	{
		return 200;
	}

	public function getElementListValues($elementList, $parentList, $select, $queryContext, $sourceValues)
	{
		$result = [];
		$parentToElementMapByIblock = [];

		foreach ($elementList as $elementId => $element)
		{
			$parent = null;

			if (!isset($element['PARENT_ID'])) // is not offer
			{
				$parent = $element;
			}
			else if (isset($parentList[$element['PARENT_ID']])) // has parent
			{
				$parent = $parentList[$element['PARENT_ID']];
			}

			if (isset($parent))
			{
				if (!isset($parentToElementMapByIblock[$parent['IBLOCK_ID']]))
				{
					$parentToElementMapByIblock[$parent['IBLOCK_ID']] = [];
				}

				if (!isset($parentToElementMapByIblock[$parent['IBLOCK_ID']][$parent['ID']]))
				{
					$parentToElementMapByIblock[$parent['IBLOCK_ID']][$parent['ID']] = [];
				}

				$parentToElementMapByIblock[$parent['IBLOCK_ID']][$parent['ID']][] = $elementId;
			}
		}

		if (!empty($parentToElementMapByIblock))
		{
			$originalPropertyIds = isset($queryContext['DISCOUNT_ORIGINAL_ELEMENT_PROPERTIES']) ? $queryContext['DISCOUNT_ORIGINAL_ELEMENT_PROPERTIES'] : null;

			foreach ($parentToElementMapByIblock as $iblockId => $parentToElementMap)
			{
				$parentIds = array_keys($parentToElementMap);
				$propertyValuesList = $this->getPropertyValues($iblockId, $parentIds, $select, $queryContext, $originalPropertyIds);

				foreach ($propertyValuesList as $parentId => $propertyValues)
				{
					if (isset($parentToElementMap[$parentId]))
					{
						foreach ($parentToElementMap[$parentId] as $elementId)
						{
							$result[$elementId] = $propertyValues;
						}
					}
				}
			}
		}

		return $result;
	}

	public function getFields(array $context = [])
	{
		return $this->getPropertyFields($context['IBLOCK_ID']);
	}

	public function getPropertyFields($iblockId)
	{
		$iblockId = (int)$iblockId;
		$result = [];

		if ($iblockId > 0 && Main\Loader::includeModule('iblock'))
		{
			$query = Iblock\PropertyTable::getList([
				'filter' => ['=IBLOCK_ID' => $iblockId],
				'select' => ['ID', 'NAME', 'PROPERTY_TYPE', 'USER_TYPE', 'USER_TYPE_SETTINGS'],
			]);

			while ($propertyRow = $query->fetch())
			{
				$type = $propertyRow['PROPERTY_TYPE'];

				switch ($propertyRow['USER_TYPE'])
				{
					case 'DateTime':
						$type = Market\Export\Entity\Data::TYPE_DATETIME;
					break;

					case 'Date':
						$type = Market\Export\Entity\Data::TYPE_DATE;
					break;

					case 'ym_service_category':
						$type = Market\Export\Entity\Data::TYPE_SERVICE_CATEGORY;
					break;

					case 'directory':
						$type = Market\Export\Entity\Data::TYPE_ENUM;
					break;
				}

				$result[] = [
					'ID' => $propertyRow['ID'],
					'VALUE' => '[' . $propertyRow['ID'] . '] ' . $propertyRow['NAME'],
					'TYPE' => $type,
					'PROPERTY_TYPE' => $propertyRow['PROPERTY_TYPE'],
					'FILTERABLE' => true,
					'SELECTABLE' => true,
					'USER_TYPE' => $propertyRow['USER_TYPE'],
					'USER_TYPE_SETTINGS' => $propertyRow['USER_TYPE_SETTINGS'] ? unserialize($propertyRow['USER_TYPE_SETTINGS']) : null,
				];
			}
		}

		return $result;
	}

	public function getFieldEnum($field, array $context = [])
	{
		$result = null;

		$propertyType = $field['USER_TYPE'] ?: $field['PROPERTY_TYPE'];

		switch ($propertyType)
		{
			case 'L':

				if (Main\Loader::includeModule('iblock'))
				{
					$result = [];

					$queryEnum = Iblock\PropertyEnumerationTable::getList([
						'filter' => [
							'=PROPERTY_ID' => $field['ID']
						],
						'select' => [
							'ID',
							'VALUE'
						]
					]);

					while ($enum = $queryEnum->fetch())
					{
						$result[] = [
							'ID' => $enum['ID'],
							'VALUE' => $enum['VALUE']
						];
					}
				}

			break;

			case 'directory':

				if (
					!empty($field['USER_TYPE_SETTINGS']['TABLE_NAME'])
					&& Main\Loader::includeModule('highloadblock')
				)
				{
					$result = [];

					$queryHighload = Highloadblock\HighloadBlockTable::getList([
						'filter' => ['=TABLE_NAME' => $field['USER_TYPE_SETTINGS']['TABLE_NAME']],
					]);

					if ($highload = $queryHighload->fetch())
					{
						$entity = Highloadblock\HighloadBlockTable::compileEntity($highload);
						$dataClass = $entity->getDataClass();

						$queryEnum = $dataClass::getList();

						while ($enum = $queryEnum->fetch())
						{
							$result[] = [
								'ID' => $enum['UF_XML_ID'],
								'VALUE' => $enum['UF_NAME']
							];
						}
					}
				}

			break;

		}

		if ($result === null)
		{
			$result = parent::getFieldEnum($field, $context);
		}

		return $result;
	}

	protected function getLangPrefix()
	{
		return 'IBLOCK_ELEMENT_PROPERTY_';
	}

	protected function buildQueryFilter($filter)
	{
		$result = [];

		foreach ($filter as $filterItem)
		{
			$result[$filterItem['COMPARE'] . 'PROPERTY_' . $filterItem['FIELD']] = $filterItem['VALUE'];
        }

        return $result;
	}

	protected function getPropertyValues($iblockId, $elementIds, $select, $queryContext, $originalPropertyIds = null)
	{
		$isNeedDiscountCache = (!empty($queryContext['DISCOUNT_CACHE']) && Main\Loader::includeModule('catalog'));
		$isNeedSelectAll = $isNeedDiscountCache && empty($queryContext['DISCOUNT_PROPERTIES_OPTIMIZATION']);
		$propertyIds = $select;
		$propertyValuesList = $this->queryProperties($iblockId, $elementIds, $propertyIds, $isNeedSelectAll);
		$result = [];

		if ($isNeedDiscountCache)
		{
			foreach ($propertyValuesList as $elementId => $propertyList)
			{
				if (!empty($queryContext['DISCOUNT_ONLY_SALE']))
				{
					if (\method_exists('\Bitrix\Catalog\Discount\DiscountManager', 'setProductPropertiesCache'))
					{
						Catalog\Discount\DiscountManager::setProductPropertiesCache($elementId, $propertyList);
					}
				}
				else
				{
					\CCatalogDiscount::SetProductPropertiesCache($elementId, $propertyList);
				}
			}
		}

		$propertyIdsMap = ($originalPropertyIds !== null ?  array_flip($originalPropertyIds) : array_flip($propertyIds));

		$this->extendDisplayValue($propertyValuesList, $propertyIdsMap);

		foreach ($propertyValuesList as $elementId => $propertyList)
		{
			foreach ($propertyList as $property)
			{
				if (isset($propertyIdsMap[$property['ID']]))
				{
					if (!isset($result[$elementId]))
					{
						$result[$elementId] = [];
					}

					$result[$elementId][$property['ID']] = $this->getDisplayValue($property);
				}
			}
		}

		return $result;
	}

	protected function queryProperties($iblockId, $elementIds, $propertyIds, $isSelectAll = false)
	{
		$result = [];

		if (
			(!empty($propertyIds) || $isSelectAll)
			&& Main\Loader::includeModule('iblock')
		)
		{
			// build result for iblock method

			foreach ($elementIds as $elementId)
			{
				$result[$elementId] = [];
			}

			// query values

			\CIBlockElement::GetPropertyValuesArray($result, $iblockId, ['ID' => $elementIds], $isSelectAll ? [] : ['ID' => $propertyIds]);
		}

		return $result;
	}

	protected function extendDisplayValue(&$propertyValuesList, $usedMap)
	{
		$optimizedTypes = [
			'directory' => true,
			'E' => true,
			//'G' => true TODO before need resolve conflict with categoryId
		];
		$targetProperties = [];
		$targetPropertiesValues = [];

		foreach ($propertyValuesList as $elementId => $propertyList)
		{
			foreach ($propertyList as $property)
			{
				if (!empty($property['VALUE']) && isset($usedMap[$property['ID']]))
				{
					$isOptimizedProperty = false;

					if (isset($targetProperties[$property['ID']]))
					{
						$isOptimizedProperty = true;
					}
					else
					{
						$propertyType = $property['PROPERTY_TYPE'];

						if (isset($optimizedTypes[$property['USER_TYPE']]))
						{
							$propertyType = $property['USER_TYPE'];
						}

						if (isset($optimizedTypes[$propertyType]))
						{
							$isOptimizedProperty = true;
							$targetProperties[$property['ID']] = $property;
						}
					}

					if ($isOptimizedProperty)
					{
						if (!isset($targetPropertiesValues[$property['ID']]))
						{
							$targetPropertiesValues[$property['ID']] = [];
						}

						if (is_array($property['VALUE']))
						{
							foreach ($property['VALUE'] as $value)
							{
								$value = trim($value);

								if ($value !== '')
								{
									if (!isset($targetPropertiesValues[$property['ID']][$value]))
									{
										$targetPropertiesValues[$property['ID']][$value] = [];
									}

									$targetPropertiesValues[$property['ID']][$value][] = $elementId;
								}
							}
						}
						else
						{
							$value = trim($property['VALUE']);

							if ($value !== '')
							{
								if (!isset($targetPropertiesValues[$property['ID']][$value]))
								{
									$targetPropertiesValues[$property['ID']][$value] = [];
								}

								$targetPropertiesValues[$property['ID']][$value][] = $elementId;
							}
						}
					}
				}
			}
		}

		foreach ($targetProperties as $propertyId => $property)
		{
			$propertyValuesMap = $targetPropertiesValues[$propertyId];
			$propertyValues = array_keys($propertyValuesMap);
			$propertyType = $property['PROPERTY_TYPE'];
			$enumList = [];

			if (isset($optimizedTypes[$property['USER_TYPE']]))
			{
				$propertyType = $property['USER_TYPE'];
			}

			if (empty($propertyValues)) { continue; }

			// query enum list

			switch ($propertyType)
			{
				case 'directory':

					$highloadDataClass = $this->getHighloadDataClass($property);

					if ($highloadDataClass)
					{
						$queryEnum = $highloadDataClass::getList([
							'filter' => [
								'=UF_XML_ID' => $propertyValues,
							],
						]);

						while ($enum = $queryEnum->fetch())
						{
							if (isset($enum['UF_NAME']))
							{
								$enumList[] = [
									'ID' => $enum['UF_XML_ID'],
									'NAME' => $enum['UF_NAME']
								];
							}
						}
					}

				break;

				case 'E':

					$queryEnum = \CIBlockElement::GetList(
						[],
						[ 'ID' => $propertyValues ],
						false,
						false,
						[ 'ID', 'NAME' ]
					);

					while ($enum = $queryEnum->Fetch())
					{
						$enumList[] = $enum;
					}

				break;
			}

			// fill display value

			if (!empty($enumList))
			{
				foreach ($enumList as $enum)
				{
					$enumName = trim($enum['NAME']);

					if (isset($propertyValuesMap[$enum['ID']]))
					{
						foreach ($propertyValuesMap[$enum['ID']] as $elementId)
						{
							$elementProperty = &$propertyValuesList[$elementId][$property['CODE']];

							if (!isset($elementProperty['DISPLAY_VALUE']) || $elementProperty['DISPLAY_VALUE'] === '')
							{
								$elementProperty['DISPLAY_VALUE'] = $enumName;
							}
							else if ($enumName !== '')
							{
								if (!is_array($elementProperty['DISPLAY_VALUE']))
								{
									$elementProperty['DISPLAY_VALUE'] = (array)$elementProperty['DISPLAY_VALUE'];
								}

								$elementProperty['DISPLAY_VALUE'][] = $enumName;
							}

							unset($elementProperty);
						}
					}
				}
			}
		}
	}

	protected function getDisplayValue($property)
	{
		$result = null;

		if (isset($property['DISPLAY_VALUE']))
		{
			$result = $property['DISPLAY_VALUE'];
		}
		else if (!empty($property['VALUE']))
		{
			$propertyType = $property['USER_TYPE'] ?: $property['PROPERTY_TYPE'];

			switch ($propertyType)
			{
				case 'F':
					$fileIds = (array)$property['VALUE'];
					$result = [];

					foreach ($fileIds as $fileId)
					{
						$result[] = \CFile::GetPath($fileId);
					}
				break;

				default:
					$result = htmlspecialcharsback($property['VALUE']);
				break;
			}
		}

		return $result;
	}

	protected function getHighloadDataClass($property)
	{
		$result = false;
		$tableName = !empty($property['USER_TYPE_SETTINGS']['TABLE_NAME'])
			? $property['USER_TYPE_SETTINGS']['TABLE_NAME']
			: null;

		if ($tableName === null)
		{
			// nothing
		}
		else if (isset($this->highloadDataClassCache[$tableName]))
		{
			$result = $this->highloadDataClassCache[$tableName];
		}
		else if (Main\Loader::includeModule('highloadblock'))
		{
			$queryHighload = Highloadblock\HighloadBlockTable::getList([
				'filter' => ['=TABLE_NAME' => $tableName],
			]);

			if ($highload = $queryHighload->fetch())
			{
				$entity = Highloadblock\HighloadBlockTable::compileEntity($highload);
				$result = $entity->getDataClass();
			}

			$this->highloadDataClassCache[$tableName] = $result;
		}

		return $result;
	}
}
