<?php

namespace Yandex\Market\Export\Xml\Format\YandexMarket;

use Yandex\Market\Export\Xml;

class Book extends VendorModel
{
	public function getDocumentationLink()
	{
		return 'https://yandex.ru/support/partnermarket/export/books.html';
	}

	public function getType()
	{
		return 'book';
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
					new Xml\Tag\Base(['name' => 'publisher', 'required' => true]),
					new Xml\Tag\Base(['name' => 'ISBN']),
					new Xml\Tag\Base(['name' => 'author']),
					new Xml\Tag\Base(['name' => 'series']),
					new Xml\Tag\Base(['name' => 'year', 'value_type' => 'number']),
					new Xml\Tag\Base(['name' => 'volume', 'value_type' => 'number']),
					new Xml\Tag\Base(['name' => 'part', 'value_type' => 'number']),
					new Xml\Tag\Base(['name' => 'language']),
					new Xml\Tag\Base(['name' => 'table_of_contents']),
					new Xml\Tag\Base(['name' => 'binding']),
					new Xml\Tag\Base(['name' => 'page_extent', 'value_type' => 'number']),
				],
				$this->getOfferDefaultChildren('epilog', [
					'age' => [ 'required' => true ]
				])
			)
		]);
	}
}
