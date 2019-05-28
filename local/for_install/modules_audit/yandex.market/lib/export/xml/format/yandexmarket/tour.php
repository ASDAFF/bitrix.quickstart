<?php

namespace Yandex\Market\Export\Xml\Format\YandexMarket;

use Yandex\Market\Export\Xml;

class Tour extends VendorModel
{
	public function getDocumentationLink()
	{
		return 'https://yandex.ru/support/partnermarket/export/tours.html';
	}

	public function getType()
	{
		return 'tour';
	}

	/**
	 * @return Xml\Tag\Base
	 */
	public function getOffer()
	{
		return new Xml\Tag\Base([
			'name' => 'offer',
			'required' => true,
			'attributes' => [
				new Xml\Attribute\Id(['required' => true]),
				new Xml\Attribute\Type(['required' => true]),
				new Xml\Attribute\Base(['name' => 'bid', 'value_type' => 'number']),
				new Xml\Attribute\Available(['visible' => true]),
			],
			'children' => array_merge(
				$this->getOfferDefaultChildren('prolog'),
				[
					new Xml\Tag\Name(['required' => true]),
					new Xml\Tag\Base(['name' => 'worldRegion']),
					new Xml\Tag\Base(['name' => 'country']),
					new Xml\Tag\Base(['name' => 'region']),
					new Xml\Tag\Base(['name' => 'days', 'required' => true, 'value_type' => 'number']),
					new Xml\Tag\Base(['name' => 'dataTour', 'multiple' => true, 'value_type' => 'date']),
					new Xml\Tag\Base(['name' => 'hotel_stars']),
					new Xml\Tag\Base(['name' => 'room']),
					new Xml\Tag\Base(['name' => 'meal']),
					new Xml\Tag\Base(['name' => 'included', 'required' => true]),
					new Xml\Tag\Base(['name' => 'transport', 'required' => true]),
					new Xml\Tag\Base(['name' => 'price_min', 'value_type' => 'number']),
					new Xml\Tag\Base(['name' => 'price_max', 'value_type' => 'number']),
					new Xml\Tag\Base(['name' => 'options']),
				],
				$this->getOfferDefaultChildren('epilog')
			)
		]);
	}
}
