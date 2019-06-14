<?php

namespace Yandex\Market\Export\Xml\Tag;

class ShopName extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'name'
		];
	}

	public function isDefined()
	{
		return true;
	}

	public function getDefaultValue(array $context = [], $siblingsValues = null)
	{
		return \COption::GetOptionString('main', 'site_name');
	}
}
