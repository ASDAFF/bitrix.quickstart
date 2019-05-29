<?php

namespace Yandex\Market\Export\Entity\Catalog\Store;

use Bitrix\Iblock;
use Bitrix\Main;
use Yandex\Market;

class Event extends Market\Export\Entity\Reference\ElementEvent
{
	public function onStoreProductUpdate($setupId, $iblockId, $offerIblockId, $amountId, $fields)
	{
		$productId = null;

		if (isset($fields['PRODUCT_ID']))
		{
			$productId = $fields['PRODUCT_ID'];
		}
		else
		{
			$productId = static::getCatalogStoreProductId($amountId);
		}

		if (
			!static::isElementChangeRegistered($setupId, $productId)
			&& static::isTargetElement($iblockId, $offerIblockId, $productId)
		)
		{
			static::registerElementChange($setupId, $productId);
		}
	}

	public function onBeforeStoreProductDelete($setupId, $iblockId, $offerIblockId, $amountId)
	{
		$productId = static::getCatalogStoreProductId($amountId);

		if (
			!static::isElementChangeRegistered($setupId, $productId)
			&& static::isTargetElement($iblockId, $offerIblockId, $productId)
		)
		{
			static::registerElementChange($setupId, $productId);
		}
	}

	protected static function getCatalogStoreProductId($amountId)
	{
		$result = null;
		$amountId = (int)$amountId;

		if ($amountId > 0 && Main\Loader::includeModule('catalog'))
		{
			$query = \CCatalogStoreProduct::GetList(
				[],
				[ '=ID' => $amountId ],
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
		return [
			[
				'module' => 'catalog',
				'event' => 'OnStoreProductAdd',
				'method' => 'onStoreProductUpdate',
				'arguments' => [
					$setupId,
					$iblockId,
					$offerIblockId
				]
			],
			[
				'module' => 'catalog',
				'event' => 'OnStoreProductUpdate',
				'method' => 'onStoreProductUpdate',
				'arguments' => [
					$setupId,
					$iblockId,
					$offerIblockId
				]
			],
			[
				'module' => 'catalog',
				'event' => 'OnBeforeStoreProductDelete',
				'method' => 'onBeforeStoreProductDelete',
				'arguments' => [
					$setupId,
					$iblockId,
					$offerIblockId
				]
			]
		];
	}
}