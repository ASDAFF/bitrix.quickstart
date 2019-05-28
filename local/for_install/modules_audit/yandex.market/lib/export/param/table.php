<?php

namespace Yandex\Market\Export\Param;

use Bitrix\Main;
use Yandex\Market;

class Table extends Market\Reference\Storage\Table
{
	public static function getTableName()
	{
		return 'yamarket_export_param';
	}

	public static function createIndexes(Main\DB\Connection $connection)
	{
		$tableName = static::getTableName();

		$connection->createIndex($tableName, 'IX_' . $tableName . '_0', [ 'IBLOCK_LINK_ID' ]);
	}

	public static function getUfId()
	{
		return 'YAMARKET_EXPORT_PARAM';
	}

	public static function getMap()
	{
		return [
			new Main\Entity\IntegerField('ID', [
				'autocomplete' => true,
				'primary' => true
			]),
			new Main\Entity\IntegerField('IBLOCK_LINK_ID', [
				'required' => true
			]),
			new Main\Entity\ReferenceField('IBLOCK_LINK', Market\Export\IblockLink\Table::getClassName(), [
				'=this.IBLOCK_LINK_ID' => 'ref.ID'
			]),
			new Main\Entity\StringField('XML_TAG', [
				'required' => true
			]),
			new Main\Entity\ReferenceField('PARAM_VALUE', Market\Export\ParamValue\Table::getClassName(), [
				'=this.ID' => 'ref.PARAM_ID'
			]),
			new Main\Entity\TextField('SETTINGS', [
				'serialized' => true
			])
		];
	}

	public static function getReference($primary = null)
	{
		return [
			'PARAM_VALUE' => [
				'TABLE' => Market\Export\ParamValue\Table::getClassName(),
				'LINK_FIELD' => 'PARAM_ID',
				'LINK' => [
					'PARAM_ID' => $primary
				]
			]
		];
	}
}