<?php

namespace Yandex\Market\Export\Run\Steps;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class Offer extends Base
{
	const ELEMENT_TYPE_PRODUCT = 1;
	const ELEMENT_TYPE_SET = 2;
	const ELEMENT_TYPE_SKU = 3;
	const ELEMENT_TYPE_OFFER = 4;
	const ELEMENT_TYPE_FREE_OFFER = 5;
	const ELEMENT_TYPE_EMPTY_SKU = 6;

	protected $queryExcludeFilter;
	protected $isCatalogTypeCompatibility;

	public function getName()
	{
		return 'offer';
	}

	public function getReadyCount()
	{
		$dataClass = $this->getStorageDataClass();
		$context = $this->getContext();
		$readyFilter = $this->getStorageReadyFilter($context);
		$result = 0;

		$query = $dataClass::getList([
			'filter' => $readyFilter,
			'select' => [ 'CNT' ],
			'runtime' => [
				new Main\Entity\ExpressionField('CNT', 'COUNT(1)')
			]
		]);

		if ($row = $query->fetch())
		{
			$result = (int)$row['CNT'];
		}

		return $result;
	}

	public function getTotalCount($isDisableCalculation = false)
	{
		if ($this->totalCount === null && !$isDisableCalculation)
		{
			$this->totalCount = 0;

			/** @var \Yandex\Market\Export\IblockLink\Model $iblockLink */
			$setup = $this->getSetup();
			$iblockLinkIndex = 0;

			foreach ($setup->getIblockLinkCollection() as $iblockLink)
			{
				if ($iblockLink->isExportAll())
				{
					$iblockContext = $iblockLink->getContext();
					$queryFilter = $this->makeQueryFilter([], [], $iblockContext);

					$this->totalCount += $this->queryTotalCount($queryFilter, $iblockContext);
				}
				else
				{
					$filterCountList = $this->getCount($iblockLinkIndex, false);

					$this->totalCount += $filterCountList->getSum();
				}

				$iblockLinkIndex++;
			}
		}

		return $this->totalCount;
	}

	public function getCount($offset = null, $isNeedAll = null)
	{
		$setup = $this->getSetup();
		$result = new Market\Result\StepCount();
		$offsetIblockLinkIndex = null;
		$offsetFilterIndex = null;

		if (isset($offset))
		{
			$offsetParts = explode(':', $offset);
			$offsetIblockLinkIndex = (int)$offsetParts[0];
			$offsetFilterIndex = isset($offsetParts[1]) ? (int)$offsetParts[1] : null;
		}

		$iblockLinkIndex = 0;

		foreach ($setup->getIblockLinkCollection() as $iblockLink)
		{
			if ($offsetIblockLinkIndex !== null && $offsetIblockLinkIndex !== $iblockLinkIndex) // is iblock out of offset
			{
				$iblockLinkIndex++;
			    continue;
		    }

			$iblockContext = $iblockLink->getContext();
			$iblockLinkId = $iblockLink->getInternalId();

			$sourceFilterList = $this->getSourceFilterList($iblockLink, $iblockContext, $isNeedAll);
			$sourceFilterIndex = 0;
			$excludeList = [];

			foreach ($sourceFilterList as $sourceFilter)
			{
				if ($offsetFilterIndex === null || $offsetFilterIndex >= $sourceFilterIndex) // is filter in offset or no offset
				{
					$queryFilter = $this->makeQueryFilter($sourceFilter['FILTER'], [], $sourceFilter['CONTEXT']);
					$filterCount = 0;
					$isIblockLinkFilter = ($sourceFilter['ID'] === null);

					if ($isIblockLinkFilter)
					{
						$totalCount = $this->queryTotalCount($queryFilter, $sourceFilter['CONTEXT']);
						$filterCount = $totalCount - count($excludeList);

						if ($this->isCatalogTypeCompatibility($sourceFilter['CONTEXT']))
						{
							$result->addCountWarning($iblockLinkId, new Market\Error\Base(
								Market\Config::getLang('EXPORT_RUN_STEP_OFFER_COUNT_CATALOG_TYPE_COMPATIBILITY')
							));
						}
					}
					else if (!empty($sourceFilter['FILTER']))
					{
						$filterIdList = $this->queryCount($queryFilter, $sourceFilter['CONTEXT'], $excludeList);

						$filterCount = count($filterIdList);
						$excludeList += $filterIdList;
					}

					if ($isIblockLinkFilter) // is iblock link
					{
						$result->setCount($iblockLinkId, $filterCount);
					}
					else
					{
						$result->setCount($iblockLinkId . ':' . $sourceFilter['ID'], $filterCount);
					}
				}

				$sourceFilterIndex++;
			}

			$iblockLinkIndex++;
		}

		return $result;
	}

