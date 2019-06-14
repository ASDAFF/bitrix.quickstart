<?php

namespace Yandex\Market\Ui\Update\Check;

use Yandex\Market;
use Bitrix\Main;

class Event
{
	public static function listenModuleUpdate($dir = true)
	{
		$params = [
			'module' => 'main',
			'event' => 'OnModuleUpdate',
			'method' => 'callUiUpdateCheck',
			'arguments' => [ 'OnModuleUpdate' ]
		];

		return $dir ? Market\EventManager::register($params) : Market\EventManager::unregister($params);
	}

	public static function OnModuleUpdate($readyModules)
	{
		if (is_array($readyModules) && in_array(Market\Config::getModuleName(), $readyModules))
		{
			\CAdminNotify::DeleteByTag(Agent::NOTIFY_TAG);

			static::listenModuleUpdate(false);
		}
	}
}