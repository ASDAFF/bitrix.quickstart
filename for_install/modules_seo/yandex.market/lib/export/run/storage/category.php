<?php

namespace Yandex\Market\Export\Run\Storage;

use Bitrix\Main;
use Yandex\Market;

class CategoryTable extends Market\Reference\Storage\Table
{
	public static function getTableName()
	{
		return 'yamarket_export_run_category';
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
			new Main\Entity\IntegerField('ELEMENT_ID', [
				'required' => true,
				'primary' => true
			]),
			new Main\Entity\StringField('HASH', [
				'validation' => [__CLASS__, 'validateHash'],
				'size' => 33 // md5
			]),
			new Main\Entity\StringField('STATUS', [
				'validation' => [__CLASS__, 'validateStatus'],
				'size' => 1
			]),
			new Main\Entity\DatetimeField('TIMESTAMP_X', [
				'required' => true
			])
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