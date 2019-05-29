<?php

namespace Yandex\Market\Export\FilterCondition;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class Table extends Market\Reference\Storage\Table
{
	public static function getTableName()
	{
		return 'yamarket_export_filtercondition';
	}

	public static function createIndexes(Main\DB\Connection $connection)
	{
		$tableName = static::getTableName();

		$connection->createIndex($tableName, 'IX_' . $tableName . '_0', [ 'FILTER_ID' ]);
	}

	public static function getUfId()
	{
		return 'YAMARKET_EXPORT_FILTERCONDITION';
	}

	public static function getMap()
	{
		$compareList = Market\Export\Entity\Data::getCompareList();

		return [
			new Main\Entity\IntegerField('ID', [
				'autocomplete' => true,
				'primary' => true
			]),
			new Main\Entity\StringField('FIELD', [
				'required' => true
			]),
			new Main\Entity\EnumField('COMPARE', [
				'required' => true,
				'values' => array_keys($compareList)
			]),
			new Main\Entity\TextField('VALUE', [
				'required' => true,
				'save_data_modification' => [__CLASS__, 'getSaveDataModificationForValue'],
				'fetch_data_modification' => [__CLASS__, 'getFetchDataModificationForValue']
			]),
			new Main\Entity\IntegerField('FILTER_ID', [
				'required' => true
			]),
			new Main\Entity\ReferenceField('FILTER', Market\Export\Filter\Table::getClassName(), [
				'=this.FILTER_ID' => 'ref.ID'
			])
		];
	}

	public static function getSaveDataModificationForValue()
	{
		return [
			[__CLASS__, 'saveDataModificationForValue']
		];
	}

	public static function saveDataModificationForValue($value)
	{
		return serialize($value);
	}

	public static function getFetchDataModificationForValue()
	{
		return [
			[__CLASS__, 'fetchDataModificationForValue']
		];
	}

	public static function fetchDataModificationForValue($value)
	{
		return unserialize($value);
	}

	public static function getFieldEnumTitle($fieldName, $optionValue, Main\Entity\Field $field = null)
	{
		$result = null;

		switch ($fieldName)
		{
			case 'COMPARE':
				$result = Market\Export\Entity\Data::getCompareTitle($optionValue);
			break;
		}

		if ($result === null)
		{
			$result = parent::getFieldEnumTitle($fieldName, $optionValue, $field);
		}

		return $result;
	}

	public static function isValidData($data)
	{
		$result = true;

		if (array_key_exists('FIELD', $data) && trim($data['FIELD']) === '')
		{
			$result = false;
		}

		if (array_key_exists('COMPARE', $data) && trim($data['COMPARE']) === '')
		{
			$result = false;
		}

		if (array_key_exists('VALUE', $data))
		{
			$isValidValue = false;

			if (is_array($data['VALUE']))
			{
				$isValidValue = !empty($data['VALUE']);
			}
			else
			{
				$isValidValue = (trim($data['VALUE']) !== '');
			}

			if (!$isValidValue)
			{
				$result = false;
			}
		}

		return $result;
	}
}
