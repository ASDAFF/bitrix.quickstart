<?php

namespace Yandex\Market\Export\Run;

use Bitrix\Main;
use Yandex\Market;

class Manager
{
	const STEP_ROOT = 'root';
	const STEP_OFFER = 'offer';
	const STEP_CURRENCY = 'currency';
	const STEP_CATEGORY = 'category';

	const ENTITY_TYPE_ROOT = 'root';
	const ENTITY_TYPE_OFFER = 'offer';
	const ENTITY_TYPE_CATEGORY = 'category';
	const ENTITY_TYPE_CURRENCY = 'currency';

	protected static $registeredAgentMethods = [];
	protected static $registeredChanges = [];

	/**
	 * @return String[]
	 */
	public static function getSteps()
	{
		return [
			static::STEP_ROOT,
			static::STEP_OFFER,
			static::STEP_CATEGORY,
			static::STEP_CURRENCY
		];
	}

	public static function getStepsWeight()
	{
		return [
			static::STEP_ROOT => 10,
			static::STEP_OFFER => 70,
			static::STEP_CATEGORY => 10,
			static::STEP_CURRENCY => 10
		];
	}

	/**
	 * @param $stepName
	 * @param Processor $processor
	 *
	 * @return Steps\Base
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function getStepProvider($stepName, Processor $processor)
	{
		$result = null;

		switch ($stepName)
		{
			case static::STEP_ROOT:
				$result = new Steps\Root($processor);
			break;

			case static::STEP_OFFER:
				$result = new Steps\Offer($processor);
			break;

			case static::STEP_CATEGORY:
				$result = new Steps\Category($processor);
			break;

			case static::STEP_CURRENCY:
				$result = new Steps\Currencies($processor);
			break;

			default:
				throw new Main\SystemException('not found export run step');
			break;
		}

		return $result;
	}

	public static function isChangeRegistered($setupId, $entityType, $entityId)
	{
		$changeKey = $setupId . ':' . $entityType . ':' . $entityId;

		return isset(static::$registeredChanges[$changeKey]);
	}

	public static function registerChange($setupId, $entityType, $entityId)
	{
		$changeKey = $setupId . ':' . $entityType . ':' . $entityId;

		if (!isset(static::$registeredChanges[$changeKey]))
		{
			static::$registeredChanges[$changeKey] = true;

			$queryExists = Storage\ChangesTable::getList([
				'filter' => [
					'=SETUP_ID' => $setupId,
					'=ENTITY_TYPE' => $entityType,
					'=ENTITY_ID' => $entityId
				]
			]);

			if ($queryExists->fetch())
			{
				Storage\ChangesTable::update(
					[
						'SETUP_ID' => $setupId,
						'ENTITY_TYPE' => $entityType,
						'ENTITY_ID' => $entityId
					],
					[
						'TIMESTAMP_X' => new Main\Type\DateTime()
					]
				);
			}
			else
			{
				Storage\ChangesTable::add([
					'SETUP_ID' => $setupId,
					'ENTITY_TYPE' => $entityType,
					'ENTITY_ID' => $entityId,
					'TIMESTAMP_X' => new Main\Type\DateTime()
				]);
			}

			static::registerAgent('change');
		}
	}

	protected static function registerAgent($method)
	{
		if (!isset(static::$registeredAgentMethods[$method]))
		{
			static::$registeredAgentMethods[$method] = true;

			Agent::register([
				'method' => $method
			]);
		}
	}
}