<?php

namespace Yandex\Market\Export\Xml\Tag;

use Bitrix\Main;

class ShopPlatformVersion extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'version'
		];
	}

	public function isDefined()
	{
		return true;
	}

	public function getDefaultValue(array $context = [], $siblingsValues = null)
	{
		return Main\ModuleManager::getVersion('yandex.market');
	}
}
