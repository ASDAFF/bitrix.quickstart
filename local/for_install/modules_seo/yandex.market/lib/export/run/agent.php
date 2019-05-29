<?php

namespace Yandex\Market\Export\Run;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class Agent extends Market\Reference\Agent\Base
{
	protected static $offsetStorageIds = [];
	protected static $timeLimit;
	protected static $startTime;

	public static function getDefaultParams()
	{
		return [
			'interval' => 60
		];
	}

	public static function change()
	{
		$isNeedRepeatAgent = false;

		if (static::isTimeExpired())
		{
			$isNeedRepeatAgent = true;
		}
		else
		{
			$setupIds = static::getSetupIds();
			$method = 'change';

			foreach ($setupIds as $setupId)
			{
				$changes = static::getSetupChanges($setupId);
				$changesBySource = static::groupChangesByType($changes);
				$state = static::getState($method, $setupId);
				$isFinished = false;
				$isError = false;
				$progressStep = null;
				$progressOffset = null;
				$startTime = !empty($state['START_TIME']) ? $state['START_TIME'] : new Main\Type\DateTime();

				try
				{
					/** @var \Yandex\Market\Export\Setup\Model $setup */
					$setup = Market\Export\Setup\Model::loadById($setupId);

					if (!$setup->isFileReady())
					{
						$progressStep = isset($state['STEP']) ? $state['STEP'] : null;
						$progressOffset = isset($state['OFFSET']) ? $state['OFFSET'] : null;
					}
					else
					{
						$processor = new Market\Export\Run\Processor($setup, [
							'changes' => $changesBySource,
							'step' => isset($state['STEP']) ? $state['STEP'] : null,
							'stepOffset' => isset($state['OFFSET']) ? $state['OFFSET'] : null,
							'timeLimit' => static::getTimeLimit(),
							'usePublic' => true,
							'initTime' => $startTime
						]);

						$processResult = $processor->run('change');

						if ($processResult->isFinished())
						{
							$isFinished = true;
						}
						else if (!$processResult->isSuccess())
						{
							$isError = true;
						}
						else
						{
							$progressStep = $processResult->getStep();
							$progressOffset = $processResult->getStepOffset();
						}
					}
				}
				catch (Main\SystemException $exception)
				{
					$isError = true;
				}

				if ($isFinished || $isError)
				{
					static::releaseChanges($changes);
					static::releaseState($method, $setupId, $state ?: false);
				}
				else
				{
					$isNeedRepeatAgent = true;
					static::setState($method, $setupId, $progressStep, $progressOffset, $startTime, $state);
				}

				$isNeedRepeatAgent = true;

				if (static::isTimeExpired()) { break; }
			}
		}

		return $isNeedRepeatAgent;
	}

	public static function refreshStart($setupId)
	{
		static::register([
			'method' => 'refresh',
			'arguments' => [ (int)$setupId ]
		]);
	}

	public static function refresh($setupId)
	{
		$isNeedRepeatAgent = false;

		if (!Market\Utils::isCli())
		{
			$logger = new Market\Logger\Logger();
			$existLogList = $logger->getExists(
				Market\Logger\Table::ENTITY_TYPE_EXPORT_AGENT,
				$setupId,
				'refresh'
			);
			$existLog = reset($existLogList);

			$logger->critical(Market\Config::getLang('EXPORT_RUN_AGENT_REFRESH_ONLY_CLI'), [
				'ENTITY_TYPE' => Market\Logger\Table::ENTITY_TYPE_EXPORT_AGENT,
				'ENTITY_PARENT' => $setupId,
				'ENTITY_ID' => 'refresh',
				'LOG_ID' => isset($existLog['ID']) ? $existLog['ID'] : null
			]);
		}
		else if (static::isTimeExpired())
		{
			$isNeedRepeatAgent = true;
		}
		else
		{
			$method = 'refresh';
			$state = static::getState($method, $setupId);
			$startTime = !empty($state['START_TIME']) ? $state['START_TIME'] : new Main\Type\DateTime();
			$isFinished = false;
			$isError = false;
			$progressStep = null;
			$progressOffset = null;

			try
			{
				$setup = Market\Export\Setup\Model::loadById($setupId);
				$processor = new Market\Export\Run\Processor($setup, [
					'step' => isset($state['STEP']) ? $state['STEP'] : null,
					'stepOffset' => isset($state['OFFSET']) ? $state['OFFSET'] : null,
					'timeLimit' => static::getTimeLimit(),
					'initTime' => $startTime,
					'usePublic' => true
				]);

				$processResult = $processor->run('refresh');

				if ($processResult->isFinished())
				{
					$isFinished = true;
				}
				else if (!$processResult->isSuccess())
				{
					$isError = true;
				}
				else
				{
					$progressStep = $processResult->getStep();
					$progressOffset = $processResult->getStepOffset();
				}
			}
			catch (Main\SystemException $exception)
			{
				$isError = true;
			}

			if ($isFinished || $isError)
			{
				static::releaseState($method, $setupId, $state ?: false);
			}
			else
			{
				$isNeedRepeatAgent = true;
				static::setState($method, $setupId, $progressStep, $progressOffset, $startTime, $state);
			}
		}

		return $isNeedRepeatAgent;
	}

