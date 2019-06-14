<?php

namespace Yandex\Market\Export\Xml\Format\YandexMarket;

use Yandex\Market\Export\Xml;

class Medicine extends VendorModel
{
	public function getDocumentationLink()
	{
		return 'https://yandex.ru/support/partnermarket/export/medicine.html';
	}

	public function getType()
	{
		return 'medicine';
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
				new Xml\Attribute\Available(['required' => true]),
				new Xml\Attribute\Base(['name' => 'bid', 'value_type' => 'number']),
			],
			'children' => array_merge(
				$this->getOfferDefaultChildren('prolog', [
					'delivery' => [ 'required' => true ],
					'pickup' => [ 'required' => true ],
				]),
				[
					new Xml\Tag\Name(['required' => true]),
					new Xml\Tag\Vendor(),
					new Xml\Tag\Base(['name' => 'vendorCode']),
				],
				$this->getOfferDefaultChildren('epilog', null, null, [
					'min-quantity' => true
				])
			)
		]);
	}
}