	/**
	 * Запускаем выгрузку
	 *
	 * @param string $action
	 * @param string|null $offset
	 *
	 * @return Market\Result\Step
	 *
	 * @throws \Bitrix\Main\LoaderException
	 * @throws \Bitrix\Main\ObjectNotFoundException
	 * @throws \Bitrix\Main\SystemException
	 */
	public function run($action, $offset = null)
	{
		/** @var \Yandex\Market\Export\IblockLink\Model $iblockLink */

		$result = new Market\Result\Step();
		$setup = $this->getSetup();
		$iblockLinkCollection = $setup->getIblockLinkCollection();
		$formatTag = $this->getTag();

		$this->setRunAction($action);

		// calculate offset and total

		$offsetIblockLinkIndex = null;
		$offsetFilterIndex = null;
		$offsetFilterShift = null;
		$totalFilterCount = 0;
		$iblockLinkWeightList = [];

		if ($offset !== null)
		{
			$offsetParts = explode(':', $offset);
			$offsetIblockLinkIndex = (int)$offsetParts[0];
			$offsetFilterIndex = isset($offsetParts[1]) ? (int)$offsetParts[1] : null;
			$offsetFilterShift = isset($offsetParts[2]) ? (int)$offsetParts[2] : null;
		}

		foreach ($iblockLinkCollection as $iblockLink)
		{
			$iblockLinkWeight = count($iblockLink->getFilterCollection());

			if ($iblockLink->isExportAll())
			{
				++$iblockLinkWeight;
			}

			$iblockLinkWeightList[] = $iblockLinkWeight;
			$totalFilterCount += $iblockLinkWeight;
		}

		$result->setTotal($totalFilterCount);

		// progress total count

		if ($this->getParameter('progressCount') === true)
		{
			$totalCount = $this->getTotalCount($offset !== null);

			$result->setTotalCount($totalCount);
		}

		// run export

		$iblockLinkIndex = 0;
		$isTimeExpired = false;

		foreach ($iblockLinkCollection as $iblockLink)
		{
			if ($offsetIblockLinkIndex !== null && $offsetIblockLinkIndex > $iblockLinkIndex) // is iblock out of offset
			{
				$result->increaseProgress($iblockLinkWeightList[$iblockLinkIndex]);
				$iblockLinkIndex++;
			    continue;
		    }

			$iblockContext = $iblockLink->getContext();
			$tagDescriptionList = $iblockLink->getTagDescriptionList();

			$formatTag->extendTagDescriptionList($tagDescriptionList, $iblockContext);

			$sourceSelect = $this->getSourceSelect($tagDescriptionList);

			$this->initializeQueryContext($iblockContext, $sourceSelect);
			$this->sortSourceSelect($sourceSelect);
			$this->applySelectMap($tagDescriptionList, $iblockContext);

			$querySelect = $this->makeQuerySelect($sourceSelect, $iblockContext);
			$sourceFilterList = $this->getSourceFilterList($iblockLink, $iblockContext);
			$sourceFilterIndex = 0;
			$changesFilter = null;

			if ($action === 'change')
			{
				$changes = $this->getParameter('changes');
				$changesFilter = $this->getQueryChangesFilter($changes, $iblockContext);
			}

			foreach ($sourceFilterList as $sourceFilter)
			{
				$queryFilter = $this->makeQueryFilter($sourceFilter['FILTER'], $sourceSelect, $sourceFilter['CONTEXT'], $changesFilter);

				if ($offsetFilterIndex === null || $sourceFilterIndex >= $offsetFilterIndex) // is not filter out of offset
				{
					$queryOffset = ($offsetFilterShift !== null ? $offsetFilterShift : 0);

					do
					{
						$queryResult = $this->queryElementList($queryFilter, $querySelect, $sourceFilter['CONTEXT'], $queryOffset);
						$filterProgress = $queryResult['HAS_NEXT'] ? 0 : 1;
						$queryOffset = (int)$queryResult['OFFSET'];

						foreach (array_chunk($queryResult['ELEMENT'], 500, true) as $elementChunk)
						{
							$sourceValueList = $this->extractElementListValues($sourceSelect, $elementChunk, $queryResult['PARENT'], $sourceFilter['CONTEXT']);
							$tagValuesList = $this->buildTagValuesList($tagDescriptionList, $sourceValueList);

							$this->writeData($tagValuesList, $elementChunk, $sourceFilter['CONTEXT'], [
								'PARENT_LIST' => $queryResult['PARENT']
		                    ]);
	                    }

	                    $result->setOffset($iblockLinkIndex . ':' . $sourceFilterIndex . ':' . $queryOffset);

	                    if ($this->getProcessor()->isTimeExpired())
	                    {
	                        $isTimeExpired = true;
	                        break;
	                    }
					}
					while ($queryResult['HAS_NEXT']);

					$result->increaseProgress($filterProgress);

					$offsetFilterShift = null; // reset page offset for next filter
				}
				else
				{
					$result->increaseProgress(1);
				}

				if ($isTimeExpired) { break; }

				$sourceFilterIndex++;
			}

			$this->releaseQueryContext($iblockContext, $sourceSelect);

			if ($isTimeExpired) { break; }

			$offsetFilterIndex = null; // reset filter offset for next iblock link
			$offsetFilterShift = null; // reset page offset for next iblock link

			$iblockLinkIndex++;
		}

		if ($this->getParameter('progressCount') === true && $this->getTotalCount(true) > 0)
		{
			$readyCount = $this->getReadyCount();

			$result->setReadyCount($readyCount);
		}

		return $result;
	}

	public function getFormatTag(Market\Export\Xml\Format\Reference\Base $format)
	{
		return $format->getOffer();
	}

	public function getFormatTagParentName(Market\Export\Xml\Format\Reference\Base $format)
	{
		return $format->getOfferParentName();
	}

	protected function useHashCollision()
	{
		return true;
	}

	protected function getStorageDataClass()
	{
		return Market\Export\Run\Storage\OfferTable::getClassName();
	}

