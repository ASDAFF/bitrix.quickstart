<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;

class Picture extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'picture',
			'value_type' => Market\Type\Manager::TYPE_FILE,
			'max_count' => 10
		];
	}

	public function getSourceRecommendation(array $context = [])
	{
		$result = [
			[
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_ELEMENT_FIELD,
				'FIELD' => 'DETAIL_PICTURE',
			],
			[
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_ELEMENT_FIELD,
				'FIELD' => 'PREVIEW_PICTURE',
			]
		];

		if (isset($context['OFFER_IBLOCK_ID']))
		{
			$result[] = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_OFFER_FIELD,
				'FIELD' => 'DETAIL_PICTURE',
			];

			$result[] = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_OFFER_FIELD,
				'FIELD' => 'PREVIEW_PICTURE',
			];
		}

		return $result;
	}
}
