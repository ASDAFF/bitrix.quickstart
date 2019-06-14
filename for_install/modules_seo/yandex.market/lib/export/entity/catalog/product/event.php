<?php

namespace Yandex\Market\Export\Entity\Catalog\Product;

use Bitrix\Iblock;
use Bitrix\Main;
use Yandex\Market;

class Event extends Market\Export\Entity\Reference\ElementEvent
{
	public function onProductUpdate($setupId, $iblockId, $offerIblockId, $elementId, $fields)
	{
		if (
			!static::isElementChangeRegistered($setupId, $elementId)
			&& static::isTargetElement($iblockId, $offerIblockId, $elementId)
		)
		{
			static::registerElementChange($setupId, $elementId);
		}
	}

	public function onEntityAfterUpdate($setupId, $iblockId, $offerIblockId, Main\Event $event)
	{
		$elementId = $event->getParameter('id');
		$elementIblockId = null;
		$externalData = $event->getParameter('external_fields');

		if (isset($externalData['IBLOCK_ID']))
		{
			$elementIblockId = $externalData['IBLOCK_ID'];
		}

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
		$result = null;

		if (Main\Loader::includeModule('catalog') && class_exists('Bitrix\Catalog\Model\Product')) // is new version
		{
			$result = [
				[
					'module' => 'catalog',
					'event' => 'Bitrix\Catalog\Model\Product::OnAfterAdd',
					'method' => 'onEntityAfterUpdate',
					'arguments' => [
						$setupId,
						$iblockId,
						$offerIblockId
					]
				],
				[
					'module' => 'catalog',
					'event' => 'Bitrix\Catalog\Model\Product::OnAfterUpdate',
					'method' => 'onEntityAfterUpdate',
					'arguments' => [
						$setupId,
						$iblockId,
						$offerIblockId
					]
				]
				// no delete event
			];
		}
		else
		{
			$result = [
				[
					'module' => 'catalog',
					'event' => 'OnProductAdd',
					'method' => 'onProductUpdate',
					'arguments' => [
						$setupId,
						$iblockId,
						$offerIblockId
					]
				],
				[
					'module' => 'catalog',
					'event' => 'OnProductUpdate',
					'method' => 'onProductUpdate',
					'arguments' => [
						$setupId,
						$iblockId,
						$offerIblockId
					]
				]
				// no delete event
			];
		}

		return $result;
	}
}