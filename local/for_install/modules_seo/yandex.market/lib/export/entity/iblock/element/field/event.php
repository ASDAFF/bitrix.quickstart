<?php

namespace Yandex\Market\Export\Entity\Iblock\Element\Field;

use Bitrix\Main;
use Yandex\Market;

class Event extends Market\Export\Entity\Reference\ElementEvent
{
	public function OnAfterIBlockElementAdd($setupId, $iblockId, $offerIblockId, $fields)
	{
		if (
			$fields['RESULT']
			&& !static::isElementChangeRegistered($setupId, $fields['ID'])
			&& static::isTargetElement($iblockId, $offerIblockId, $fields['ID'], (int)$fields['IBLOCK_ID'])
		)
		{
			static::registerElementChange($setupId, $fields['ID']);
		}
	}

	public function OnAfterIBlockElementUpdate($setupId, $iblockId, $offerIblockId, $fields)
	{
		if (
			$fields['RESULT']
			&& !static::isElementChangeRegistered($setupId, $fields['ID'])
			&& static::isTargetElement($iblockId, $offerIblockId, $fields['ID'], (int)$fields['IBLOCK_ID'])
		)
		{
			static::registerElementChange($setupId, $fields['ID']);
		}
	}

	public function OnAfterIBlockElementDelete($setupId, $iblockId, $offerIblockId, $fields)
	{
		if (
			!static::isElementChangeRegistered($setupId, $fields['ID'])
			&& static::isTargetElement($iblockId, $offerIblockId, $fields['ID'], (int)$fields['IBLOCK_ID'])
		)
		{
			static::registerElementChange($setupId, $fields['ID']);
		}
	}

	protected function getEventsForIblock($setupId, $iblockId, $offerIblockId = null)
	{
		return [
			[
				'module' => 'iblock',
				'event' => 'OnAfterIBlockElementAdd',
				'arguments' => [
					$setupId,
					$iblockId,
					$offerIblockId
				]
			],
			[
				'module' => 'iblock',
				'event' => 'OnAfterIBlockElementUpdate',
				'arguments' => [
					$setupId,
					$iblockId,
					$offerIblockId
				]
			],
			[
				'module' => 'iblock',
				'event' => 'OnAfterIBlockElementDelete',
				'arguments' => [
					$setupId,
					$iblockId,
					$offerIblockId
				]
			]
		];
	}
}