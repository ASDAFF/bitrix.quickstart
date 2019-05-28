<?php

namespace Yandex\Market\Ui\UserField\ServiceCategory;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class Field extends Market\Ui\UserField\Autocomplete\Field
{
	public static function GetUserTypeDescription()
	{
		$langKey = static::getLangKey();

		return [
			'USER_TYPE_ID' => 'ym_service_category',
			'CLASS_NAME'   => __CLASS__,
			'DESCRIPTION'  => Market\Config::getLang($langKey . 'DESCRIPTION'),
			'BASE_TYPE'    => 'int'
		];
	}

	public static function GetDBColumnType($userField)
	{
		$connection = Main\Application::getConnection();
		$result = null;

		switch ($connection->getType())
		{
			case 'mysql':
				$result = 'int(18)';
			break;

			case 'oracle':
				$result = 'number(18)';
			break;

			case 'mssql':
				$result = 'int';
			break;
		}

		return $result;
	}

	public static function getDataProvider()
	{
		return Provider::getClassName();
	}

	public static function getLangKey()
	{
		return 'UI_USERFIELD_SERVICECATEGORY_FIELD_';
	}
}