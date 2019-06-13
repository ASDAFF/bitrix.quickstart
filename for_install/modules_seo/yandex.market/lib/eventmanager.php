<?php

namespace Yandex\Market;

use Bitrix\Main;
use Yandex\Market;

class EventManager extends Market\Reference\Event\Base
{
	public static function callMethod($className, $method)
	{
		$arguments = array_slice(func_get_args(), 2);
		$result = null;

		if (!empty($arguments))
		{
			$result = call_user_func_array([ $className, $method ], $arguments);
		}
		else
		{
			$result = $className::$method();
		}

		return $result;
	}

	public static function callExportSource($type, $method)
	{
		$arguments = array_slice(func_get_args(), 2);

		try
		{
			$event = Market\Export\Entity\Manager::getEvent($type);

			call_user_func_array([$event, $method], $arguments);
		}
		catch (Main\SystemException $exception)
		{
			// TODO
		}
	}

	public static function callUiUpdateCheck($method)
	{
		$arguments = array_slice(func_get_args(), 1);

		call_user_func_array(
			[ '\Yandex\Market\Ui\Update\Check\Event', $method ],
			$arguments
		);
	}
}