<?php

namespace Yandex\Market\Export\FilterCondition;

use Bitrix\Main;
use Yandex\Market;

class Model extends Market\Reference\Storage\Model
{
	public static function getDataClass()
	{
		return Table::getClassName();
	}

	public function isValid()
	{
		$dataClass = static::getDataClass();
		$fields = $this->getFields();

		return $dataClass::isValidData($fields);
	}

	/**
	 * @return string
	 * @throws \Bitrix\Main\SystemException
	 */
	public function getQueryCompare()
	{
		$compareField = $this->getField('COMPARE');
		$compareType = Market\Export\Entity\Data::getCompare($compareField);

		return $compareType['QUERY'];
	}

	public function getQueryField()
	{
		$field = $this->getField('FIELD');
		$dotPosition = strpos($field, '.');

		if ($dotPosition === false)
		{
			throw new Main\SystemException('not valid field format');
		}

		return substr($field, $dotPosition + 1);
	}

	public function getSourceName()
	{
		$field = $this->getField('FIELD');
		$dotPosition = strpos($field, '.');

		if ($dotPosition === false)
		{
			throw new Main\SystemException('not valid field format');
		}

		return substr($field, 0, $dotPosition);
	}

	public function getQueryValue()
	{
		$value = $this->getField('VALUE');

		if (is_array($value) && count($value) === 1)
		{
			$value = reset($value);
		}

		if ($value === Market\Export\Entity\Data::SPECIAL_VALUE_EMPTY)
		{
			$value = false;
		}

		return $value;
	}
}
