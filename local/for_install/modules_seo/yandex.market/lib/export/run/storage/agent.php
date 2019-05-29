<?php

namespace Yandex\Market\Export\Run\Storage;

use Bitrix\Main;
use Yandex\Market;

class AgentTable extends Market\Reference\Storage\Table
{
	public static function getTableName()
	{
		return 'yamarket_export_run_agent';
	}

	public static function getMap()
	{
		return [
			new Main\Entity\StringField('METHOD', [
				'required' => true,
				'primary' => true,
				'size' => 15,
				'validation' => [__CLASS__, 'validateMethod']
			]),
			new Main\Entity\IntegerField('SETUP_ID', [
				'primary' => true,
				'required' => true
			]),
			new Main\Entity\StringField('STEP', [
				'size' => 15,
				'validation' => [__CLASS__, 'validateStep']
			]),
			new Main\Entity\StringField('OFFSET', [
				'size' => 20,
				'validation' => [__CLASS__, 'validateOffset']
			]),
			new Main\Entity\DatetimeField('START_TIME')
		];
	}

	public static function validateMethod()
	{
		return [
			new Main\Entity\Validator\Length(null, 15)
		];
	}

	public static function validateStep()
	{
		return [
			new Main\Entity\Validator\Length(null, 15)
		];
	}

	public static function validateOffset()
	{
		return [
			new Main\Entity\Validator\Length(null, 20)
		];
	}
}