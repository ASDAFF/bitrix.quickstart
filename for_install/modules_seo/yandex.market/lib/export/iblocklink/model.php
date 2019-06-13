<?php

namespace Yandex\Market\Export\IblockLink;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Yandex\Market;

Loc::loadMessages(__FILE__);

class Model extends Market\Reference\Storage\Model
{
	protected $iblockFields;
	protected $iblockCatalog;

	public function getTagDescriptionList()
	{
		$paramCollection = $this->getParamCollection();
		$result = [];
		$textType = Market\Export\Entity\Manager::TYPE_TEXT;

		/** @var \Yandex\Market\Export\Param\Model $param */
		foreach ($paramCollection as $param)
		{
			$paramValueCollection = $param->getValueCollection();
			$tagResult = [
				'TAG' => $param->getField('XML_TAG'),
				'VALUE' => null,
				'ATTRIBUTES' => [],
				'SETTINGS' => $param->getSettings()
			];

			/** @var \Yandex\Market\Export\ParamValue\Model $paramValue */
			foreach ($paramValueCollection as $paramValue)
			{
				$sourceType = $paramValue->getSourceType();
				$sourceField = $paramValue->getSourceField();
				$sourceMap = (
					$sourceType === $textType
						? [ 'VALUE' => $sourceField ]
						: [ 'TYPE' => $sourceType, 'FIELD' => $sourceField ]
				);

				if ($paramValue->isAttribute())
				{
					$attributeName = $paramValue->getAttributeName();

					$tagResult['ATTRIBUTES'][$attributeName] = $sourceMap;
				}
				else
				{
					$tagResult['VALUE'] = $sourceMap;
				}
			}

			$result[] = $tagResult;
		}

		return $result;
	}

