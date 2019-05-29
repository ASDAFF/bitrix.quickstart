<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;

class CategoryId extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'categoryId',
			'value_type' => Market\Type\Manager::TYPE_CATEGORY
		];
	}

	public function getSourceRecommendation(array $context = [])
	{
		$result = [
			[
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_ELEMENT_FIELD,
				'FIELD' => 'IBLOCK_SECTION_ID'
			]
		];

		if (isset($context['OFFER_IBLOCK_ID']))
		{
			$result[] = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_OFFER_FIELD,
				'FIELD' => 'IBLOCK_SECTION_ID'
			];
		}

		return $result;
	}
}