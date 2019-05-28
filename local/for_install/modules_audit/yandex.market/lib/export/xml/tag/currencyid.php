<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;
use Bitrix\Currency;

class CurrencyId extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'currencyId',
			'value_type' => Market\Type\Manager::TYPE_CURRENCY
		];
	}

	public function getSourceRecommendation(array $context = [])
	{
		$result = [];

		if ($context['HAS_CATALOG'])
		{
			$result = [
				[
					'TYPE' => Market\Export\Entity\Manager::TYPE_CATALOG_PRICE,
					'FIELD' => 'MINIMAL.CURRENCY'
				],
				[
					'TYPE' => Market\Export\Entity\Manager::TYPE_CATALOG_PRICE,
					'FIELD' => 'OPTIMAL.CURRENCY'
				],
				[
					'TYPE' => Market\Export\Entity\Manager::TYPE_CATALOG_PRICE,
					'FIELD' => 'BASE.CURRENCY'
				]
			];
		}

		return $result;
	}
}