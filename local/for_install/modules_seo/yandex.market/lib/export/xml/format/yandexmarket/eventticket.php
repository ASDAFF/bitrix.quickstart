<?php

namespace Yandex\Market\Export\Xml\Format\YandexMarket;

use Yandex\Market\Export\Xml;

class EventTicket extends VendorModel
{
	public function getDocumentationLink()
	{
		return 'https://yandex.ru/support/partnermarket/export/event-tickets.html';
	}

	public function getType()
	{
		return 'event-ticket';
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
					new Xml\Tag\Base(['name' => 'place', 'required' => true]),
					new Xml\Tag\Base(['name' => 'hall']),
					new Xml\Tag\Base(['name' => 'hall_part']),
					new Xml\Tag\Base(['name' => 'date', 'required' => true, 'value_type' => 'date']),
					new Xml\Tag\Base(['name' => 'is_premiere', 'value_type' => 'boolean']),
					new Xml\Tag\Base(['name' => 'is_kids', 'value_type' => 'boolean']),
				],
				$this->getOfferDefaultChildren('epilog')
			)
		]);
	}
}
