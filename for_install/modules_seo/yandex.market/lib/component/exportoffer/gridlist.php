<?php

namespace Yandex\Market\Component\ExportOffer;

use Yandex\Market;

class GridList extends Market\Component\Data\GridList
{
	public function getDefaultFilter()
	{
		$elementId = (int)$this->getComponentParam('ELEMENT_ID') ?: -1;

		return [
			[
				'LOGIC' => 'OR',
				[ '=ELEMENT_ID' => $elementId ],
				[ '=PARENT_ID' => $elementId ]
			]
		];
	}
}