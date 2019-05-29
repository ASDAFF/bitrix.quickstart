<?php

namespace Yandex\Market\Export\Entity\Reference;

use Yandex\Market;
use Bitrix\Main;
use Bitrix\Iblock;

abstract class ElementEvent extends Event
{
	protected static $elementIblockIdCache = [];

	protected static function isElementChangeRegistered($setupId, $elementId)
	{
		return Market\Export\Run\Manager::isChangeRegistered(
			$setupId,
			Market\Export\Run\Manager::ENTITY_TYPE_OFFER,
			$elementId
		);
	}

	protected static function registerElementChange($setupId, $elementId)
	{
		Market\Export\Run\Manager::registerChange(
			$setupId,
			Market\Export\Run\Manager::ENTITY_TYPE_OFFER,
			$elementId
		);
	}

	protected static function isTargetElement($iblockId, $offerIblockId, $elementId, $elementIblockId = null)
	{
		if ($elementIblockId === null)
		{
			$elementIblockId = static::getElementIblockId($elementId);
		}
		else
		{
			$elementIblockId = (int)$elementIblockId;
		}

		return (
			$elementIblockId !== null
			&& ($elementIblockId === (int)$iblockId || $elementIblockId === (int)$offerIblockId)
		);
	}

	protected static function getElementIblockId($elementId)
	{
		$result = null;
		$elementId = (int)$elementId;

		if ($elementId <= 0)
		{
			// nothing
		}
		else if (isset(static::$elementIblockIdCache[$elementId]))
		{
			$result = static::$elementIblockIdCache[$elementId] ?: null;
		}
		else if (Main\Loader::includeModule('iblock'))
		{
			$query = Iblock\ElementTable::getList([
				'filter' => [ '=ID' => $elementId ],
				'select' => [ 'IBLOCK_ID' ],
				'limit' => 1
			]);

			if ($row = $query->fetch())
			{
				$result = (int)$row['IBLOCK_ID'];
			}

			static::$elementIblockIdCache[$elementId] = $result ?: false;
		}

		return $result;
	}

	protected function getEvents($context)
	{
		$setupId = (int)$context['SETUP_ID'];
		$iblockId = (int)$context['IBLOCK_ID'];
		$offerIblockId = null;

		if (isset($context['OFFER_IBLOCK_ID']))
		{
			$offerIblockId = (int)$context['OFFER_IBLOCK_ID'];
		}

		return $this->getEventsForIblock($setupId, $iblockId, $offerIblockId);
	}

	abstract protected function getEventsForIblock($setupId, $iblockId, $offerIblockId = null);
}