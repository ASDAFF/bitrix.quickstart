<?php

namespace Yandex\Market\Export\Run\Storage;

use Bitrix\Main;
use Yandex\Market;

class ChangesTable extends Market\Reference\Storage\Table
{
	public static function getTableName()
	{
		return 'yamarket_export_run_changes';
	}

	public static function getMap()
	{
		return [
			new Main\Entity\IntegerField('SETUP_ID', [
				'required' => true,
				'primary' => true
			]),
			new Main\Entity\StringField('ENTITY_TYPE', [
				'required' => true,
				'primary' => true,
				'size' => 20,
				'validation' => [__CLASS__, 'validateEntityType'],
			]),
			new Main\Entity\StringField('ENTITY_ID', [
				'required' => true,
				'primary' => true,
				'size' => 20,
				'validation' => [__CLASS__, 'validateEntityId'],
			]), // may be currency id and bigInt
			new Main\Entity\DatetimeField('TIMESTAMP_X', [
				'required' => true
			])
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
}