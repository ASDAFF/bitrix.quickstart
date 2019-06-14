<?php

namespace Yandex\Market\Export\Xml\Tag;

class ShopCompany extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'company'
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
