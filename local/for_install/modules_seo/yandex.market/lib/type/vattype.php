<?php

namespace Yandex\Market\Type;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class VatType extends AbstractType
{
	protected static $availableValues = [
		'VAT_18' => true,
		'VAT_18_118' => true,
		'VAT_10' => true,
		'VAT_10_110' => true,
		'VAT_0' => true,
		'NO_VAT' => true,
		'1' => true,
		'2' => true,
		'3' => true,
		'4' => true,
		'5' => true,
		'6' => true
	];

	public function validate($value, array $context = [], Market\Export\Xml\Reference\Node $node = null, Market\Result\XmlNode $nodeResult = null)
	{
		$result = true;
		$value = $this->convertValue($value);

		if (!isset(static::$availableValues[$value]))
		{
			$result = false;

			if ($nodeResult)
			{
				$nodeResult->registerError(Market\Config::getLang('TYPE_VAT_ERROR_INVALID'));
			}
		}

		return $result;
	}

	public function format($value, array $context = [], Market\Export\Xml\Reference\Node $node = null, Market\Result\XmlNode $nodeResult = null)
	{
		return $this->convertValue($value);
	}

	protected function convertValue($value)
	{
		$result = strtoupper(trim($value));

		if (isset(static::$availableValues[$result]))
		{
			// nothing
		}
		else if (is_numeric($result))
		{
			$valueInteger = (int)$result;

			if ($valueInteger > 0)
			{
				$result = 'VAT_' . $valueInteger;
			}
			else
			{
				$result = 'NOT_VAT';
			}
		}

		return $result;
	}
}