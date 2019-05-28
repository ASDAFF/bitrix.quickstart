<?php

namespace Yandex\Market\Export\Xml\Format\YandexMarket;

use Bitrix\Main;
use Yandex\Market\Export\Xml;

class VendorModel extends Xml\Format\Reference\Base
{
	public function getDocumentationLink()
	{
		return 'https://yandex.ru/support/partnermarket/export/vendor-model.html';
	}

	public function getType()
	{
		return 'vendor.model';
	}

	public function isSupportDeliveryOptions()
	{
		return true;
	}

	public function getHeader()
	{
		$encoding = Main\Application::isUtfMode() ? 'utf-8' : 'windows-1251';

		$result = '<?xml version="1.0" encoding="' . $encoding . '"?>';
		$result .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">';

		return $result;
	}

	public function getRoot()
	{
		$result = new Xml\Tag\Base([
			'name' => 'yml_catalog',
			'attributes' => [
				new Xml\Attribute\Base([ 'name' => 'date', 'value_type' => 'date', 'default_value' => time() ])
			],
			'children' => [
				new Xml\Tag\Base([
					'name' => 'shop',
					'children' => [
						new Xml\Tag\ShopName(),
						new Xml\Tag\ShopCompany(),
						new Xml\Tag\ShopUrl(),
						new Xml\Tag\ShopPlatform(),
						new Xml\Tag\ShopPlatformVersion(),
						new Xml\Tag\EnableAutoDiscounts(['global' => true]),
						new Xml\Tag\Base([
							'name' => 'categories',
							'default_value' => ' ',
						]),
						new Xml\Tag\Base([
							'name' => 'currencies',
							'default_value' => ' ',
						]),
						new Xml\Tag\Base([
							'name' => 'offers',
							'default_value' => ' ',
						]),
					],
				]),
			],
		]);

		if ($this->isSupportDeliveryOptions())
		{
			$rootChidren = $result->getChildren();
			$shopTag = reset($rootChidren);

			$shopTag->addChild(new Xml\Tag\DeliveryOptions(), -1);
			$shopTag->addChild(new Xml\Tag\PickupOptions(), -1);
		}

		return $result;
	}

	public function getCategoryParentName()
	{
		return 'categories';
	}

	public function getCategory()
	{
		return new Xml\Tag\Base([
			'name' => 'category',
			'attributes' => [
				new Xml\Attribute\Base(['name' => 'id', 'required' => true]),
				new Xml\Attribute\Base(['name' => 'parentId']),
			],
		]);
	}

	public function getCurrencyParentName()
	{
		return 'currencies';
	}

	public function getCurrency()
	{
		return new Xml\Tag\Base([
			'name' => 'currency',
			'empty_value' => true,
			'attributes' => [
				new Xml\Attribute\Base(['name' => 'id', 'value_type' => 'currency', 'required' => true]),
				new Xml\Attribute\Base(['name' => 'rate', 'required' => true]),
			],
		]);
	}

	public function getOfferParentName()
	{
		return 'offers';
	}

	/**
	 * @return Xml\Tag\Base
	 */
	public function getOffer()
	{
		return new Xml\Tag\Base([
			'name' => 'offer',
			'required' => true,
			'visible' => true,
			'attributes' => [
				new Xml\Attribute\Id(['required' => true]),
				new Xml\Attribute\Type(['required' => true]),
				new Xml\Attribute\Available(['value_type' => 'boolean', 'visible' => true]),
				new Xml\Attribute\Base(['name' => 'bid', 'value_type' => 'number']),
				new Xml\Attribute\Base(['name' => 'group_id', 'value_type' => 'number']),
			],
			'children' => array_merge(
				$this->getOfferDefaultChildren('prolog', [
					'picture' => [ 'required' => true ]
				]),
				[
					new Xml\Tag\Vendor(['required' => true]),
					new Xml\Tag\Model(['required' => true]),
					new Xml\Tag\Base(['name' => 'vendorCode', 'visible' => true]),
					new Xml\Tag\Base(['name' => 'typePrefix', 'visible' => true]),
				],
				$this->getOfferDefaultChildren('epilog', [
					'barcode' => [ 'visible' => true ]
				])
			)
		]);
	}