	protected function getStorageChangesFilter($changes, $context)
	{
		$isNeedFull = false;
		$result = [];

		if (!empty($changes))
		{
			foreach ($changes as $changeType => $entityIds)
			{
				switch ($changeType)
				{
					case Market\Export\Run\Manager::ENTITY_TYPE_OFFER:

						$dataClass = $this->getStorageDataClass();
						$elementFilter = [];
						$parentFilter = [];

						$query = $dataClass::getList([
							'filter' => [
								'=SETUP_ID' => $context['SETUP_ID'],
								[
									'LOGIC' => 'OR',
									[ '=ELEMENT_ID' => $entityIds ],
									[ '=PARENT_ID' => $entityIds ]
								]

							],
							'select' => [
								'ELEMENT_ID',
								'PARENT_ID'
							]
						]);

						while ($row = $query->fetch())
						{
							$parentId = (int)$row['PARENT_ID'];

							if ($parentId > 0)
							{
								$parentFilter[$parentId] = true;
							}
							else
							{
								$elementFilter[] = (int)$row['ELEMENT_ID'];
							}
						}

						$hasParentFilter = !empty($parentFilter);
						$hasElementFilter = !empty($elementFilter);

						if ($hasParentFilter || $hasElementFilter)
						{
							if ($hasParentFilter)
							{
								$result[] = [
									'=PARENT_ID' => array_keys($parentFilter)
								];
							}

							if ($hasElementFilter)
							{
								$result[] = [
									'=ELEMENT_ID' => $elementFilter
								];
							}
						}
						else
						{
							$result[] = [
								'=ELEMENT_ID' => -1
							];
						}
					break;

					case Market\Export\Run\Manager::ENTITY_TYPE_CATEGORY:
						$result[] = [
							'=CATEGORY_ID' => $entityIds
						];
					break;

					case Market\Export\Run\Manager::ENTITY_TYPE_CURRENCY:
						$result[] = [
							'=CURRENCY_ID' => $entityIds
						];
					break;

					default:
						$isNeedFull = true;
					break;
				}

				if ($isNeedFull)
				{
					break;
				}
			}
		}

		if ($isNeedFull)
		{
			$result = [];
		}
		else if (count($result) > 1)
		{
			$result['LOGIC'] = 'OR';
		}

		return $result;
	}

	protected function getStorageAdditionalData($tagResult, $tagValues, $element, $context)
	{
		$categoryId = $tagValues->getTagValue('categoryId') ?: '';
		$currencyId = $tagValues->getTagValue('currencyId') ?: '';

		return [
			'PARENT_ID' => isset($element['PARENT_ID']) ? $element['PARENT_ID'] : '',
			'IBLOCK_LINK_ID' => isset($context['IBLOCK_LINK_ID']) ? $context['IBLOCK_LINK_ID'] : '',
			'FILTER_ID' => isset($context['FILTER_ID']) ? $context['FILTER_ID'] : '',
			'CATEGORY_ID' => $categoryId,
			'CURRENCY_ID' => $currencyId
		];
	}

	protected function getDataLogEntityType()
	{
		return Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_OFFER;
	}

	protected function isAllowPublicDelete()
	{
		return true;
	}

	protected function initializeQueryContext(&$iblockContext, &$sourceSelect)
	{
		foreach ($sourceSelect as $sourceType => $sourceFields)
		{
			$source = $this->getSource($sourceType);

			$source->initializeQueryContext($sourceFields, $iblockContext, $sourceSelect);
		}
	}

	protected function applySelectMap(&$tagDescriptionList, $iblockContext)
	{
		if (!empty($iblockContext['SELECT_MAP']))
		{
			$selectMap = $iblockContext['SELECT_MAP'];

			foreach ($tagDescriptionList as &$tagDescription)
			{
				if (isset($tagDescription['VALUE']))
				{
					$valueSourceMap = $tagDescription['VALUE'];

					if (isset($selectMap[$valueSourceMap['TYPE']][$valueSourceMap['FIELD']]))
					{
						$tagDescription['VALUE']['FIELD'] = $selectMap[$valueSourceMap['TYPE']][$valueSourceMap['FIELD']];
					}
				}

				if (isset($tagDescription['ATTRIBUTES']))
				{
					foreach ($tagDescription['ATTRIBUTES'] as &$attributeSourceMap)
					{
						if (isset($selectMap[$attributeSourceMap['TYPE']][$attributeSourceMap['FIELD']]))
						{
							$attributeSourceMap['FIELD'] = $selectMap[$attributeSourceMap['TYPE']][$attributeSourceMap['FIELD']];
						}
					}
					unset($attributeSourceMap);
				}
			}
			unset($tagDescription);
		}
	}

	protected function releaseQueryContext($iblockContext, $sourceSelect)
	{
		foreach ($sourceSelect as $sourceType => $sourceFields)
		{
			$source = $this->getSource($sourceType);

			$source->releaseQueryContext($sourceFields, $iblockContext, $sourceSelect);
		}
	}

