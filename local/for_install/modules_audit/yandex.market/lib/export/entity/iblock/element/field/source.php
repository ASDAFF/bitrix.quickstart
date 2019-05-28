<?php

namespace Yandex\Market\Export\Entity\Iblock\Element\Field;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class Source extends Market\Export\Entity\Reference\Source
{
	public function getQuerySelect($select)
	{
		return [
			'ELEMENT' => $select
		];
	}

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
		return 100;
	}

	public function getElementListValues($elementList, $parentList, $select, $queryContext, $sourceValues)
	{
		$result = [];

		foreach ($elementList as $elementId => $element)
		{
			$parent = null;

			if (!isset($element['PARENT_ID'])) // is not offer
			{
				$parent = $element;
			}
			else if (isset($parentList[$element['PARENT_ID']])) // has parent element
			{
				$parent = $parentList[$element['PARENT_ID']];
			}

			if (isset($parent))
			{
				$result[$elementId] = $this->getFieldValues($parent, $select);
			}
		}

		return $result;
	}

	public function getFields(array $context = [])
	{
		return $this->buildFieldsDescription([
			'ID' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_NUMBER
			],
			'NAME' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_STRING
			],
			'IBLOCK_SECTION_ID' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_IBLOCK_SECTION
			],
			'CODE'=> [
				'TYPE' => Market\Export\Entity\Data::TYPE_STRING
			],
			'PREVIEW_PICTURE' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_FILE
			],
			'PREVIEW_TEXT' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_STRING
			],
			'DETAIL_PICTURE' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_FILE
			],
			'DETAIL_TEXT' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_STRING
			],
			'DETAIL_PAGE_URL' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_URL
			],
			'DATE_CREATE' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_DATE
			],
			'TIMESTAMP_X' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_DATE
			],
			'XML_ID' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_STRING
			]
		]);
	}

	public function getFieldEnum($field, array $context = [])
	{
		$result = null;

		switch ($field['ID'])
		{
			case 'IBLOCK_SECTION_ID':

				if (isset($context['IBLOCK_ID']) && Main\Loader::includeModule('iblock'))
				{
					$result = [];

					$queryEnum = \CIBlockSection::getList(
						[
							'LEFT_MARGIN' => 'ASC'
						],
						[
							'IBLOCK_ID' => $context['IBLOCK_ID'],
							'ACTIVE' => 'Y',
							'CHECK_PERMISSIONS' => 'N'
						],
						false,
						[
							'ID',
							'NAME',
							'DEPTH_LEVEL'
						]
					);

					while ($enum = $queryEnum->fetch())
					{
						$result[] = [
							'ID' => $enum['ID'],
							'VALUE' => str_repeat('.', $enum['DEPTH_LEVEL'] - 1) . $enum['NAME']
						];
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

	protected function buildQueryFilter($filter)
	{
		$result = [];

		foreach ($filter as $filterItem)
		{
			$compare = $filterItem['COMPARE'];
			$field = $filterItem['FIELD'];
			$value = $filterItem['VALUE'];

			if ($field === 'IBLOCK_SECTION_ID' && ($compare === '=' || $compare === ''))
			{
				$field = 'SECTION_ID';
				$compare = '';

				if (!empty($value))
				{
					$result['INCLUDE_SUBSECTIONS'] = 'Y';
				}
			}

			$result[$compare . $field] = $value;
        }

        return $result;
	}

	protected function getFieldValues($element, $select)
	{
		$result = [];

		foreach ($select as $fieldName)
		{
			$fieldValue = null;
			$fieldNameTilda = '~' . $fieldName;

			if (isset($element[$fieldNameTilda]))
			{
				$fieldValue = $element[$fieldNameTilda];
			}
			else if (isset($element[$fieldName]))
			{
				$fieldValue = $element[$fieldName];
			}

			if ($fieldValue !== null)
			{
				switch ($fieldName)
				{
					case 'PREVIEW_PICTURE':
					case 'DETAIL_PICTURE':
						$fieldValue = \CFile::GetPath($fieldValue);
					break;
				}
			}

			$result[$fieldName] = $fieldValue;
		}

		return $result;
	}

	protected function getLangPrefix()
	{
		return 'IBLOCK_ELEMENT_FIELD_';
	}
}
