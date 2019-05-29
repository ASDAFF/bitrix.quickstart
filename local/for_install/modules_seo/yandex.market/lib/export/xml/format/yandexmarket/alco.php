<?php

namespace Yandex\Market\Export\Xml\Format\YandexMarket;

use Yandex\Market\Export\Xml;

class Alco extends Simple
{
	public function getDocumentationLink()
	{
		return 'https://yandex.ru/support/partnermarket/export/alcohol.html';
	}

	public function getType()
	{
		return 'alco';
	}

	public function isSupportDeliveryOptions()
	{
		return false;
	}

	public function getOffer()
	{
		$result = parent::getOffer();

		$result->addAttribute(new Xml\Attribute\Type(['required' => true]), 1);

		return $result;
	}

	protected function getOfferDefaultChildren($place, $overrides = null, $sort = null, $excludeList = null)
	{
		$result = null;

		switch ($place)
		{
			case 'epilog':
				// extend overrides

				if ($overrides === null) { $overrides = []; }

				$overrides['barcode'] = [ 'required' => true ];

				// get defaults

				$result = parent::getOfferDefaultChildren($place, $overrides, null, array_merge((array)$excludeList, [
					'adult' => true,
					'min-quantity' => true
				]));

				// add new tag

				$result[] = new Xml\Tag\ParamVolume([
					'required' => true,
					'attributes' => [
						new Xml\Attribute\VolumeName(['required' => true, 'visible' => true]),
						new Xml\Attribute\VolumeUnit(['required' => true]),
					],
				]);

				// sort

				if ($sort === null) { $sort = []; }

				$sort['param_volume'] = 55; // before default params

				$this->sortTags($result, $sort);
			break;

			default:
				// extend overrides

				if ($overrides === null) { $overrides = []; }

				$overrides['delivery'] = [ 'defined_value' => 'N' ];

				// get defaults

				$result = parent::getOfferDefaultChildren($place, $overrides, $sort, $excludeList);
			break;
		}

		return $result;
	}
}
