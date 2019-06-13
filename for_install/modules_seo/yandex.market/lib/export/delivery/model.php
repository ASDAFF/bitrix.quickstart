<?php

namespace Yandex\Market\Export\Delivery;

use Yandex\Market;

class Model extends Market\Reference\Storage\Model
{
	public static function getDataClass()
	{
		return Table::getClassName();
	}

	public function getDeliveryType()
	{
		return $this->getField('DELIVERY_TYPE') ?: Table::DELIVERY_TYPE_DELIVERY;
	}

	public function getDeliveryOption()
	{
		$result = null;
		$cost = $this->getCost();
		$days = $this->getDays();

		if (isset($cost) && isset($days))
		{
			$result = [
				'COST' => $cost,
				'DAYS' => $days
			];

			$orderBefore = $this->getOrderBefore();

			if (isset($orderBefore))
			{
				$result['ORDER_BEFORE'] = $orderBefore;
			}
		}

		return $result;
	}

	public function getCost()
	{
		$value = trim($this->getField('PRICE'));
		$valueFloat = (float)$value;
		$result = null;

		if ($valueFloat >= 0 && strlen($value) > 0)
		{
			$result = round($valueFloat, 2);
		}

		return $result;
	}

	public function getDays()
	{
		$periodFromValue = $this->getField('PERIOD_FROM');
		$periodFrom = (int)$periodFromValue;
		$hasPeriodFrom = (strlen($periodFromValue) > 0 && $periodFrom >= 0);
		$periodToValue = $this->getField('PERIOD_TO');
		$periodTo = (int)$periodToValue;
		$hasPeriodTo = (strlen($periodToValue) > 0 && $periodTo >= 0);
		$result = null;

		if ($hasPeriodFrom && $hasPeriodTo)
		{
			if ($periodFrom < $periodTo)
			{
				$result = $periodFrom . '-' . $periodTo;
			}
			else
			{
				$result = $periodFrom;
			}
		}
		else if ($hasPeriodFrom)
		{
			$result = $periodFrom;
		}
		else if ($hasPeriodTo)
		{
			$result = $periodTo;
		}

		return $result;
	}

	public function getOrderBefore()
	{
		$value = $this->getField('ORDER_BEFORE');
		$valueInt = (int)$value;
		$result = null;

		if ($valueInt >= 0 && $valueInt <= 24 && strlen($value) > 0)
		{
			$result = $valueInt;
		}

		return $result;
	}
}