<?php

namespace Yandex\Market\Export\Run\Storage;

use Bitrix\Main;
use Yandex\Market;

class CurrencyTable extends Market\Reference\Storage\Table
{
	public static function getTableName()
	{
		return 'yamarket_export_run_currency';
	}

	public static function createIndexes(Main\DB\Connection $connection)
	{
		$tableName = static::getTableName();

		$connection->createIndex($tableName, 'IX_' . $tableName . '_1', [ 'STATUS', 'HASH' ]);
		$connection->createIndex($tableName, 'IX_' . $tableName . '_2', [ 'TIMESTAMP_X' ]);
	}

	public static function getMap()
	{
		return [
			new Main\Entity\IntegerField('SETUP_ID', [
				'required' => true,
				'primary' => true
			]),
			new Main\Entity\StringField('ELEMENT_ID', [
				'required' => true,
				'primary' => true,
				'size' => 15,
				'validation' => [__CLASS__, 'validateElementId'],
			]),
			new Main\Entity\StringField('HASH', [
				'size' => 33, // md5
				'validation' => [__CLASS__, 'validateHash'],
			]),
			new Main\Entity\StringField('STATUS', [
				'size' => 1,
				'validation' => [__CLASS__, 'validateStatus'],
			]),
			new Main\Entity\DatetimeField('TIMESTAMP_X', [
				'required' => true
			])
		];
	}

	public static function validateElementId()
	{
		return [
			new Main\Entity\Validator\Length(null, 15)
		];
	}

	public static function validateHash()
	{
		return [
			new Main\Entity\Validator\Length(null, 33)
		];
	}

	public static function validateStatus()
	{
		return [
			new Main\Entity\Validator\Length(null, 1)
		];
	}
}