<?php

namespace Yandex\Market\Export\Xml\Format\YandexMarket;

use Yandex\Market\Export\Xml;

class ArtistTitle extends VendorModel
{
	public function getDocumentationLink()
	{
		return 'https://yandex.ru/support/partnermarket/export/music-video.html';
	}

	public function getType()
	{
		return 'artist.title';
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
				new Xml\Attribute\Available(['visible' => true]),
				new Xml\Attribute\Base(['name' => 'bid', 'value_type' => 'number']),
			],
			'children' => array_merge(
				$this->getOfferDefaultChildren('prolog'),
				[
					new Xml\Tag\Base(['name' => 'artist']),
					new Xml\Tag\Base(['name' => 'title', 'required' => true]),
					new Xml\Tag\Base(['name' => 'year', 'value_type' => 'number']),
					new Xml\Tag\Base(['name' => 'media']),
					new Xml\Tag\Base(['name' => 'starring']),
					new Xml\Tag\Base(['name' => 'director']),
					new Xml\Tag\Base(['name' => 'originalName']),
					new Xml\Tag\Base(['name' => 'country']),
				],
				$this->getOfferDefaultChildren('epilog')
			)
		]);
	}
}
