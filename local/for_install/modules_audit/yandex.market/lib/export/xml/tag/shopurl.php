<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;

class ShopUrl extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'url',
			'value_type' => Market\Type\Manager::TYPE_URL
		];
	}

	public function isDefined()
	{
		return true;
	}

	public function getDefaultValue(array $context = [], $siblingsValues = null)
	{
		return $context['DOMAIN_URL'];
	}
}
