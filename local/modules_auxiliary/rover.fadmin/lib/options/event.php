<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 07.12.2016
 * Time: 2:53
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Options;

use \Bitrix\Main\Event as BxEvent;
use \Bitrix\Main\EventManager;
use Bitrix\Main\EventResult;
use Rover\Fadmin\Inputs\Input;
use Rover\Fadmin\Options;

/**
 * Class Event
 *
 * @package Rover\Fadmin\Engine
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Event
{
	/**
	 * @var string
	 */
	public $options;

	/**
	 * @param Options $options
	 */
	public function __construct(Options $options)
	{
		$this->options = $options;
	}

	/**
	 * @param       $name
	 * @param array $params
	 * @param null  $sender
	 * @return BxEvent
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function send($name, array $params = array(), $sender = null)
	{
		$event = new BxEvent($this->options->getModuleId(), $name, $params);
		$event->send($sender);

		return $event;
	}

	/**
	 * @param $name
	 * @param $callback
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function addHandler($name, $callback)
	{
		$eventManager = EventManager::getInstance();
		$eventManager->addEventHandler($this->options->getModuleId(), $name, $callback);
	}

	/**
	 * @param       $name
	 * @param array $params
	 * @param null  $sender
	 * @return bool|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getResult($name, array $params = array(), $sender = null)
	{
		$event = $this->send($name, $params, $sender);

		foreach ($event->getResults() as $eventResult)
		{
			//check by sender
			$parameters = $eventResult->getParameters();
			if (!isset($parameters['handler'])
				|| ($parameters['handler'] !== $sender))
			continue;

			$resultType = $eventResult->getType();
			if ($resultType == EventResult::ERROR)
				return false;

			return $eventResult->getParameters();
		}
	}

	/**
	 * @param $handler
	 * @return EventResult
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getErrorResult($handler)
	{
		return new EventResult(EventResult::ERROR,
			array('handler' => $handler), $this->options->getModuleId());
	}

	/**
	 * @param       $handler
	 * @param array $params
	 * @return EventResult
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getSuccessResult($handler, array $params = array())
	{
		$params['handler'] = $handler;

		return new EventResult(EventResult::SUCCESS,
			$params, $this->options->getModuleId());
	}

	/**
	 * @param BxEvent $event
	 * @param Input   $handler
	 * @return bool
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function checkInputHandler(\Bitrix\Main\Event $event, Input $handler)
	{
		$sender = $event->getSender();

		if (false === $sender instanceof Input)
			return false;

		/**
		 * @var Input $sender
		 */
		echo $sender->getValueName() . ' ' . $handler->getValueName() . '<br>';

		return $sender->getValueName() === $handler->getValueName();
	}
}