	protected function getQueryChangesFilter($changes, $context)
	{
		$changesFilter = [];
		$isNeedFull = false;

		foreach ($changes as $changeType => $entityIds)
		{
			$entityType = null;
			$entityFilter = null;

			switch ($changeType)
			{
				case Market\Export\Run\Manager::ENTITY_TYPE_OFFER:

					if (!isset($context['OFFER_IBLOCK_ID']))
					{
						$entityType = 'ELEMENT';
						$entityFilter = [
							'ID' => $entityIds
						];
					}
					else
					{
						// no support for only one offer change

						$elementIdsMap = array_flip($entityIds);

						$queryOffers = \CIBlockElement::GetList(
							array(),
							array(
								'IBLOCK_ID' => $context['OFFER_IBLOCK_ID'],
								'ID' => $entityIds
							),
							false,
							false,
							array(
								'IBLOCK_ID',
								'ID',
								'PROPERTY_' . $context['OFFER_PROPERTY_ID']
							)
						);

						while ($offer = $queryOffers->Fetch())
						{
							$offerId = (int)$offer['ID'];
							$offerElementId = (int)$offer['PROPERTY_' . $context['OFFER_PROPERTY_ID'] . '_VALUE'];

							if ($offerElementId > 0 && !isset($elementIdsMap[$offerElementId]))
							{
								$elementIdsMap[$offerElementId] = true;
							}

							if (isset($elementIdsMap[$offerId]))
							{
								unset($elementIdsMap[$offerId]);
							}
						}

						$entityType = 'ELEMENT';
						$entityFilter = [
							'ID' => !empty($elementIdsMap) ? array_keys($elementIdsMap) : -1
						];
					}

				break;

				case Market\Export\Run\Manager::ENTITY_TYPE_CATEGORY:
					$entityType = 'ELEMENT';
					$entityFilter = [
						'SECTION_ID' => $entityIds,
						'INCLUDE_SUBSECTIONS' => 'Y'
					];
				break;

				default: // unsupported change, need full refresh
					$isNeedFull = true;
				break;
			}

			if ($isNeedFull)
			{
				$changesFilter = [];
				break;
			}
			else if (isset($entityType) && isset($entityFilter))
			{
				if (!isset($changesFilter[$entityType]))
				{
					$changesFilter[$entityType] = [];
				}
				else if (count($changesFilter[$entityType]) === 1)
				{
					$changesFilter[$entityType]['LOGIC'] = 'OR';
				}

				$changesFilter[$entityType][] = $entityFilter;
			}
		}

		return $changesFilter;
	}

	protected function queryCount($queryFilter, $queryContext, $excludeList)
	{
		$result = [];
		$hasOffers = isset($queryContext['OFFER_PROPERTY_ID']);
		$isCatalogTypeCompatibility = ($hasOffers && $this->isCatalogTypeCompatibility($queryContext));
		$pageIndex = 1;
		$pageSize = (int)($this->getParameter('offerPageSize') ?: Market\Config::getOption('export_count_offer_page_size') ?: 100);

		do
		{
			$pageElementCount = 0;
			$parentList = [];
			$foundParents = [];

			$elementSelect = $this->getSelectDefaults('ELEMENT', $queryContext);

			$queryElementList = $this->execIblockElementQueryWithOffset(
				$elementSelect,
				$queryFilter['ELEMENT'],
				$pageSize,
				($pageIndex - 1) * $pageSize
			);

			while ($element = $queryElementList->Fetch())
			{
				if ($isCatalogTypeCompatibility)
				{
					$parentList[$element['ID']] = true;
					$result[$element['ID']] = true;
				}
				else if ($hasOffers && $this->getElementCatalogType($element, $queryContext) === static::ELEMENT_TYPE_SKU)
				{
					$parentList[$element['ID']] = true;
				}
				else if (!isset($excludeList[$element['ID']]))
				{
					$result[$element['ID']] = true;
				}

				$pageElementCount++;
			}

			if (!empty($parentList) && $hasOffers) // has parents by catalog_type
			{
				$skuPropertyKey = 'PROPERTY_' . $queryContext['OFFER_PROPERTY_ID'];
				$skuPropertyValueKey = $skuPropertyKey . '_VALUE';

				$offerSelect = $this->getSelectDefaults('OFFERS', $queryContext);
				$offerSelect[] = $skuPropertyKey;

				$offerFilter = $queryFilter['OFFERS'];
				$offerFilter['=' . $skuPropertyKey] = array_keys($parentList);

				$queryOffers = \CIBlockElement::GetList(
					[],
					$offerFilter,
					false,
					false,
					$offerSelect
				);

				while ($offer = $queryOffers->Fetch())
				{
					$offerElementId = (int)$offer[$skuPropertyValueKey];

					if (isset($parentList[$offerElementId]))
					{
						$foundParents[$offerElementId] = true;

						if (!isset($excludeList[$offer['ID']]))
						{
							$result[$offer['ID']] = true;
						}
					}
				}
			}

			if ($isCatalogTypeCompatibility)
			{
				foreach ($foundParents as $parentId => $dummy)
				{
					unset($result[$parentId]);
				}
			}

			$pageIndex++;
		}
		while ($pageSize <= $pageElementCount); // has next (iblock DISTINCT)

		return $result;
	}

	protected function queryTotalCount($queryFilter, $queryContext)
	{
		$hasOffers = isset($queryContext['OFFER_PROPERTY_ID']);
		$isOnlyOffers = !empty($queryContext['OFFER_ONLY']);
		$result = 0;

		// element count

		if (!$isOnlyOffers)
		{
			$elementFilter = $queryFilter['ELEMENT'];

			if ($hasOffers)
			{
				$elementFilter['!CATALOG_TYPE'] = static::ELEMENT_TYPE_SKU;
			}

			$result += (int)\CIBlockElement::GetList([], $elementFilter, []);
		}

		// offers count

		if ($hasOffers)
		{
			$result += (int)\CIBlockElement::GetList([], $queryFilter['OFFERS'], []);
		}

		return $result;
	}

