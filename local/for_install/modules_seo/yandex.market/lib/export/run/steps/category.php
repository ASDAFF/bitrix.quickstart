<?php

namespace Yandex\Market\Export\Run\Steps;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class Category extends Base
{
	public function getName()
	{
		return 'category';
	}

	public function run($action, $offset = null)
	{
		$result = new Market\Result\Step();
		$context = $this->getContext();
		$sectionList = $this->getSectionList($context);
		$tagValuesList = $this->buildTagValuesList([], $sectionList);

		$this->setRunAction($action);
		$this->writeData($tagValuesList, $sectionList, $context);

		return $result;
	}

	public function getFormatTag(Market\Export\Xml\Format\Reference\Base $format)
	{
		return $format->getCategory();
	}

	public function getFormatTagParentName(Market\Export\Xml\Format\Reference\Base $format)
	{
		return $format->getCategoryParentName();
	}

	protected function getStorageDataClass()
	{
		return Market\Export\Run\Storage\CategoryTable::getClassName();
	}

	protected function getDataLogEntityType()
	{
		return Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_CATEGORY;
	}

	protected function getSectionList($context)
	{
		$usedFieldTypes = $this->getUsedSourceFieldTypes();
		$hasFewTypes = (count($usedFieldTypes) > 1);
		$result = [];

		foreach ($usedFieldTypes as $fieldType => $fieldConfig)
		{
			$usedSectionIds = $this->getUsedSectionIds($context, $hasFewTypes ? $fieldConfig['IBLOCK_LINK_ID'] : null);

			if (!empty($usedSectionIds))
			{
				$typeSectionList = null;

				if ($fieldConfig['CONFLICT'] !== null)
				{
					$usedSectionIds = $this->revertConflictForSectionIdList($usedSectionIds, $fieldConfig['CONFLICT']);
				}

				switch ($fieldType)
				{
					case Market\Export\Entity\Data::TYPE_SERVICE_CATEGORY:
						$typeSectionList = $this->getSectionListFromService($usedSectionIds, $context);
					break;

					default:
						$typeSectionList = $this->getSectionListFromIblock($usedSectionIds, $context);
					break;
				}

				if (!empty($typeSectionList))
				{
					if ($fieldConfig['CONFLICT'] !== null)
					{
						$typeSectionList = $this->applyConflictForSectionList($typeSectionList, $fieldConfig['CONFLICT']);
					}

					if (empty($result))
					{
						$result = $typeSectionList;
					}
					else
					{
						$result = array_merge($result, $typeSectionList);
					}
				}
			}
		}

		return $result;
	}

	protected function getSectionListFromService($usedSectionIds, $context)
	{
		$result = [];
		$usedSectionsMap = array_flip($usedSectionIds);
		$serviceCategoryList = Market\Service\Data\Category::getList();
		$currentTree = [];
		$currentTreeDepth = 0;

		foreach ($serviceCategoryList as $serviceCategoryKey => $serviceCategory)
		{
			if ($serviceCategory['depth'] < $currentTreeDepth)
			{
				array_splice($currentTree, $serviceCategory['depth']);
			}

			$currentTree[$serviceCategory['depth']] = $serviceCategoryKey;
			$currentTreeDepth = $serviceCategory['depth'];

			if (isset($usedSectionsMap[$serviceCategory['id']]))
			{
				foreach ($currentTree as $treeCategoryKey)
				{
					$treeCategory = $serviceCategoryList[$treeCategoryKey];

					if (!isset($result[$treeCategory['id']]))
					{
						$result[$treeCategory['id']] = [
							'ID' => $treeCategory['id'],
							'PARENT_ID' => $treeCategory['parentId'],
							'NAME' => Market\Service\Data\Category::getTitle($treeCategory['id']),
						];
					}
				}
			}
		}

		return $result;
	}

	protected function getSectionListFromIblock($usedSectionIds, $context)
	{
		$result = [];

		if (Main\Loader::includeModule('iblock'))
		{
			$usedSectionsMap = array_flip($usedSectionIds);

			// find used sections

			$querySections = \CIBlockSection::GetList(
				[],
				[ 'ID' => $usedSectionIds, 'CHECK_PERMISSIONS' => 'N' ],
				false,
				[ 'IBLOCK_ID', 'ID', 'IBLOCK_SECTION_ID', 'NAME', 'LEFT_MARGIN']
			);

			while ($section = $querySections->Fetch())
			{
				$sectionData = [
					'ID' => (int)$section['ID'],
					'NAME' => trim($section['NAME']),
					'LEFT_MARGIN' => (int)$section['LEFT_MARGIN']
				];
				$parentId = (int)$section['IBLOCK_SECTION_ID'];

				if ($parentId <= 0)
				{
					// hasn't parent
				}
				else if (isset($usedSectionsMap[$parentId])) // will selected
				{
					$sectionData['PARENT_ID'] = $parentId;
				}
				else if (isset($result[$parentId])) // already selected
				{
					$sectionData['PARENT_ID'] = $parentId;
				}
				else // get chain
				{
					$queryParents = \CIBlockSection::GetNavChain($section['IBLOCK_ID'], $section['ID'], [ 'ID', 'IBLOCK_SECTION_ID', 'NAME', 'LEFT_MARGIN' ]);

					while ($parent = $queryParents->Fetch())
					{
						$parentData = [
							'ID' => (int)$parent['ID'],
							'NAME' => trim($parent['NAME']),
							'LEFT_MARGIN' => (int)$parent['LEFT_MARGIN']
						];

						if ($parent['IBLOCK_SECTION_ID'] > 0)
						{
							$parentData['PARENT_ID'] = (int)$parent['IBLOCK_SECTION_ID'];
						}

						if ($parentData['ID'] !== $sectionData['ID'])
						{
							$result[$parentData['ID']] = $parentData;
						}
						else if (isset($parentData['PARENT_ID']))
						{
							$sectionData['PARENT_ID'] = $parentData['PARENT_ID'];
						}
					}
				}

				$result[$section['ID']] = $sectionData;
			}

			uasort($result, function($a, $b) {
				if ($a['LEFT_MARGIN'] === $b['LEFT_MARGIN']) { return 0; }

				return ($a['LEFT_MARGIN'] < $b['LEFT_MARGIN'] ? -1 : 1);
			});
		}

		return $result;
	}

