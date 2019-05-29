<?php

namespace Yandex\Market\Export\Xml\Attribute;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class VolumeUnit extends Base
{
	public function getDefaultParameters()
	{
		return [
			'id' => 'volume_unit',
			'name' => 'unit',
		];
	}

	public function getSourceRecommendation(array $context = [])
	{
		$langKey = $this->getLangKey();

		return [
			[
				'TYPE' => Market\Export\Entity\Manager::TYPE_TEXT,
				'VALUE' => Market\Config::getLang($langKey . '_RECOMMENDATION_MILLILITER')
			],
			[
				'TYPE' => Market\Export\Entity\Manager::TYPE_TEXT,
				'VALUE' => Market\Config::getLang($langKey . '_RECOMMENDATION_LITER')
			]
		];
	}
}