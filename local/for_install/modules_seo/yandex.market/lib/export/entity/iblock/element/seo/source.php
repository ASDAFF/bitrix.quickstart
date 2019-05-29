<?php

namespace Yandex\Market\Export\Entity\Iblock\Element\Seo;

use Yandex\Market;
use Bitrix\Main;
use Bitrix\Iblock;

Main\Localization\Loc::loadMessages(__FILE__);

class Source extends Market\Export\Entity\Reference\Source
{
	public function getElementListValues($elementList, $parentList, $select, $queryContext, $sourceValues)
	{
		$result = [];

		foreach ($elementList as $elementId => $element)
		{
			$parent = null;

			if (!isset($element['PARENT_ID']))
			{
				$parent = $element;
			}
			else if (isset($parentList[$element['PARENT_ID']])) // has parent element
			{
				$parent = $parentList[$element['PARENT_ID']];
			}

			if ($parent)
			{
				$result[$elementId] = $this->getSeoValues($parent, $select);
			}
		}

		return $result;
	}

	public function getFields(array $context = [])
	{
		return $this->buildFieldsDescription([
			'ELEMENT_META_TITLE' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_STRING
			],
			'ELEMENT_META_KEYWORDS' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_STRING
			],
			'ELEMENT_META_DESCRIPTION' => [
				'TYPE' => Market\Export\Entity\Data::TYPE_STRING
			]
		]);
	}

	protected function getLangPrefix()
	{
		return 'IBLOCK_ELEMENT_SEO_';
	}

	protected function getSeoValues($element, $select)
	{
		$result = [];

		if (Main\Loader::includeModule('iblock'))
		{
			$provider = new Iblock\InheritedProperty\ElementValues($element["IBLOCK_ID"], $element["ID"]);
			$providerValues = $provider->getValues();

			foreach ($select as $fieldName)
			{
				$result[$fieldName] = isset($providerValues[$fieldName]) ? $providerValues[$fieldName] : null;
			}
		}

		return $result;
	}
}