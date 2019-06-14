<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class Weight extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'weight',
			'value_type' => Market\Type\Manager::TYPE_NUMBER,
			'value_precision' => 3
		];
	}

	public function getSourceRecommendation(array $context = [])
	{
		$result = [];

		if ($context['HAS_CATALOG'])
		{
		    $result[] = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_CATALOG_PRODUCT,
				'FIELD' => 'WEIGHT'
			];
		}

		return $result;
	}

	public function validate($value, array $context, $siblingsValues = null, Market\Result\XmlNode $nodeResult = null, $settings = null)
	{
		$result = true;
		$isEmptyValue = ($value === null || $value === '');

		if (!$isEmptyValue)
		{
			if (!empty($settings['BITRIX_UNIT']))
			{
				$value = $this->convertByUnit($value, $settings['BITRIX_UNIT']);
			}

			$valuePrecision = $this->getParameter('value_precision');
			$value = round($value, $valuePrecision);

			if ((float)$value <= 0)
			{
				$result = false;

				if ($nodeResult)
				{
					$nodeResult->registerError(Market\Config::getLang($this->getLangKey() . '_VALIDATE_WEIGHT_NOT_POSITIVE'));
				}
			}
		}

		if ($result)
		{
			$result = parent::validate($value, $context, $siblingsValues, $nodeResult, $settings);
		}

		return $result;
	}

	protected function formatValue($value, array $context = [], Market\Result\XmlNode $nodeResult = null, $settings = null)
	{
		if (!empty($settings['BITRIX_UNIT']))
		{
			$value = $this->convertByUnit($value, $settings['BITRIX_UNIT']);
		}

		return parent::formatValue($value, $context, $nodeResult, $settings);
	}

	public function getSettingsDescription()
	{
		$langKey = $this->getLangKey();

		$result = [
			'BITRIX_UNIT' => [
				'TITLE' => Market\Config::getLang($langKey . '_SETTINGS_BITRIX_UNIT_TITLE'),
				'TYPE' => 'enumeration',
				'VALUES' => []
			]
		];

		// fill unit

		$unitMap = $this->getUnitMap();

		foreach ($unitMap as $unit => $ratio)
		{
			$result['BITRIX_UNIT']['VALUES'][] = [
				'ID' => $unit,
				'VALUE' => Market\Config::getLang($langKey . '_SETTINGS_BITRIX_UNIT_ENUM_' . strtoupper($unit))
			];
		}

		return $result;
	}

	protected function convertByUnit($value, $unit)
	{
		$map = $this->getUnitMap();
		$result = $value;

		if (isset($map[$unit]))
		{
			$result = $value * $map[$unit];
		}

		return $result;
	}

	protected function getUnitMap()
	{
		return [
			'gram' => 0.001,
			'kilogram' => 1,
			'centner' => 100,
			'ton' => 1000,
			'milligram' => 0.000001
		];
	}
}