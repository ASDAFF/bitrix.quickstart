<?php

namespace Yandex\Market\Export\Entity\Iblock\Offer\Field;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class Source extends Market\Export\Entity\Iblock\Element\Field\Source
{
	public function getQuerySelect($select)
	{
		return [
			'ELEMENT' => $select,
			'OFFERS' => $select
		];
	}

	public function getQueryFilter($filter, $select)
	{
		return [
			'OFFERS' => $this->buildQueryFilter($filter)
		];
	}

	public function getElementListValues($elementList, $parentList, $select, $queryContext, $sourceValues)
	{
		$result = [];

		foreach ($elementList as $elementId => $element)
		{
			$result[$elementId] = $this->getFieldValues($element, $select); // extract for all
		}

		return $result;
	}

	public function getFields(array $context = [])
	{
		return isset($context['OFFER_IBLOCK_ID']) ? parent::getFields($context) : [];
	}

	protected function getLangPrefix()
	{
		return 'IBLOCK_OFFER_FIELD_';
	}
}