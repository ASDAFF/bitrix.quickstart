<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;
use Bitrix\Main;
use Bitrix\Catalog;

class Vat extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'vat',
			'value_type' => Market\Type\Manager::TYPE_VAT
		];
	}

	public function getSourceRecommendation(array $context = [])
	{
		$result = [];

		if ($context['HAS_CATALOG'])
		{
			$result[] = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_CATALOG_PRODUCT,
				'FIELD' => 'VAT'
			];
		}

		return $result;
	}
}