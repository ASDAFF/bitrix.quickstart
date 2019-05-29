<?php

namespace Yandex\Market\Export\Xml\Attribute;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class VolumeName extends Base
{
	public function getDefaultParameters()
	{
		return [
			'id' => 'volume_name',
			'name' => 'name',
		];
	}

	public function isDefined()
	{
		return true;
	}

	public function getDefinedSource(array $context = [])
	{
		$langKey = $this->getLangKey();

		return [
			'TYPE' => Market\Export\Entity\Manager::TYPE_TEXT,
			'VALUE' => Market\Config::getLang($langKey . '_DEFINED_VALUE')
		];
	}
}