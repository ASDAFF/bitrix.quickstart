<?php

namespace Yandex\Market\Logger;

use Bitrix\Main;
use Yandex\Market;
use Yandex\Market\Psr\Log;

Main\Localization\Loc::loadMessages(__FILE__);

class Table extends Market\Reference\Storage\Table
{
	const ENTITY_TYPE_EXPORT_RUN_ROOT = 'export_run_root';
	const ENTITY_TYPE_EXPORT_RUN_OFFER = 'export_run_offer';
	const ENTITY_TYPE_EXPORT_RUN_CATEGORY = 'export_run_category';
	const ENTITY_TYPE_EXPORT_RUN_CURRENCY = 'export_run_currency';
	const ENTITY_TYPE_EXPORT_AGENT = 'export_agent';

	public static function getTableName()
	{
		return 'yamarket_log';
	}

	public static function createIndexes(Main\DB\Connection $connection)
	{
		$tableName = static::getTableName();

		$connection->createIndex($tableName, 'IX_' . $tableName . '_0', [ 'ENTITY_TYPE', 'ENTITY_PARENT' ]);
		$connection->createIndex($tableName, 'IX_' . $tableName . '_1', [ 'ENTITY_ID' ]);
	}

	public static function getUfId()
	{
		return 'YAMARKET_LOG';
	}

	public static function getMap()
	{
		return [
			new Main\Entity\IntegerField('ID', [
				'autocomplete' => true,
				'primary' => true,
			]),
			new Main\Entity\DatetimeField('TIMESTAMP_X', [
				'required' => true,
			]),
			new Main\Entity\EnumField('LEVEL', [
				'values' => [
					Log\LogLevel::ALERT,
					Log\LogLevel::CRITICAL,
					Log\LogLevel::DEBUG,
					Log\LogLevel::EMERGENCY,
					Log\LogLevel::ERROR,
					Log\LogLevel::INFO,
					Log\LogLevel::NOTICE,
					Log\LogLevel::WARNING,
				],
				'required' => true,
			]),
			new Main\Entity\StringField('MESSAGE', [
				'required' => true,
				'size' => 255
			]),
			new Main\Entity\IntegerField('ERROR_CODE'),
			new Main\Entity\StringField('ENTITY_TYPE', [
				'size' => 20,
				'validation' => [__CLASS__, 'validateEntityType'],
			]),
			new Main\Entity\IntegerField('ENTITY_PARENT'),
			new Main\Entity\StringField('ENTITY_ID', [
				'size' => 20,
				'validation' => [__CLASS__, 'validateEntityId'],
			]),
			new Main\Entity\StringField('CONTEXT', [
				'serialized' => true
			]),

			// OFFER_ID

			new Main\Entity\ReferenceField('RUN_OFFER', Market\Export\Run\Storage\OfferTable::getClassName(), [
				'=this.ENTITY_PARENT' => 'ref.SETUP_ID',
				'=this.ENTITY_ID' => 'ref.ELEMENT_ID',
				'=this.ENTITY_TYPE' => [ '?', static::ENTITY_TYPE_EXPORT_RUN_OFFER ],
			]),

			new Main\Entity\ExpressionField('OFFER_ID', '%s', 'RUN_OFFER.ELEMENT_ID'),

			new Main\Entity\ReferenceField('SETUP', Market\Export\Setup\Table::getClassName(), [
				'=this.ENTITY_PARENT' => 'ref.ID'
			]),
		];
	}

	public static function validateEntityType()
	{
		return [
			new Main\Entity\Validator\Length(null, 20)
		];
	}

	public static function validateEntityId()
	{
		return [
			new Main\Entity\Validator\Length(null, 20)
		];
	}

	public static function getMapDescription()
	{
		$result = parent::getMapDescription();

		if (!empty($result['LEVEL']['VALUES']))
		{
			$result['LEVEL']['USER_TYPE']['CLASS_NAME'] = 'Yandex\Market\Ui\UserField\LogType';
			$allowedTypes = [
				Log\LogLevel::CRITICAL => true,
				Log\LogLevel::WARNING => true
			];

			foreach ($result['LEVEL']['VALUES'] as $optionKey => &$option)
			{
				if (isset($allowedTypes[$option['ID']]))
				{
					$option['LOG_LEVEL'] = $option['ID'];
				}
				else
				{
					unset($result['LEVEL']['VALUES'][$optionKey]);
				}
			}
			unset($option);
		}

		if (isset($result['SETUP']))
		{
			$result['SETUP']['USER_TYPE']['CLASS_NAME'] = 'Yandex\Market\Ui\UserField\SetupType';
		}

		if (isset($result['OFFER_ID']))
		{
			$result['OFFER_ID']['USER_TYPE']['CLASS_NAME'] = 'Yandex\Market\Ui\UserField\IblockElementType';
		}

		return $result;
	}
}
