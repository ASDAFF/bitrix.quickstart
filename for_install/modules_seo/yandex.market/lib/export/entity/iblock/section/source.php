<?php

namespace Yandex\Market\Export\Entity\Iblock\Section;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class Source extends Market\Export\Entity\Reference\Source
{
	protected $cacheSectionValues = [];
	protected $cacheSectionValuesSetupId = null;

	public function getLangPrefix()
	{
		return 'IBLOCK_SECTION_';
	}

	public function getQuerySelect($select)
	{
		return [
			'ELEMENT' => [ 'IBLOCK_SECTION_ID' ]
		];
	}

	public function getElementListValues($elementList, $parentList, $selectFields, $queryContext, $sourceValues)
	{
		$result = [];

		if (!empty($queryContext['IBLOCK_ID']))
		{
			$sectionToElementMap = [];

			foreach ($elementList as $elementId => $element)
			{
				$sectionId = null;
				$parent = isset($element['PARENT_ID']) ? $parentList[$element['PARENT_ID']] : $element;

				if (!empty($parent['IBLOCK_SECTION_ID']))
				{
					$sectionId = (int)$parent['IBLOCK_SECTION_ID'];

					if ($sectionId > 0)
					{
						if (!isset($sectionToElementMap[$sectionId]))
						{
							$sectionToElementMap[$sectionId] = [];
						}

						$sectionToElementMap[$sectionId][] = $elementId;
					}
				}
			}

			if (!empty($sectionToElementMap))
			{
				$sectionIds = array_keys($sectionToElementMap);
				$sectionValuesList = $this->getSectionValues($queryContext['IBLOCK_ID'], $sectionIds, $selectFields, $queryContext['SETUP_ID']);

				foreach ($sectionValuesList as $sectionId => $sectionValues)
				{
					if (isset($sectionToElementMap[$sectionId]))
					{
						foreach ($sectionToElementMap[$sectionId] as $elementId)
						{
							$result[$elementId] = $sectionValues;
						}
					}
				}
			}
		}

		return $result;
	}