	/**
	 * Запрашиваем элементы из базы данных
	 *
	 * @param $queryFilter
	 * @param $querySelect
	 * @param $queryContext
	 * @param $offset
	 *
	 * @return array
	 */
	protected function queryElementList($queryFilter, $querySelect, $queryContext, $offset = 0)
	{
		$pageSize = (int)($this->getParameter('offerPageSize') ?: Market\Config::getOption('export_run_offer_page_size') ?: 20);
		$pageElementCount = 0;
		$elementList = [];
		$parentList = [];
		$foundParents = [];
		$hasOffers = isset($queryContext['OFFER_PROPERTY_ID']);
		$isCatalogTypeCompatibility = ($hasOffers && $this->isCatalogTypeCompatibility($queryContext));

		$elementFilter = $queryFilter['ELEMENT'];
		$elementFilter[] = [
			'!ID' => $this->getQueryExcludeFilter($queryContext)
		];

		$queryElementList = $this->execIblockElementQueryWithOffset(
			$querySelect['ELEMENT'],
			$elementFilter,
			$pageSize,
			$offset
		);

		while ($element = $queryElementList->GetNext())
		{
			if ($isCatalogTypeCompatibility)
			{
				$parentList[$element['ID']] = $element;
				$elementList[$element['ID']] = $element;
			}
			else if ($hasOffers && $this->getElementCatalogType($element, $queryContext) === static::ELEMENT_TYPE_SKU)
			{
				$parentList[$element['ID']] = $element;
				$offset++;
			}
			else
			{
				$elementList[$element['ID']] = $element;
			}

			$pageElementCount++;
		}

		if ($hasOffers && !empty($parentList))
		{
			$offerList = [];

			$skuPropertyKey = 'PROPERTY_' . $queryContext['OFFER_PROPERTY_ID'];
			$skuPropertyValueKey = $skuPropertyKey . '_VALUE';

			$offerSelect = $querySelect['OFFERS'];
			$offerSelect[] = $skuPropertyKey;

			$offerFilter = $queryFilter['OFFERS'];
			$offerFilter['=' . $skuPropertyKey] = array_keys($parentList);

			if (!$isCatalogTypeCompatibility)
			{
				$offerFilter[] = [
					'!ID' => $this->getQueryExcludeFilter($queryContext)
				];
			}

			$queryOfferList = \CIBlockElement::GetList(
				array(),
				$offerFilter,
				false,
				false,
				$offerSelect
			);

			while ($offer = $queryOfferList->GetNext())
			{
				$offerElementId = (int)$offer[$skuPropertyValueKey];

				if (isset($parentList[$offerElementId]))
				{
					$foundParents[$offerElementId] = true;
					$offer['PARENT_ID'] = $offerElementId;

					$offerList[$offer['ID']] = $offer;
				}
			}

			if ($isCatalogTypeCompatibility && !empty($offerList))
			{
				$storageDataClass = $this->getStorageDataClass();
				$storageReadyFilter = $this->getStorageReadyFilter($queryContext);
				$offerIds = array_keys($offerList);

				foreach (array_chunk($offerIds, 500) as $offerIdsChunk)
				{
					$storageReadyFilter['@ELEMENT_ID'] = $offerIdsChunk;

					$queryReadyOffers = $storageDataClass::getList([
						'filter' => $storageReadyFilter,
						'select' => [ 'ELEMENT_ID' ]
					]);

					while ($readyOffer = $queryReadyOffers->fetch())
					{
						if (isset($offerList[$readyOffer['ELEMENT_ID']]))
						{
							unset($offerList[$readyOffer['ELEMENT_ID']]);
						}
					}
				}
			}

			$elementList += $offerList;
		}

		// release parents without offers

		foreach ($parentList as $parentId => $parent)
		{
			if (!isset($foundParents[$parentId]))
			{
				unset($parentList[$parentId]);
			}
			else if ($isCatalogTypeCompatibility)
			{
				if (isset($elementList[$parentId]))
				{
					unset($elementList[$parentId]);
				}

				$offset++;
			}
		}

		return [
			'ELEMENT' => $elementList,
			'PARENT' => $parentList,
			'OFFSET' => $offset,
			'HAS_NEXT' => ($pageElementCount >= $pageSize) // iblock distinct
		];
	}

	/**
	 * @param $select
	 * @param $filter
	 * @param $limit
	 * @param $offset
	 *
	 * @return \CIBlockResult
	 */
	protected function execIblockElementQueryWithOffset($select, $filter, $limit, $offset)
	{
		global $DB;

		// get iblock query builder version

		static $iblockQueryVersion = null;

		if ($iblockQueryVersion === null)
		{
			if (method_exists('\CIBlockElement', 'prepareSql'))
			{
				$iblockQueryVersion = 'prepareSql';
			}
			else
			{
				$iblockQueryVersion = 'subQuery';
			}
		}

		// build sql

		$catalogSelect = [];
		$catalogSelectSql = '';

		foreach ($select as $field)
		{
			if (strpos($field, 'CATALOG_') === 0)
			{
				$catalogSelect[] = $field;
			}
		}

		if (!empty($catalogSelect) && Main\Loader::includeModule('catalog'))
		{
			$catalogSelectResult = \CCatalogProduct::GetQueryBuildArrays([], [], $catalogSelect);

			if (isset($catalogSelectResult['SELECT']))
			{
				$catalogSelectSql = $catalogSelectResult['SELECT'];
			}
		}

		switch ($iblockQueryVersion)
		{
			case 'prepareSql':
				$queryProvider = new \CIBlockElement();
				$queryProvider->prepareSql($select, $filter, false, false);

				$sql =
					'SELECT ' . $queryProvider->sSelect . $catalogSelectSql
					. ' FROM ' . $queryProvider->sFrom
					. ' WHERE 1=1 '.$queryProvider->sWhere
					. $queryProvider->sGroupBy;
			break;

			case 'subQuery':
				$queryProvider = new \CIBlockElement();
				$queryProvider->strField = 'ID';

				$sql = $queryProvider->GetList([], $filter, false, false, $select);

				if ($catalogSelectSql !== '')
				{
					$sql = preg_replace('/(\s+FROM\s)/', $catalogSelectSql . '$1', $sql, 1);
				}
			break;
		}

		$sql .= ' LIMIT ' . (int)$offset . ',' . (int)$limit;

		// exec query

		$query = $DB->Query($sql, false, 'FILE: '.__FILE__.'<br> LINE: '.__LINE__);

		return new \CIBlockResult($query);
	}