	public function getSourceSelect()
	{
		$result = [];
		$paramCollection = $this->getParamCollection();

		/** @var \Yandex\Market\Export\Param\Model $param */
		foreach ($paramCollection as $param)
		{
			$paramValueCollection = $param->getValueCollection();

			/** @var \Yandex\Market\Export\ParamValue\Model $paramValue */
			foreach ($paramValueCollection as $paramValue)
			{
				$sourceType = $paramValue->getSourceType();
				$sourceField = $paramValue->getSourceField();

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

		$result = $this->extendSourceSelectByTemplate($result);

		return $result;
	}

	protected function extendSourceSelectByTemplate($sourceSelect)
	{
		$result = $sourceSelect;

		foreach ($sourceSelect as $sourceType => $sourceFields)
		{
			$source = Market\Export\Entity\Manager::getSource($sourceType);

			if ($source->isTemplate() && Market\Template\Engine::load())
			{
				foreach ($sourceFields as $sourceField)
				{
					$templateNode = Market\Template\Engine::compileTemplate($sourceField);
					$templateSourceSelect = $templateNode->getSourceSelect();

					foreach ($templateSourceSelect as $templateSourceType => $templateSourceFields)
					{
						if (!isset($result[$templateSourceType]))
						{
							$result[$templateSourceType] = [];
						}

						foreach ($templateSourceFields as $templateSourceField)
						{
							if (!in_array($templateSourceField, $result[$templateSourceType]))
							{
								$result[$templateSourceType][] = $templateSourceField;
							}
						}
					}
				}
			}
		}

		return $result;
	}

	public function getUsedSources()
	{
		$result = $this->getSourceSelect();

		foreach ($this->getFilterCollection() as $filterModel)
		{
			$filterUserSources = $filterModel->getUsedSources();

			foreach ($filterUserSources as $sourceType)
			{
				if (!isset($result[$sourceType]))
				{
					$result[$sourceType] = true;
				}
			}
		}

		return array_keys($result);
	}

	public function handleChanges($direction)
	{
		$sourceList = $this->getUsedSources();
		$context = $this->getContext();

		foreach ($sourceList as $sourceType)
		{
			$eventHandler = Market\Export\Entity\Manager::getEvent($sourceType);

			$eventHandler->handleChanges($direction, $context);
		}
	}

	/**
	 * @return array
	 */
	public function getContext()
	{
		$result = [
			'IBLOCK_LINK_ID' => $this->getId(),
			'IBLOCK_ID' => $this->getIblockId(),
			'SITE_ID' => $this->getSiteId(),
			'HAS_CATALOG' => $this->hasIblockCatalog()
		];

		// offers

		if ($this->hasIblockOffers())
		{
			$result['OFFER_ONLY'] = $this->isIblockCatalogOnlyOffers();
			$result['OFFER_IBLOCK_ID'] = $this->getOfferIblockId();
			$result['OFFER_PROPERTY_ID'] = $this->getOfferPropertyId();
		}

		// sales notes

		$salesNotes = $this->getSalesNotes();

		if (strlen($salesNotes) > 0)
		{
			$result['SALES_NOTES'] = $salesNotes;
		}

		// delivery options

		$deliveryOptions = $this->getDeliveryOptions();

		if (!empty($deliveryOptions))
		{
			$result['DELIVERY_OPTIONS'] = $deliveryOptions;
		}

		$result = $this->mergeParentContext($result);

		return $result;
	}

	protected function mergeParentContext($selfContext)
	{
		$collection = $this->getCollection();
		$setup = $collection ? $collection->getParent() : null;
		$setupContext = $setup ? $setup->getContext() : null;
		$result = $selfContext;

		if (isset($setupContext))
		{
			$result += $setupContext;

			if (isset($setupContext['DELIVERY_OPTIONS']) && !isset($selfContext['DELIVERY_OPTIONS']))
			{
				unset($result['DELIVERY_OPTIONS']);
			}
		}

		return $result;
	}

	public function getDeliveryOptions()
	{
		$deliveryCollection = $this->getDeliveryCollection();

		return $deliveryCollection->getDeliveryOptions();
	}

	public function getSalesNotes()
	{
		return trim($this->getField('SALES_NOTES'));
	}

	public function getIblockId()
	{
		return (int)$this->getField('IBLOCK_ID');
	}

	public function getOfferIblockId()
	{
		$result = null;

		if ($this->hasIblockOffers())
		{
			$iblockCatalog = $this->getIblockCatalog();
			$result = (int)$iblockCatalog['IBLOCK_ID'];
		}

		return $result;
	}

	public function getOfferPropertyId()
	{
		$result = null;

		if ($this->hasIblockOffers())
		{
			$iblockCatalog = $this->getIblockCatalog();
			$result = (int)$iblockCatalog['SKU_PROPERTY_ID'];
		}

		return $result;
	}

	public function isExportAll()
	{
		return $this->getField('EXPORT_ALL') === '1';
	}

	public function getSiteId()
	{
		$iblockFields = $this->getIblockFields();

		return $iblockFields['LID'];
	}

	public function getIblockFields()
	{
		if (!isset($this->iblockFields))
		{
			$iblockId = $this->getIblockId();
			$this->iblockFields = [];

			if ($iblockId > 0 && Main\Loader::includeModule('iblock'))
			{
				$query = \CIBlock::GetList([], [ 'ID' => $iblockId, 'CHECK_PERMISSIONS' => 'N' ]);

				if ($iblock = $query->Fetch())
				{
					$this->iblockFields = $iblock;
				}
			}
		}

		return $this->iblockFields;
	}

	public function getIblockCatalog()
	{
		if (!isset($this->iblockCatalog) && Main\Loader::includeModule('catalog'))
		{
			$iblockId = $this->getIblockId();
			$iblockCatalog = \CCatalogSku::GetInfoByIBlock($iblockId);

			$this->iblockCatalog = $iblockCatalog ?: false;
		}

		return $this->iblockCatalog;
	}

	public function hasIblockCatalog()
	{
		$iblockCatalog = $this->getIblockCatalog();

		return !empty($iblockCatalog);
	}

	public function isIblockCatalogOnlyOffers()
	{
		$iblockCatalog = $this->getIblockCatalog();

		return (
			!empty($iblockCatalog['CATALOG_TYPE'])
			&& $iblockCatalog['CATALOG_TYPE'] === \CCatalogSku::TYPE_PRODUCT
		);
	}

	public function hasIblockOffers()
	{
		$iblockCatalog = $this->getIblockCatalog();
		$result = false;

		if (
			!empty($iblockCatalog['CATALOG_TYPE'])
			&& (
				$iblockCatalog['CATALOG_TYPE'] === \CCatalogSku::TYPE_PRODUCT
				|| $iblockCatalog['CATALOG_TYPE'] === \CCatalogSku::TYPE_FULL
			)
		)
		{
			$result = true;
		}

		return $result;
	}

	/**
	 * Название класса таблицы
	 *
	 * @return Table
	 */
	public static function getDataClass()
	{
		return Table::getClassName();
	}

	/**
	 * @return \Yandex\Market\Export\Filter\Collection
	 */
	public function getFilterCollection()
	{
		return $this->getChildCollection('FILTER');
	}

	/**
	 * @return \Yandex\Market\Export\Param\Collection
	 */
	public function getParamCollection()
	{
		return $this->getChildCollection('PARAM');
	}

	/**
	 * @return \Yandex\Market\Export\Param\Collection
	 */
	public function getDeliveryCollection()
	{
		return $this->getChildCollection('DELIVERY');
	}

	protected function getChildCollectionReference($fieldKey)
	{
		$result = null;

		switch ($fieldKey)
		{
			case 'FILTER':
				$result = Market\Export\Filter\Collection::getClassName();
			break;

			case 'PARAM':
				$result = Market\Export\Param\Collection::getClassName();
			break;

			case 'DELIVERY':
				$result = Market\Export\Delivery\Collection::getClassName();
			break;
		}

		return $result;
	}
}