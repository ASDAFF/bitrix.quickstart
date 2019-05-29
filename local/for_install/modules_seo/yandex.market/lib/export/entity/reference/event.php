<?php

namespace Yandex\Market\Export\Entity\Reference;

use Yandex\Market;

abstract class Event
{
	protected $type;

	public function setType($type)
	{
		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	public function handleChanges($direction, $context)
	{
		$events = $this->getEvents($context);

		foreach ($events as $event)
		{
			$managerEvent = $event;
			$managerArguments = [
				$this->getType(),
				isset($event['method']) ? $event['method'] : $event['event']
			];

			if (isset($event['arguments']))
			{
				foreach ($event['arguments'] as $argument)
				{
					$managerArguments[] = $argument;
				}
			}

			$managerEvent['arguments'] = $managerArguments;
			$managerEvent['method'] = 'callExportSource';

			if ($direction)
			{
				Market\EventManager::register($managerEvent);
			}
			else
			{
				Market\EventManager::unregister($managerEvent);
			}
		}
	}

	protected function getEvents($context)
	{
		return [];
	}
}