	/**
	 * Формируем фильтры для запросов
	 *
	 * @param $sourceFilterList
	 * @param $sourceSelectList
	 * @param $queryContext
	 *
	 * @return array
	 * @throws \Bitrix\Main\ObjectNotFoundException
	 */
	protected function makeQueryFilter($sourceFilterList, $sourceSelectList, $queryContext, $changesFilter = null)
	{
		$result = [];
		$iblockIds = [
			'ELEMENT' => $queryContext['IBLOCK_ID'],
			'OFFERS' => $queryContext['OFFER_IBLOCK_ID']
		];
		$isOfferSubQueryInitialized = false;

		// init element filter

		$result['ELEMENT'] = $this->getFilterDefaults('ELEMENT', $iblockIds['ELEMENT']);

		// extend filters by sourceFilter and sourceSelect

		$sourceNameList = array_merge(array_keys($sourceFilterList), array_keys($sourceSelectList));
		$sourceNameList = array_unique($sourceNameList);

		foreach ($sourceNameList as $sourceName)
		{
			$source = $this->getSource($sourceName);
			$sourceFilter = isset($sourceFilterList[$sourceName]) ? $sourceFilterList[$sourceName] : [];
			$sourceSelect = isset($sourceSelectList[$sourceName]) ? $sourceSelectList[$sourceName] : [];

			if ($source->isFilterable())
			{
				$queryFilter = $source->getQueryFilter($sourceFilter, $sourceSelect);

				foreach ($queryFilter as $chainType => $filter)
				{
					if (!empty($filter))
					{
						if (!isset($result[$chainType]))
						{
							$result[$chainType] = $this->getFilterDefaults($chainType, $iblockIds[$chainType]);
						}

						$result[$chainType][] = $filter;
					}
				}
			}
		}

		// extend by changes filter

		if (!empty($changesFilter))
		{
			foreach ($changesFilter as $entityType => $entityFilter)
			{
				if (!isset($result[$entityType]))
				{
					$result[$entityType] = $this->getFilterDefaults($entityType, $iblockIds[$entityType]);
				}

				$result[$entityType][] = $entityFilter;
			}
		}

		// catalog filter

		if (!empty($result['CATALOG']))
		{
			if (empty($iblockIds['OFFERS'])) // hasn't offers
			{
				$result['ELEMENT'][] = $result['CATALOG'];
			}
			else if (!empty($result['OFFERS'])) // has required offers
			{
				$result['OFFERS'][] = $result['CATALOG'];
			}
			else if (!empty($queryContext['OFFER_ONLY']))
			{
				$result['OFFERS'] = $this->getFilterDefaults('OFFERS', $iblockIds['OFFERS']);
				$result['OFFERS'][] = $result['CATALOG'];
			}
			else
			{
				$isOfferSubQueryInitialized = true;
				$catalogOfferFilter = $this->getFilterDefaults('OFFERS', $iblockIds['OFFERS']);
				$catalogOfferFilter[] = $result['CATALOG'];

				// element match catalog condition or has offers match condition

				$result['ELEMENT'][] = [
	                'LOGIC' => 'OR',
	                $result['CATALOG'],
	                [
	                    'ID' => \CIBlockElement::SubQuery('PROPERTY_' . $queryContext['OFFER_PROPERTY_ID'], $catalogOfferFilter)
                    ]
	            ];

				// filter offers by catalog rules

				$result['OFFERS'] = $catalogOfferFilter;
			}
		}

		// offer subquery for elements

		if (!empty($iblockIds['OFFERS']))
		{
			if (!$isOfferSubQueryInitialized && !empty($result['OFFERS']))
			{
				$result['ELEMENT'][] = [
					'ID' => \CIBlockElement::SubQuery('PROPERTY_' . $queryContext['OFFER_PROPERTY_ID'], $result['OFFERS']),
				];
			}
			else if (!isset($result['OFFERS']))
			{
				$result['OFFERS'] = $this->getFilterDefaults('OFFERS', $iblockIds['OFFERS']);
			}
		}

		return $result;
	}

	/**
	 * Исключаем уже выгруженные элементы
	 *
	 * @param $queryFilter
	 * @param $queryContext
	 *
	 * @return Market\Export\Run\Helper\ExcludeFilter
	 */
	protected function getQueryExcludeFilter($queryContext)
	{
		if ($this->queryExcludeFilter === null)
		{
			$this->queryExcludeFilter = new Market\Export\Run\Helper\ExcludeFilter(
				$this->getStorageDataClass(),
				'ELEMENT_ID',
				$this->getStorageReadyFilter($queryContext)
			);
		}

		return $this->queryExcludeFilter;
	}

