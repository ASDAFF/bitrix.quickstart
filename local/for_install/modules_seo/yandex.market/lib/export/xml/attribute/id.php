<?php

namespace Yandex\Market\Export\Xml\Attribute;

use Yandex\Market;

class Id extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'id'
		];
	}

	public function isDefined()
	{
		return true;
	}

	public function getDefinedSource(array $context = [])
	{
		$result = null;

		if (isset($context['OFFER_IBLOCK_ID']))
		{
			$result = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_OFFER_FIELD,
				'FIELD' => 'ID'
			];
		}
		else
		{
			$result = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_ELEMENT_FIELD,
				'FIELD' => 'ID'
			];
		}

		return $result;
	}
}