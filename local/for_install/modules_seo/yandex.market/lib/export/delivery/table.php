<?php

namespace Yandex\Market\Export\Delivery;

use Bitrix\Main;
use Yandex\Market;

class Table extends Market\Reference\Storage\Table
{
	const ENTITY_TYPE_SETUP = 'setup';
	const ENTITY_TYPE_IBLOCK_LINK = 'iblock';
	const ENTITY_TYPE_FILTER = 'filter';

	const DELIVERY_TYPE_DELIVERY = 'delivery';
	const DELIVERY_TYPE_PICKUP = 'pickup';

	public static function getTableName()
	{
		return 'yamarket_export_delivery';
	}

	public static function createIndexes(Main\DB\Connection $connection)
	{
		$tableName = static::getTableName();

		$connection->createIndex($tableName, 'IX_' . $tableName . '_0', [ 'ENTITY_TYPE', 'ENTITY_ID' ]);
	}

	public static function getUfId()
	{
		return 'YAMARKET_EXPORT_DELIVERY';
	}

	public static function getMap()
	{
		return [
			new Main\Entity\IntegerField('ID', [
				'autocomplete' => true,
				'primary' => true
			]),
			new Main\Entity\StringField('NAME'),
			new Main\Entity\StringField('PERIOD_FROM', [
				'size' => 4
			]),
			new Main\Entity\StringField('PERIOD_TO', [
				'size' => 4
		    ]),
			new Main\Entity\StringField('ORDER_BEFORE', [
				'size' => 4
			]),
			new Main\Entity\IntegerField('PRICE', [
				'required' => true
			]),
			new Main\Entity\EnumField('DELIVERY_TYPE', [
				'required' => true,
				'default_value' => static::DELIVERY_TYPE_DELIVERY,
				'values' => [
					static::DELIVERY_TYPE_DELIVERY,
					static::DELIVERY_TYPE_PICKUP
				]
			]),
			new Main\Entity\EnumField('ENTITY_TYPE', [
				'required' => true,
				'values' => [
					static::ENTITY_TYPE_SETUP,
					static::ENTITY_TYPE_IBLOCK_LINK,
					static::ENTITY_TYPE_FILTER
				]
			]),
			new Main\Entity\IntegerField('ENTITY_ID', [
				'required' => true
			]),
			new Main\Entity\ReferenceField('SETUP', Market\Export\Setup\Table::getClassName(), [
				'=this.ENTITY_ID' => 'ref.ID',
				'=this.ENTITY_TYPE' => [ '?', static::ENTITY_TYPE_SETUP ]
			]),
			new Main\Entity\ReferenceField('IBLOCK_LINK', Market\Export\IblockLink\Table::getClassName(), [
				'=this.ENTITY_ID' => 'ref.ID',
				'=this.ENTITY_TYPE' => [ '?', static::ENTITY_TYPE_IBLOCK_LINK ]
			]),
			new Main\Entity\ReferenceField('FILTER', Market\Export\Filter\Table::getClassName(), [
				'=this.ENTITY_ID' => 'ref.ID',
				'=this.ENTITY_TYPE' => [ '?', static::ENTITY_TYPE_FILTER ]
			])
		];
	}

	public static function isValidData($data)
	{
		$result = true;

		if (array_key_exists('PRICE', $data) && ((int)$data['PRICE'] < 0 || trim($data['PRICE']) === ''))
		{
			$result = false;
		}

		if (array_key_exists('PERIOD_FROM', $data) && array_key_exists('PERIOD_TO', $data))
		{
			$hasPeriodFrom = true;
			$hasPeriodTo = true;

			if ((int)$data['PERIOD_FROM'] < 0 || trim($data['PERIOD_FROM']) === '')
			{
				$hasPeriodFrom = false;
			}

			if ((int)$data['PERIOD_TO'] < 0 || trim($data['PERIOD_TO']) === '')
			{
				$hasPeriodTo = false;
			}

			if (!$hasPeriodFrom && !$hasPeriodTo)
			{
				$result = false;
			}
		}

		return $result;
	}
}