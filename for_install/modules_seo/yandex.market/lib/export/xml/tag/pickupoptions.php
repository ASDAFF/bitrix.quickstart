<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;

class PickupOptions extends DeliveryOptions
{
    public function getDefaultParameters()
	{
		return [
		    'name' => 'pickup-options'
        ];
	}

	public function getDefaultValue(array $context = [], $siblingsValues = null)
	{
		return !empty($context['DELIVERY_OPTIONS']['pickup']) ? $context['DELIVERY_OPTIONS']['pickup'] : null;
	}
}