	protected function getOfferDefaultChildren($place, $overrides = null, $sort = null, $excludeList = null)
	{
		$result = [];

		switch ($place)
		{
			case 'prolog':
				$result = [
					new Xml\Tag\Url(['required' => true]),
					new Xml\Tag\Price(['required' => true]),
					new Xml\Tag\OldPrice(),
					new Xml\Tag\EnableAutoDiscounts(),
					new Xml\Tag\Vat(),
					new Xml\Tag\CurrencyId(['required' => true]),
					new Xml\Tag\CategoryId(['required' => true]),
					new Xml\Tag\Picture(['multiple' => true, 'visible' => true]),
					new Xml\Tag\Base(['name' => 'delivery', 'value_type' => 'boolean']),
					new Xml\Tag\Base(['name' => 'pickup', 'value_type' => 'boolean']),
					new Xml\Tag\Base(['name' => 'store', 'value_type' => 'boolean']),
				];

				if ($this->isSupportDeliveryOptions())
				{
					array_splice($result, -2, 0, [
					    new Xml\Tag\DeliveryOptions(),
					    new Xml\Tag\PickupOptions(),
                    ]);
				}
			break;

			case 'epilog':
				$result = [
					new Xml\Tag\Description(['visible' => true]),
					new Xml\Tag\SalesNotes(),
					new Xml\Tag\Base(['name' => 'min-quantity', 'value_type' => 'number']),
					new Xml\Tag\Base(['name' => 'manufacturer_warranty', 'value_type' => 'boolean']),
					new Xml\Tag\Base(['name' => 'country_of_origin']),
					new Xml\Tag\Base(['name' => 'adult', 'value_type' => 'boolean']),
					new Xml\Tag\Base(['name' => 'barcode', 'multiple' => true]),
					new Xml\Tag\Param([
						'multiple' => true,
						'visible' => true,
						'attributes' => [
							new Xml\Attribute\Base(['name' => 'name', 'required' => true, 'visible' => true]),
							new Xml\Attribute\Base(['name' => 'unit']),
						],
					]),
					new Xml\Tag\Expiry(),
					new Xml\Tag\Weight(),
					new Xml\Tag\Base(['name' => 'dimensions']),
					new Xml\Tag\Base(['name' => 'downloadable', 'value_type' => 'boolean']),
					new Xml\Tag\Base([
						'name' => 'age',
						'attributes' => [
							new Xml\Attribute\Base(['name' => 'unit', 'visible' => true]),
						],
					]),
				];
			break;
		}

		$this->overrideTags($result, $overrides);
		$this->excludeTags($result, $excludeList);
		$this->sortTags($result, $sort);

		return $result;
	}

	protected function overrideTags($tags, $overrides)
	{
		if ($overrides !== null)
		{
			/** @var \Yandex\Market\Export\Xml\Tag\Base $tag */
			foreach ($tags as $tag)
			{
				$tagName = $tag->getName();

				if (isset($overrides[$tagName]))
				{
					$tag->extendParameters($overrides[$tagName]);
				}
			}
		}
	}

	protected function sortTags(&$tags, $sort)
	{
		if ($sort !== null)
		{
			$fullSort = [];
			$nextSortIndex = 10;

			foreach ($tags as $tag)
			{
				$tagId = $tag->getId();
				$fullSort[$tagId] = isset($sort[$tagId]) ? $sort[$tagId] : $nextSortIndex;

				$nextSortIndex += 10;
			}

			uasort($tags, function($tagA, $tagB) use ($fullSort) {
				$tagAId = $tagA->getId();
				$tagBId = $tagB->getId();
				$tagASort = $fullSort[$tagAId];
				$tagBSort = $fullSort[$tagBId];

				if ($tagASort === $tagBSort) { return 0; }

				return ($tagASort < $tagBSort ? -1 : 1);
			});
		}
	}

	protected function excludeTags(&$tags, $excludeList)
	{
		if ($excludeList !== null)
		{
			foreach ($tags as $tagIndex => $tag)
			{
				$tagName = $tag->getName();

				if (isset($excludeList[$tagName]))
				{
					unset($tags[$tagIndex]);
				}
			}
		}
	}
}
