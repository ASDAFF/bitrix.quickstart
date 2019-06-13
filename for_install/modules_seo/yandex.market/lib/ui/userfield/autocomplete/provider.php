<?php

namespace Yandex\Market\Ui\UserField\Autocomplete;

abstract class Provider
{
	abstract public static function searchByName($searchQuery);

	abstract public static function getList();

	abstract public static function getPropertyValue($property, $value);

	public static function getClassName()
	{
		return '\\' . get_called_class();
	}

	public static function getValueForAutoComplete($property, $value)
	{
		$result = '';
		$propertyValue = static::getPropertyValue($property, $value);

		if ($propertyValue !== null)
		{
			$result = $propertyValue['NAME'] . ' [' . $propertyValue['ID'] . ']';
		}

		return $result;
	}

	public static function getValueForAutoCompleteMulti($property, $valueList)
	{
		$result = [];
		$valueList = (array)$valueList;

		foreach ($valueList as $valueKey => $value)
		{
			$valueText = static::getValueForAutoComplete($property, $value);

			if ($valueText !== '')
			{
				$result[] = $valueText;
			}
		}

		return $result;
	}
}