	/**
	 * Фильтр по готовым элементам
	 *
	 * @param $queryContext
	 *
	 * @return array
	 */
	protected function getStorageReadyFilter($queryContext)
	{
		$filter = [
			'=SETUP_ID' => $queryContext['SETUP_ID']
		];

		switch ($this->getRunAction())
		{
			case 'change':
			case 'refresh':
				$filter['>=TIMESTAMP_X'] = $this->getParameter('initTime');
			break;
		}

		return $filter;
	}

	/**
	 * Формируем select для запросов
	 *
	 * @param $sourceSelect
	 * @param $context
	 *
	 * @return array
	 * @throws \Bitrix\Main\ObjectNotFoundException
	 */
	protected function makeQuerySelect($sourceSelect, $context)
	{
		$result = [
			'ELEMENT' => $this->getSelectDefaults('ELEMENT', $context),
			'OFFERS' => $this->getSelectDefaults('OFFERS', $context)
		];

		foreach ($sourceSelect as $sourceType => $sourceFields)
		{
			$source = $this->getSource($sourceType);
			$querySelect = $source->getQuerySelect($sourceFields);

			foreach ($querySelect as $chainType => $fields)
			{
				if (!empty($fields))
				{
					if (!isset($result[$chainType]))
					{
						$result[$chainType] = [];
					}

					foreach ($fields as $field)
					{
						if (!in_array($field, $result[$chainType]))
						{
							$result[$chainType][] = $field;
						}
					}
				}
			}
		}

		if (empty($result['CATALOG']))
		{
			// nothing
		}
		else if (!empty($context['OFFER_ONLY']))
		{
			$result['OFFERS'] = array_merge($result['OFFERS'], $result['CATALOG']);
		}
		else
		{
			$result['ELEMENT'] = array_merge($result['ELEMENT'], $result['CATALOG']);
			$result['OFFERS'] = array_merge($result['OFFERS'], $result['CATALOG']);
		}

		return $result;
	}

	/**
	 * Определяем тип элемента инфоблока для инфоблока
	 *
	 * @param $element
	 * @param $context
	 *
	 * @return int
	 */
	protected function getElementCatalogType($element, $context)
	{
		$result = static::ELEMENT_TYPE_PRODUCT;

		if (!empty($context['OFFER_ONLY']))
		{
			$result = static::ELEMENT_TYPE_SKU;
		}
		else if (isset($element['CATALOG_TYPE']))
		{
			$result = (int)$element['CATALOG_TYPE'];
		}
		else if (
			array_key_exists('CATALOG_TYPE', $element)
			&& !empty($context['OFFER_IBLOCK_ID'])
		)
		{
			$result = static::ELEMENT_TYPE_SKU;
		}

		return $result;
	}

	/**
	 * Генерируем список "Select по источникам" на основании описании тега
	 *
	 * @param $tagDescriptionList
	 *
	 * @return array
	 */
	protected function getSourceSelect($tagDescriptionList)
	{
		$result = [];
		$childKeys = [
			'ATTRIBUTES',
			'SETTINGS'
		];

		foreach ($tagDescriptionList as $tagName => $tagSourceValue)
		{
			if (isset($tagSourceValue['VALUE']['TYPE']) && isset($tagSourceValue['VALUE']['FIELD']))
			{
				$sourceType = $tagSourceValue['VALUE']['TYPE'];
				$sourceField = $tagSourceValue['VALUE']['FIELD'];

				if (!isset($result[$sourceType]))
				{
					$result[$sourceType] = [];
				}

				if (!in_array($sourceField, $result[$sourceType]))
				{
					$result[$sourceType][] = $sourceField;
				}
			}

			foreach ($childKeys as $childKey)
			{
				if (isset($tagSourceValue[$childKey]) && is_array($tagSourceValue[$childKey]))
				{
					foreach ($tagSourceValue[$childKey] as $attributeValueSource)
					{
						if (
							isset($attributeValueSource['TYPE'])
							&& $attributeValueSource['TYPE'] !== Market\Export\Entity\Manager::TYPE_TEXT
							&& !empty($attributeValueSource['FIELD'])
						)
						{
							$sourceType = $attributeValueSource['TYPE'];
							$sourceField = $attributeValueSource['FIELD'];

							if (!isset($result[$sourceType]))
							{
								$result[$sourceType] = [];
							}

							if (!in_array($sourceField, $result[$sourceType]))
							{
								$result[$sourceType][] = $sourceField;
							}
						}
					}
				}
			}
		}

		return $result;
	}

	protected function sortSourceSelect(&$sourceSelect)
	{
		$order = [];

		foreach ($sourceSelect as $sourceType => $sourceFields)
		{
			$source = $this->getSource($sourceType);
			$order[$sourceType] = $source->getOrder();
		}

		uksort($sourceSelect, function($aType, $bType) use ($order) {
			$aOrder = $order[$aType];
			$bOrder = $order[$bType];

			if ($aOrder === $bOrder) { return 0; }

			return ($aOrder < $bOrder ? -1 : 1);
		});
	}

