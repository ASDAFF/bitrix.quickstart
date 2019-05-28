<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;

class Description extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'description',
			'value_type' => Market\Type\Manager::TYPE_HTML,
			'max_length' => 3000
		];
	}

	public function getSourceRecommendation(array $context = [])
	{
		$result = [
			[
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_ELEMENT_FIELD,
				'FIELD' => 'PREVIEW_TEXT'
			],
			[
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_ELEMENT_FIELD,
				'FIELD' => 'DETAIL_TEXT'
			]
		];

		if (isset($context['OFFER_IBLOCK_ID']))
		{
			$result[] = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_OFFER_FIELD,
				'FIELD' => 'PREVIEW_TEXT'
			];

			$result[] = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_OFFER_FIELD,
				'FIELD' => 'DETAIL_TEXT'
			];
		}

		return $result;
	}
}