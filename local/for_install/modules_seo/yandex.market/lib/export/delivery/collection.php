<?php

namespace Yandex\Market\Export\Delivery;

use Yandex\Market;

class Collection extends Market\Reference\Storage\Collection
{
	public static function getItemReference()
	{
		return Model::getClassName();
	}

	public function getDeliveryOptions()
	{
		$result = [];

		/** @var Model $model */
		foreach ($this as $model)
		{
			$option = $model->getDeliveryOption();

			if (isset($option))
			{
				$type = $model->getDeliveryType();

				if (!isset($result[$type])) { $result[$type] = []; }

				$result[$type][] = $option;
			}
		}

		return $result;
	}
}