	protected static function getSetupIds()
	{
		$result = [];

		$query = Storage\ChangesTable::getList([
			'group' => [ 'SETUP_ID' ],
			'select' => [ 'SETUP_ID' ]
		]);

		while ($row = $query->fetch())
		{
			$setupId = (int)$row['SETUP_ID'];

			if ($setupId > 0)
			{
				$result[] = $setupId;
			}
		}

		return $result;
	}

	protected static function getSetupChanges($setupId)
	{
		$result = [];
		$limit = Market\Config::getOption('export_run_agent_changes_limit', 1000);

		$query = Storage\ChangesTable::getList([
			'filter' => [
				'=SETUP_ID' => $setupId
			],
			'select' => [
				'SETUP_ID',
				'ENTITY_TYPE',
				'ENTITY_ID'
		    ],
		    'order' => [
		        'TIMESTAMP_X' => 'asc'
		    ],
			'limit' => $limit
		]);

		while ($row = $query->fetch())
		{
			$result[] = $row;
		}

		return $result;
	}

	protected static function releaseChanges($changes)
	{
		foreach ($changes as $change)
		{
			Storage\ChangesTable::delete([
				'SETUP_ID' => $change['SETUP_ID'],
				'ENTITY_TYPE' => $change['ENTITY_TYPE'],
				'ENTITY_ID' => $change['ENTITY_ID'],
			]);
		}
	}

	protected static function groupChangesByType($changes)
	{
		$result = [];

		foreach ($changes as $change)
		{
			if (!isset($result[$change['ENTITY_TYPE']]))
			{
				$result[$change['ENTITY_TYPE']] = [];
			}

			$result[$change['ENTITY_TYPE']][] = $change['ENTITY_ID'];
		}

		return $result;
	}

	protected static function getState($method, $setupId)
	{
		$result = null;

		$query = Storage\AgentTable::getList([
			'filter' => [
				'=METHOD' => $method,
				'=SETUP_ID' => $setupId
			]
		]);

		if ($row = $query->fetch())
		{
			$result = $row;
		}

		return $result;
	}

	public static function setState($method, $setupId, $step, $offset, $startTime, $currentState = null)
	{
		$fields = [
			'METHOD' => $method,
			'SETUP_ID' => $setupId,
			'STEP' => $step !== null ? $step : '',
			'OFFSET' => $offset !== null ? $offset : '',
			'START_TIME' => $startTime
		];

		if (isset($currentState))
		{
			Storage\AgentTable::update(
				[
					'METHOD' => $method,
					'SETUP_ID' => $setupId
				],
				$fields
			);
		}
		else
		{
			Storage\AgentTable::add($fields);
		}
	}

	public static function releaseState($method, $setupId, $currentState = null)
	{
		$isExists = false;

		if ($currentState !== null)
		{
			$isExists = !empty($currentState);
		}
		else
		{
			$state = static::getState($method, $setupId);

			$isExists = !empty($state);
		}

		if ($isExists)
		{
			Storage\AgentTable::update(
				[
					'METHOD' => $method,
					'SETUP_ID' => $setupId
				],
				[
					'STEP' => '',
					'OFFSET' => '',
					'START_TIME' => ''
				]
			);
		}
	}

	protected static function isTimeExpired()
	{
		$limit = static::getTimeLimit();
		$diff = 0;

		if (static::$startTime === null)
		{
			$diff = 0;
			static::$startTime = microtime(true);
		}
		else
		{
			$diff = microtime(true) - static::$startTime;
		}

		return ($limit > 0 && $diff >= $limit);
	}

	protected static function getTimeLimit()
	{
		$result = null;

		if (static::$timeLimit !== null)
		{
			$result = static::$timeLimit;
		}
		else
		{
			$maxTime = (int)ini_get('max_execution_time') * 0.75;

			if (Market\Utils::isCli())
			{
				$result = (int)Market\Config::getOption('export_run_agent_time_limit_cli', 30);
			}
			else
			{
				$result = (int)Market\Config::getOption('export_run_agent_time_limit', 5);
			}

			if ($maxTime > 0 && $result > $maxTime)
			{
				$result = $maxTime;
			}

			static::$timeLimit = $result;
		}

		return $result;
	}
}