<?php

namespace Yandex\Market\Export\Entity\Iblock\Offer\Seo;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class Source extends Market\Export\Entity\Iblock\Element\Seo\Source
{
	public function getElementListValues($elementList, $parentList, $select, $queryContext, $sourceValues)
	{
		$result = [];

		foreach ($elementList as $elementId => $element)
		{
			$result[$elementId] = $this->getSeoValues($element, $select);
		}

		return $result;
	}

	public function getFields(array $context = [])
	{
		return isset($context['OFFER_IBLOCK_ID']) ? parent::getFields($context) : [];
	}

	protected function getLangPrefix()
	{
		return 'IBLOCK_OFFER_SEO_';
	}
}