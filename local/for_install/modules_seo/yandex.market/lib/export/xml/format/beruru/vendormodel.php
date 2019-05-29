<?php

namespace Yandex\Market\Export\Xml\Format\BeruRu;

use Yandex\Market\Export\Xml;

class VendorModel extends Xml\Format\YandexMarket\VendorModel
{
	public function getOffer()
	{
		$tag = parent::getOffer();

		$tag->addChildren([
			new Xml\Tag\Base(['name' => 'shop-sku', 'required' => true]),
			new Xml\Tag\Base(['name' => 'market-sku', 'required' => true]),
			new Xml\Tag\Base(['name' => 'disabled', 'value_type' => 'boolean'])
		]);

		return $tag;
	}
}