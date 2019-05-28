<?php

namespace Yandex\Market\Type;

use Yandex\Market;

class UrlType extends AbstractType
{
	public function format($value, array $context = [], Market\Export\Xml\Reference\Node $node = null, Market\Result\XmlNode $nodeResult = null)
	{
		$result = $value;

		if (strpos($result, '://') === false) // is not absolute url
		{
			$result = $context['DOMAIN_URL'] . $result;
		}

		$result = str_replace('&', '&amp;', $result); // escape xml entities

		return $result;
	}
}