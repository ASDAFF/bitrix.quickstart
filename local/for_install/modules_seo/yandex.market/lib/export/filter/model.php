<?php

namespace Yandex\Market\Export\Filter;

use Yandex\Market;

class Model extends Market\Reference\Storage\Model
{
	public static function getDataClass()
	{
		return Table::getClassName();
	}

	public function getSourceFilter()
	{
		$conditionCollection = $this->getConditionCollection();
		$result = [];

		/** @var \Yandex\Market\Export\FilterCondition\Model $condition */
		foreach ($conditionCollection as $condition)
		{
			if ($condition->isValid())
			{
				$conditionCompare = $condition->getQueryCompare();
				$conditionField = $condition->getQueryField();
				$conditionValue = $condition->getQueryValue();
				$conditionSource = $condition->getSourceName();

				if (!isset($result[$conditionSource]))
				{
					$result[$conditionSource] = [];
				}

				$result[$conditionSource][] = [
					'FIELD' => $conditionField,
					'COMPARE' => $conditionCompare,
					'VALUE' => $conditionValue
				];
			}
		}

		return $result;
	}

	public function getUsedSources()
	{
		return array_keys($this->getSourceFilter());
	}

	/**
	 * @return array
	 */
	public function getContext($isOnlySelf = false)
	{
		$result = [
			'FILTER_ID' => $this->getId()
		];

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

		if (!$isOnlySelf)
		{
			$result = $this->mergeParentContext($result);
		}

		return $result;
	}

	protected function mergeParentContext($selfContext)
	{
		$collection = $this->getCollection();
		$iblockLink = $collection ? $collection->getParent() : null;
		$iblockLinkContext = $iblockLink ? $iblockLink->getContext() : null;
		$result = $selfContext;

		if (isset($iblockLinkContext))
		{
			$result += $iblockLinkContext;
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

	/**
	 * @return \Yandex\Market\Export\FilterCondition\Collection
	 */
	public function getConditionCollection()
	{
		return $this->getChildCollection('FILTER_CONDITION');
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
			case 'FILTER_CONDITION':
				$result = Market\Export\FilterCondition\Collection::getClassName();
			break;

			case 'DELIVERY':
				$result = Market\Export\Delivery\Collection::getClassName();
			break;
		}

		return $result;
	}
}
