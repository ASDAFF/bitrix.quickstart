<?php

namespace Yandex\Market\Ui\UserField\ServiceCategory;

use Yandex\Market;
use Bitrix\Main;

class Event extends Market\Reference\Event\Regular
{
	public static function getHandlers()
	{
		return [
			[
				'module' => 'main',
				'event' => 'OnUserTypeBuildList'
			],
			[
				'module' => 'iblock',
				'event' => 'OnIBlockPropertyBuildList'
			]
		];
	}

	public static function OnUserTypeBuildList()
	{
		return Field::GetUserTypeDescription();
	}

	public static function OnIBlockPropertyBuildList()
	{
		return Property::GetUserTypeDescription();
	}
}