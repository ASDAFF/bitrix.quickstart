<?php

namespace Yandex\Market\Export\Entity\Iblock\Element\Property;

use Yandex\Market;

class Event extends Market\Export\Entity\Reference\ElementEvent
{
	public function onAfterSetPropertyValues($setupId, $iblockId, $offerIblockId, $elementId, $elementIblockId, $propertyValues, $flags)
	{
		if (
			!static::isElementChangeRegistered($setupId, $elementId)
			&& static::isTargetElement($iblockId, $offerIblockId, $elementId, $elementIblockId)
		)
		{
			static::registerElementChange($setupId, $elementId);
		}
	}

	protected function getEventsForIblock($setupId, $iblockId, $offerIblockId = null)
	{
		return [
			[
				'module' => 'iblock',
				'event' => 'OnAfterIBlockElementSetPropertyValuesEx',
				'method' => 'onAfterSetPropertyValues',
				'arguments' => [
					$setupId,
					$iblockId,
					$offerIblockId
				]
			],
			[
				'module' => 'iblock',
				'event' => 'OnAfterIBlockElementSetPropertyValues',
				'method' => 'onAfterSetPropertyValues',
				'arguments' => [
					$setupId,
					$iblockId,
					$offerIblockId
				]
			]
		];
	}
}