	protected function getSectionValues($iblockId, $sectionIds, $selectFields, $setupId = null)
	{
		$result = [];
		$iblockId = (int)$iblockId;
		$sectionIds = (array)$sectionIds;

		// get from cache

		if ($setupId !== null)
		{
			$cacheSectionValuesList = $this->getCacheSectionValues($setupId);
			$newSectionIds = [];

			foreach ($sectionIds as $sectionId)
			{
				if (!isset($cacheSectionValuesList[$sectionId]))
				{
					$newSectionIds[] = $sectionId;
				}
				else if ($cacheSectionValuesList[$sectionId] === false)
				{
					// nothing
				}
				else
				{
					$result[$sectionId] = $cacheSectionValuesList[$sectionId];
				}
			}
		}
		else
		{
			$newSectionIds = $sectionIds;
		}

		// query from db

		if ($iblockId > 0 && !empty($newSectionIds) && Main\Loader::includeModule('iblock'))
		{
			$nextSections = $newSectionIds;
			$nextSectionExportMap = [];
			$sectionList = [];

			while (!empty($nextSections))
			{
				$filterSections = $nextSections;
				$sectionExportMap = $nextSectionExportMap;

				$nextSections = [];
				$nextSectionExportMap = [];

				$query = \CIBlockSection::GetList(
					[ 'LEFT_MARGIN' => 'ASC' ],
					[ 'IBLOCK_ID' => $iblockId, 'ID' => $filterSections, 'CHECK_PERMISSIONS' => 'N' ],
					false,
					array_merge(
						[ 'IBLOCK_ID', 'ID', 'IBLOCK_SECTION_ID' ],
						$selectFields
					)
				);

				while ($section = $query->Fetch())
				{
					$sectionList[$section['ID']] = $section;
					$hasAllFields = true;
					$exportIdList = isset($sectionExportMap[$section['ID']]) ? $sectionExportMap[$section['ID']] : [ $section['ID'] ];
					$lastParentId = null;

					foreach ($selectFields as $fieldName)
					{
						$isFoundValue = false;
						$fieldValue = null;
						$searchParentId = null;

						if (isset($section[$fieldName]) && $section[$fieldName] !== '')
						{
							$isFoundValue = true;
							$fieldValue = $section[$fieldName];
						}
						else
						{
							$searchParentId = (int)$section['IBLOCK_SECTION_ID'];

							while ($searchParentId > 0 && isset($sectionList[$searchParentId]))
							{
								$parentSection = $sectionList[$searchParentId];

								if (isset($parentSection[$fieldName]) && $parentSection[$fieldName] !== '')
								{
									$isFoundValue = true;
									$fieldValue = $parentSection[$fieldName];

									break;
								}

								$searchParentId = (int)$parentSection['IBLOCK_SECTION_ID'];
							}
						}

						if (!$isFoundValue)
						{
							$hasAllFields = false;
							$lastParentId = $searchParentId;
						}
						else if ($fieldValue !== null)
						{
							foreach ($exportIdList as $exportId)
							{
								if (!isset($result[$exportId][$fieldName]))
								{
									if (!isset($result[$exportId])) { $result[$exportId] = []; }

									$result[$exportId][$fieldName] = $fieldValue;
								}
							}
						}
					}

					if (!$hasAllFields && $lastParentId > 0)
					{
						if (!isset($nextSectionExportMap[$lastParentId]))
						{
							$nextSectionExportMap[$lastParentId] = $exportIdList;
						}
						else
						{
							foreach ($exportIdList as $exportId)
							{
								$nextSectionExportMap[$lastParentId][] = $exportId;
							}
						}

						$nextSections[] = $lastParentId;
					}
				}
			}

			if ($setupId !== null)
			{
				$this->setCacheSectionValues($setupId, $newSectionIds, $result);
			}
		}

		return $result;
	}

	protected function getCacheSectionValues($setupId)
	{
		if ($this->cacheSectionValuesSetupId === $setupId)
		{
			$result = $this->cacheSectionValues;
		}
		else
		{
			$result = [];
		}

		return $result;
	}

	protected function setCacheSectionValues($setupId, $sectionIds, $sectionValuesList)
	{
		if ($this->cacheSectionValuesSetupId !== $setupId)
		{
			$this->cacheSectionValuesSetupId = $setupId;
			$this->cacheSectionValues = [];
		}

		foreach ($sectionIds as $sectionId)
		{
			$this->cacheSectionValues[$sectionId] = (
				isset($sectionValuesList[$sectionId])
					? $sectionValuesList[$sectionId]
					: false
			);
		}

		if (count($this->cacheSectionValues) > 100)
		{
			$this->cacheSectionValues = array_slice($this->cacheSectionValues, -100, 100, true);
		}
	}

	public function getFields(array $context = [])
	{
		global $USER_FIELD_MANAGER;

		$result = $this->buildFieldsDescription([
			'NAME' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_STRING,
				'FILTERABLE' => false,
				'SELECTABLE' => true
			]
		]);

		if (!empty($context['IBLOCK_ID']))
		{
			$userFields = $USER_FIELD_MANAGER->GetUserFields('IBLOCK_' . $context['IBLOCK_ID'] . '_SECTION', 0, LANGUAGE_ID);

			foreach ($userFields as $userField)
			{
				$result[] = [
					'ID' => $userField['FIELD_NAME'],
					'VALUE' => $userField['EDIT_FORM_LABEL'] ?: $userField['LIST_COLUMN_LABEL'] ?: $userField['FIELD_NAME'],
					'TYPE' => Market\Export\Entity\Data::convertUserTypeToDataType($userField['USER_TYPE_ID']),
					'FILTERABLE' => false,
					'SELECTABLE' => true
				];
			}
		}

		return $result;
	}
}