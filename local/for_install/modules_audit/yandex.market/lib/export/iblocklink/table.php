<?php

namespace Yandex\Market\Export\IblockLink;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class Table extends Market\Reference\Storage\Table
{
	public static function getTableName()
	{
		return 'yamarket_export_iblocklink';
	}

	public static function createIndexes(Main\DB\Connection $connection)
	{
		$tableName = static::getTableName();

		$connection->createIndex($tableName, 'IX_' . $tableName . '_0', [ 'SETUP_ID' ]);
	}

	public static function getUfId()
	{
		return 'YAMARKET_EXPORT_IBLOCKLINK';
	}

	public static function getMap()
	{
		return [
			new Main\Entity\IntegerField('ID', [
				'autocomplete' => true,
				'primary' => true
			]),
			new Main\Entity\IntegerField('SETUP_ID', [
				'required' => true
			]),
			new Main\Entity\ReferenceField('SETUP', Market\Export\Setup\Table::getClassName(), [
				'=this.SETUP_ID' => 'ref.ID'
			]),
			new Main\Entity\IntegerField('IBLOCK_ID', [
				'required' => true
			]),
			new Main\Entity\ReferenceField('IBLOCK_LINK', 'Bitrix\Iblock\Iblock', [
				'=this.IBLOCK_ID' => 'ref.ID'
			]),
			new Main\Entity\StringField('SALES_NOTES'),
			new Main\Entity\BooleanField('EXPORT_ALL', [
				'values' => [ '0', '1' ],
				'default_value' => '1',
			]),
			new Main\Entity\ReferenceField('DELIVERY', Market\Export\Delivery\Table::getClassName(), [
				'=this.ID' => 'ref.ENTITY_ID',
				'=ref.ENTITY_TYPE' => [ '?', Market\Export\Delivery\Table::ENTITY_TYPE_IBLOCK_LINK ]
			]),
			new Main\Entity\ReferenceField('FILTER', Market\Export\Filter\Table::getClassName(), [
				'=this.ID' => 'ref.IBLOCK_LINK_ID'
			]),
			new Main\Entity\ReferenceField('PARAM', Market\Export\Param\Table::getClassName(), [
				'=this.ID' => 'ref.IBLOCK_LINK_ID'
			]),
		];
	}

	public static function getReference($primary = null)
	{
		return [
			'FILTER' => [
				'TABLE' => Market\Export\Filter\Table::getClassName(),
				'LINK_FIELD' => 'IBLOCK_LINK_ID',
				'LINK' => [
					'IBLOCK_LINK_ID' => $primary
				],
				'ORDER' => [
					'SORT' => 'asc',
					'ID' => 'asc'
				]
			],
			'PARAM' => [
				'TABLE' => Market\Export\Param\Table::getClassName(),
				'LINK_FIELD' => 'IBLOCK_LINK_ID',
				'LINK' => [
					'IBLOCK_LINK_ID' => $primary
				]
			],
			'DELIVERY' => [
				'TABLE' => Market\Export\Delivery\Table::getClassName(),
				'LINK_FIELD' => 'ENTITY_ID',
				'LINK' => [
					'ENTITY_TYPE' => Market\Export\Delivery\Table::ENTITY_TYPE_IBLOCK_LINK,
					'ENTITY_ID' => $primary
				]
			]
		];
	}
}