	protected function getUsedSectionIds($context, $iblockLinkIdList = null)
	{
		$result = [];
		$queryFilter = [
			'=SETUP_ID' => $context['SETUP_ID'],
			'=STATUS' => static::STORAGE_STATUS_SUCCESS
		];

		if ($iblockLinkIdList !== null)
		{
			$queryFilter['=IBLOCK_LINK_ID'] = $iblockLinkIdList;
		}

		$query = Market\Export\Run\Storage\OfferTable::getList([
			'group' => [ 'CATEGORY_ID' ],
			'select' => [ 'CATEGORY_ID' ],
			'filter' => $queryFilter
		]);

		while ($row = $query->fetch())
		{
			$categoryId = (int)$row['CATEGORY_ID'];

			if ($categoryId > 0)
			{
				$result[] = $categoryId;
			}
		}

		return $result;
	}

	protected function revertConflictForSectionIdList($sectionIdList, $conflict)
	{
		$result = $sectionIdList;

		foreach ($result as &$sectionId)
		{
			switch ($conflict['TYPE'])
			{
				case 'INCREMENT':
					$sectionId = (int)($sectionId - $conflict['VALUE']);
				break;
			}
		}
		unset($sectionId);

		return $result;
	}

	protected function applyConflictForSectionList($sectionList, $conflict)
	{
		$result = [];

		foreach ($sectionList as $sectionId => $sectionData)
		{
			$newSectionId = $sectionId;
			$newSectionData = $sectionData;

			switch ($conflict['TYPE'])
			{
				case 'INCREMENT':
					$newSectionId += $conflict['VALUE'];
					$newSectionData['ID'] += $conflict['VALUE'];

					if (isset($newSectionData['PARENT_ID']))
					{
						$newSectionData['PARENT_ID'] += $conflict['VALUE'];
					}
				break;
			}

			$result[$newSectionId] = $newSectionData;
		}

		return $result;
	}

	protected function buildTagValues($dummy, $section)
	{
		$result = new Market\Result\XmlValue();

		$attributes = [
			'id' => $section['ID']
		];

		if (isset($section['PARENT_ID']))
		{
			$attributes['parentId'] = $section['PARENT_ID'];
		}

		$result->addTag('category', $section['NAME'], $attributes);

		return $result;
	}

	protected function getUsedSourceFieldTypes()
	{
		$setup = $this->getSetup();
		$iblockLinkCollection = $setup->getIblockLinkCollection();
		$conflictList = $this->getProcessor()->getConflicts();
		$result = [];

		/** @var \Yandex\Market\Export\IblockLink\Model $iblockLink */
		foreach ($iblockLinkCollection as $iblockLink)
		{
			$sourceMap = $this->getOfferTagSource($iblockLink, 'categoryId');
			$fieldType = null;

			if ($sourceMap === null)
			{
				throw new Main\SystemException(
					Market\Config::getLang('EXPORT_RUN_STEP_CATEGORY_NOT_FOUND_SOURCE_FOR_TAG')
				);
			}

			$iblockContext = $iblockLink->getContext();
			$source = Market\Export\Entity\Manager::getSource($sourceMap['TYPE']);

			if ($source->isVariable())
			{
				throw new Main\SystemException(
					Market\Config::getLang('EXPORT_RUN_STEP_CATEGORY_NO_SUPPORT_FOR_VARIABLE_SOURCE')
				);
			}

			$sourceFields = $source->getFields($iblockContext);

			foreach ($sourceFields as $sourceField)
			{
				if ($sourceField['ID'] === $sourceMap['FIELD'])
				{
					$fieldType = $sourceField['TYPE'];
					break;
				}
			}

			if ($fieldType === null)
			{
				throw new Main\SystemException(
					Market\Config::getLang('EXPORT_RUN_STEP_CATEGORY_NOT_FOUND_SOURCE_FIELD')
				);
			}

			if (!isset($result[$fieldType]))
			{
				$result[$fieldType] = [
					'IBLOCK_LINK_ID' => [],
					'CONFLICT' => (
						isset($conflictList[$sourceMap['TYPE']][$sourceMap['FIELD']])
							? $conflictList[$sourceMap['TYPE']][$sourceMap['FIELD']]
							: null
					)
				];
			}

			$result[$fieldType]['IBLOCK_LINK_ID'][] = $iblockLink->getId();
		}

		return $result;
	}

	protected function getOfferTagSource(Market\Export\IblockLink\Model $iblockLink, $tagName)
	{
		$result = null;
		$tagDescriptionList = $iblockLink->getTagDescriptionList();

		foreach ($tagDescriptionList as $tagDescription)
		{
			if ($tagDescription['TAG'] === $tagName)
			{
				$result = $tagDescription['VALUE'];
				break;
			}
		}

		return $result;
	}
}