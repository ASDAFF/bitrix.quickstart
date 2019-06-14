<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;

class Param extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'param'
		];
	}

	public function getDefaultSource(array $context = [])
	{
		return Market\Export\Entity\Manager::TYPE_IBLOCK_ELEMENT_PROPERTY;
	}
}
