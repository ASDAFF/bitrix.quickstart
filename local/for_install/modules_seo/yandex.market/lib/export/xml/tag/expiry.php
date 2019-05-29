<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;

class Expiry extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'expiry',
			'value_type' => Market\Type\Manager::TYPE_DATEPERIOD,
			'date_format' => 'Y-m-d\TH:i'
		];
	}
}