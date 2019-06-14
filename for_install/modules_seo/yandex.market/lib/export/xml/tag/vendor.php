<?php

namespace Yandex\Market\Export\Xml\Tag;

use Bitrix\Main;
use Yandex\Market;
use Bitrix\Iblock;

class Vendor extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'vendor'
		];
	}

	public function getSourceRecommendation(array $context = [])
	{
		$returnList = [];

		if (!empty($context['IBLOCK_ID']) && Main\Loader::includeModule('iblock'))
		{
			$query = Iblock\PropertyTable::getList([
				'filter' => [
					'=IBLOCK_ID' => $context['IBLOCK_ID'],
					'@CODE' => [
						'MANUFACTURER',
						'VENDOR',
						'BRAND'
					]
				],
				'select' => [
					'ID'
				]
			]);

			while ($prop = $query->fetch())
			{
				$returnList[] = [
					'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_ELEMENT_PROPERTY,
					'FIELD' => $prop['ID']
				];
			}
		}

		return $returnList;
	}
}