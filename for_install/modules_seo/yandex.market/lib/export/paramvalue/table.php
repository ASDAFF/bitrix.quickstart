<?php

namespace Yandex\Market\Export\ParamValue;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class Table extends Market\Reference\Storage\Table
{
	const SOURCE_TYPE_RECOMMENDATION = 'recommendation';

	const XML_TYPE_VALUE = 'value';
	const XML_TYPE_ATTRIBUTE = 'attribute';

	public static function getTableName()
	{
		return 'yamarket_export_paramvalue';
	}

	public static function createIndexes(Main\DB\Connection $connection)
	{
		$tableName = static::getTableName();

		$connection->createIndex($tableName, 'IX_' . $tableName . '_0', [ 'PARAM_ID' ]);
	}

	public static function getUfId()
	{
		return 'YAMARKET_EXPORT_PARAMVALUE';
	}

	public static function getMap()
	{
		return [
			new Main\Entity\IntegerField('ID', [
				'autocomplete' => true,
				'primary' => true
			]),
			new Main\Entity\IntegerField('PARAM_ID', [
				'required' => true
			]),
			new Main\Entity\ReferenceField('PARAM', Market\Export\Param\Table::getClassName(), [
				'=this.PARAM_ID' => 'ref.ID'
			]),
			new Main\Entity\EnumField('XML_TYPE', [
				'required' => true,
				'values' => [
					static::XML_TYPE_VALUE,
					static::XML_TYPE_ATTRIBUTE
				],
				'default' => static::XML_TYPE_VALUE
			]),
			new Main\Entity\StringField('XML_ATTRIBUTE_NAME', [
				'default_value' => ''
			]),
			new Main\Entity\StringField('SOURCE_TYPE', [
				'required' => true
			]),
			new Main\Entity\StringField('SOURCE_FIELD', [
				'required' => true,
				'default_value' => ''
			])
		];
	}

	public static function isValidData($data)
	{
		$result = true;

		if (array_key_exists('SOURCE_FIELD', $data) && trim($data['SOURCE_FIELD']) === '')
		{
			$result = false;
		}

		return $result;
	}
}