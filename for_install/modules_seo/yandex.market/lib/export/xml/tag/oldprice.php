<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;

class OldPrice extends Price
{
	public function getDefaultParameters()
	{
		return [ 'name' => 'oldprice' ] + parent::getDefaultParameters();
	}

	public function validate($value, array $context, $siblingsValues = null, Market\Result\XmlNode $nodeResult = null, $settings = null)
	{
		$result = parent::validate($value, $context, $siblingsValues, $nodeResult);

		if ($result)
		{
			$priceValue = $this->getTagValues($siblingsValues, 'price', false);

			if (!isset($priceValue['VALUE']) || (float)$priceValue['VALUE'] >= (float)$value)
			{
				$result = false; // is not error, silent skip element
			}
		}

		return $result;
	}

	public function getSourceRecommendation(array $context = [])
	{
		$result = parent::getSourceRecommendation($context);

		foreach ($result as &$recommendation)
		{
			$recommendation['FIELD'] = str_replace('.DISCOUNT_VALUE', '.VALUE', $recommendation['FIELD']);
		}
		unset($recommendation);

		return $result;
	}
}