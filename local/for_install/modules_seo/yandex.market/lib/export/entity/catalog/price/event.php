<?php

namespace Yandex\Market\Export\Entity\Catalog\Price;

use Bitrix\Catalog;
use Bitrix\Main;
use Yandex\Market;

class Event extends Market\Export\Entity\Reference\ElementEvent
{
	public function onPriceUpdate($setupId, $iblockId, $offerIblockId, $priceId, $fields)
	{
		$productId = null;

		if (isset($fields['PRODUCT_ID']))
		{
			$productId = $fields['PRODUCT_ID'];
		}
		else
		{
			$productId = static::getPriceProductId($priceId);
		}

		if (
			!static::isElementChangeRegistered($setupId, $productId)
			&& static::isTargetElement($iblockId, $offerIblockId, $productId)
		)
		{
			static::registerElementChange($setupId, $productId);
		}
	}

	public function onBeforePriceDelete($setupId, $iblockId, $offerIblockId, $priceId)
	{
		$productId = static::getPriceProductId($priceId);

		if (
			!static::isElementChangeRegistered($setupId, $productId)
			&& static::isTargetElement($iblockId, $offerIblockId, $productId)
		)
		{
			static::registerElementChange($setupId, $productId);
		}
	}

	public function onEntityAfterUpdate($setupId, $iblockId, $offerIblockId, Main\Event $event)
	{
		$this->onPriceUpdate(
			$setupId,
			$iblockId,
			$offerIblockId,
			$event->getParameter('id'),
			$event->getParameter('fields')
		);
	}

	public function onEntityDelete($setupId, $iblockId, $offerIblockId, Main\Event $event)
	{
		$this->onBeforePriceDelete(
			$setupId,
			$iblockId,
			$offerIblockId,
			$event->getParameter('id')
		);
	}

	protected static function getPriceProductId($priceId)
	{
		$result = null;
		$priceId = (int)$priceId;

		if ($priceId > 0 && Main\Loader::includeModule('catalog'))
		{
			$query = \CPrice::GetList(
				[],
				[ '=ID' => $priceId ],
				false,
				false,
				[ 'PRODUCT_ID' ]
			);

			if ($row = $query->Fetch())
			{
				$result = (int)$row['PRODUCT_ID'];
			}
		}

		return $result;
	}

	protected function getEventsForIblock($setupId, $iblockId, $offerIblockId = null)
	{
		$result = null;

		if (Main\Loader::includeModule('catalog') && class_exists('Bitrix\Catalog\Model\Price')) // is new version
		{
			$result = [
				[
					'module' => 'catalog',
					'event' => 'Bitrix\Catalog\Model\Price::OnAfterAdd',
					'method' => 'onEntityAfterUpdate',
					'arguments' => [
						$setupId,
						$iblockId,
						$offerIblockId
					]
				],
				[
					'module' => 'catalog',
					'event' => 'Bitrix\Catalog\Model\Price::OnAfterUpdate',
					'method' => 'onEntityAfterUpdate',
					'arguments' => [
						$setupId,
						$iblockId,
						$offerIblockId
					]
				],
				[
					'module' => 'catalog',
					'event' => 'Bitrix\Catalog\Model\Price::OnDelete',
					'method' => 'onEntityDelete',
					'arguments' => [
						$setupId,
						$iblockId,
						$offerIblockId
					]
				]
			];
		}
		else
		{
			$result = [
				[
					'module' => 'catalog',
					'event' => 'OnPriceAdd',
					'method' => 'onPriceUpdate',
					'arguments' => [
						$setupId,
						$iblockId,
						$offerIblockId
					]
				],
				[
					'module' => 'catalog',
					'event' => 'OnPriceUpdate',
					'method' => 'onPriceUpdate',
					'arguments' => [
						$setupId,
						$iblockId,
						$offerIblockId
					]
				],
				[
					'module' => 'catalog',
					'event' => 'OnBeforePriceDelete',
					'method' => 'onBeforePriceDelete',
					'arguments' => [
						$setupId,
						$iblockId,
						$offerIblockId
					]
				]
			];
		}

		return $result;
	}
}