<?php

namespace Yandex\Market\Export\Xml\Tag;

class ShopPlatform extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'platform'
		];
	}

	public function isDefined()
	{
		return true;
	}

	public function getDefaultValue(array $context = [], $siblingsValues = null)
	{
		return 'BSM/Yandex/Market';
	}
}
