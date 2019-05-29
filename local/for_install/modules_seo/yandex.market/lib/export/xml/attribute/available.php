<?php

namespace Yandex\Market\Export\Xml\Attribute;

use Yandex\Market;

class Available extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'available',
			'value_type' => Market\Type\Manager::TYPE_BOOLEAN
		];
	}

	public function getSourceRecommendation(array $context = [])
	{
		$result = [];

		if ($context['HAS_CATALOG'])
		{
		    $result[] = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_CATALOG_PRODUCT,
				'FIELD' => 'AVAILABLE'
			];
		}

		return $result;
	}
}