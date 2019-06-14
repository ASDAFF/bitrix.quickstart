<?php

namespace Yandex\Market\Type;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class CurrencyType extends AbstractType
{
	protected static $convertMap = [
		'RUB' => 'RUR'
	];
	protected static $availableValues = [
		'RUR' => true,
		'USD' => true,
		'EUR' => true,
		'UAH' => true,
		'KZT' => true,
		'BYN' => true
	];
	protected static $baseValues = [
		'RUR' => true,
		'BYN' => true,
		'UAH' => true,
		'KZT' => true
	];

	public function validate($value, array $context = [], Market\Export\Xml\Reference\Node $node = null, Market\Result\XmlNode $nodeResult = null)
	{
		$value = $this->convertValue($value);
		$result = true;

		if (!isset(static::$availableValues[$value]))
		{
			$result = false;

			if ($nodeResult)
			{
				$nodeResult->registerError(Market\Config::getLang('TYPE_CURRENCY_ERROR_INVALID'));
			}
		}

		return $result;
	}

	public function format($value, array $context = [], Market\Export\Xml\Reference\Node $node = null, Market\Result\XmlNode $nodeResult = null)
	{
		return $this->convertValue($value);
	}

	public function getAvailableList()
	{
		return static::$availableValues;
	}

	public function getDefaultBase()
	{
		return Market\Config::getOption('type_currency_default_base', 'RUR');
	}

	public function isBase($value)
	{
		$value = $this->convertValue($value);

		return isset(static::$baseValues[$value]);
	}

	public function getBaseList()
	{
		return static::$baseValues;
	}

	protected function convertValue($value)
	{
		$result = strtoupper($value);

		if (isset(static::$convertMap[$result]))
		{
			$result = static::$convertMap[$result];
		}

		return $result;
	}
}