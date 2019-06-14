<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;

class ParamVolume extends Param
{
	public function getDefaultParameters()
	{
		return [
			'id' => 'param_volume',
			'value_type' => Market\Type\Manager::TYPE_NUMBER,
			'value_precision' => 3
		] + parent::getDefaultParameters();
	}
}