	/**
	 * Генерируем список "Фильтров по источникам" на основании настроек
	 *
	 * @param \Yandex\Market\Export\IblockLink\Model $iblockLink
	 * @param $iblockContext array
	 * @param $isNeedAll bool|null
	 *
	 * @return array
	 */
	protected function getSourceFilterList(Market\Export\IblockLink\Model $iblockLink, $iblockContext, $isNeedAll = null)
	{
		$result = [];
		$filterCollection = $iblockLink->getFilterCollection();

		/** @var \Yandex\Market\Export\Filter\Model $filterModel */
		foreach ($filterCollection as $filterModel)
		{
			$sourceFilter = $filterModel->getSourceFilter();
			$result[] = [
				'ID' => $filterModel->getInternalId(),
				'FILTER' => $sourceFilter,
				'CONTEXT' => $filterModel->getContext(true) + $iblockContext
			];
		}

		if ($isNeedAll === null)
		{
			$isNeedAll = $iblockLink->isExportAll();
		}

		if ($isNeedAll)
		{
			$result[] = [
				'ID' => null,
				'FILTER' => [],
				'CONTEXT' => $iblockContext
			];
		}

		return $result;
	}

	/**
	 * Поля для запроса по умолчанию
	 *
	 * @param $entityType
	 * @param $context
	 *
	 * @return array
	 */
	protected function getSelectDefaults($entityType, $context)
	{
		switch ($entityType)
		{
			case 'ELEMENT':
				$result = [ 'IBLOCK_ID',  'ID' ];

				if (
					isset($context['OFFER_IBLOCK_ID']) // has offers
					&& empty($context['OFFER_ONLY']) // has not only offers
					&& !$this->isCatalogTypeCompatibility($context) // is valid catalog_type
				)
				{
					$result[] = 'CATALOG_TYPE';
				}
			break;

			case 'OFFERS':
				$result = [ 'IBLOCK_ID', 'ID' ];
			break;

			default:
				$result = [];
			break;
		}

		return $result;
	}

	/**
	 * Фильтр для запроса по умолчанию
	 *
	 * @param $iblockId
	 *
	 * @return array
	 */
	protected function getFilterDefaults($entityType, $iblockId)
	{
		$result = null;

		switch ($entityType)
		{
			case 'ELEMENT':
			case 'OFFERS':
				$result = [
					'IBLOCK_ID' => $iblockId,
					'ACTIVE' => 'Y',
					'ACTIVE_DATE' => 'Y',
				];
			break;

			default:
				$result = [];
			break;
		}

		return $result;
	}

	/**
	 * Получаем значения из источников на основе результатов запроса к базе данных
	 *
	 * @param $sourceSelectList
	 * @param $elementList
	 * @param $parentList
	 *
	 * @return array
	 * @throws \Bitrix\Main\ObjectNotFoundException
	 */
	protected function extractElementListValues($sourceSelect, $elementList, $parentList, $queryContext)
	{
		$result = [];
		$conflictList = $this->getProcessor()->getConflicts();

		foreach ($sourceSelect as $sourceType => $sourceFields)
		{
			$source = $this->getSource($sourceType);
			$sourceValues = $source->getElementListValues($elementList, $parentList, $sourceFields, $queryContext, $result);
			$sourceConflicts = (isset($conflictList[$sourceType]) ? $conflictList[$sourceType] : null);

			foreach ($sourceValues as $elementId => $elementValues)
			{
				if (!isset($result[$elementId]))
				{
					$result[$elementId] = [];
				}

				if ($sourceConflicts !== null)
				{
					foreach ($sourceConflicts as $fieldName => $conflictAction)
					{
						if (isset($elementValues[$fieldName]))
						{
							switch ($conflictAction['TYPE'])
							{
								case 'INCREMENT':
									$elementValues[$fieldName] += $conflictAction['VALUE'];
								break;
							}
						}
					}
				}

				$result[$elementId][$sourceType] = $elementValues;
			}
		}

		return $result;
	}

	/**
	 * Получить источник данных для выгрузки
	 *
	 * @param $type
	 *
	 * @return \Yandex\Market\Export\Entity\Reference\Source
	 * @throws \Bitrix\Main\ObjectNotFoundException
	 */
	protected function getSource($type)
	{
		return Market\Export\Entity\Manager::getSource($type);
	}

	/**
	 * Поле CATALOG_TYPE содержит неверную информацию "Имеет ли товар торговые предложения"
	 *
	 * @param $context
	 *
	 * @return bool
	 * @throws \Bitrix\Main\ArgumentNullException
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 */
	protected function isCatalogTypeCompatibility($context)
	{
		$result = false;

		if (!isset($context['OFFER_IBLOCK_ID'])) // hasn't offers
		{
			$result = false;
		}
		else if (!empty($context['OFFER_ONLY'])) // has only offers
		{
			$result = false;
		}
		else if ($this->isCatalogTypeCompatibility !== null) // already fetched
		{
			$result = $this->isCatalogTypeCompatibility;
		}
		else
		{
			$selfOption = (string)Market\Config::getOption('export_offer_catalog_type_compatibility');
			$catalogVersion = Main\ModuleManager::getVersion('catalog');

			if ($selfOption !== '') // has self module option
			{
				$result = ($selfOption === 'Y');
			}
			else if ($catalogVersion !== false && CheckVersion('15.99.99', $catalogVersion)) // module catalog version less 16.0.0
			{
				$result = true;
			}
			else if (Main\Config\Option::get('catalog', 'show_catalog_tab_with_offers') === 'Y') // catalog tab open for product with offers
			{
				$result = true;
			}

			$this->isCatalogTypeCompatibility = $result;
		}

		return $result;
	}
}
