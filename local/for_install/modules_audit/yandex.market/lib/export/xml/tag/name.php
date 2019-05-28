<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;

class Name extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'name'
		];
	}

	public function getSourceRecommendation(array $context = [])
	{
		$result = [
			[
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_ELEMENT_FIELD,
				'FIELD' => 'NAME'
			]
		];

		if (isset($context['OFFER_IBLOCK_ID']))
		{
			$result[] = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_OFFER_FIELD,
				'FIELD' => 'NAME'
			];
		}

		return $result;
	}
}