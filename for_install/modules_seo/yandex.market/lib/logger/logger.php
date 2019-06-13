<?php
namespace Yandex\Market\Logger;

use Bitrix\Main\Type\DateTime;
use Yandex\Market\Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed  $level
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function log($level, $message, array $context = array())
	{
		if (isset($context['LOG_ID']))
		{
			Table::update($context['LOG_ID'], [
				'TIMESTAMP_X' => new DateTime()
			]);
		}
		else
		{
			$entityType = null;
			$entityParent = null;
			$entityId = null;
			$errorCode = null;

			if (array_key_exists('LOG_ID', $context))
			{
				unset($context['LOG_ID']);
			}

			if (array_key_exists('ENTITY_TYPE', $context))
			{
				$entityType = $context['ENTITY_TYPE'];
				unset($context['ENTITY_TYPE']);
			}

			if (array_key_exists('ENTITY_PARENT', $context))
			{
				$entityParent = $context['ENTITY_PARENT'];
				unset($context['ENTITY_PARENT']);
			}

			if (array_key_exists('ENTITY_ID', $context))
			{
				$entityId = $context['ENTITY_ID'];
				unset($context['ENTITY_ID']);
			}

			if (array_key_exists('ERROR_CODE', $context))
			{
				$errorCode = $context['ERROR_CODE'];
				unset($context['ERROR_CODE']);
			}

			Table::add([
				'TIMESTAMP_X' => new DateTime(),
				'LEVEL' => $level,
				'MESSAGE' => $message,
				'ENTITY_TYPE' => $entityType,
				'ENTITY_PARENT' => $entityParent ?: '',
				'ENTITY_ID' => $entityId ?: '',
				'ERROR_CODE' => $errorCode ?: '',
				'CONTEXT' => $context,
			]);
		}
	}

	public function getExists($entityType, $entityParent, $entityId)
	{
		$result = [];

		$filter = [
			'=ENTITY_TYPE' => $entityType,
			'=ENTITY_PARENT' => $entityParent
		];

		if ($entityId !== null)
		{
			$filter['=ENTITY_ID'] = $entityId;
		}

		$query = Table::getList([
			'filter' => $filter,
		]);

		while ($row = $query->fetch())
		{
			$result[] = $row;
		}

		return $result;
	}

	public function releaseExists($logId)
	{
		$query = Table::delete($logId);

		return $query->isSuccess();
	}
}
