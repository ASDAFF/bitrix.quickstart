<?php

namespace Yandex\Market\Ui\UserField;

use Yandex\Market;

class LogType extends EnumerationType
{
	protected static $optionCache = null;

	function GetAdminListViewHTML($arUserField, $arHtmlControl)
	{
		$result = '';
		$option = static::getOption($arUserField, $arHtmlControl['VALUE']);

		if ($option)
		{
			$imgType = 'green';

			if (isset($option['LOG_LEVEL']))
			{
				switch ($option['LOG_LEVEL'])
				{
					case Market\Psr\Log\LogLevel::CRITICAL:
					case Market\Psr\Log\LogLevel::EMERGENCY:
					case Market\Psr\Log\LogLevel::ALERT:
						$imgType = 'red';
					break;

					default:
						$imgType = 'yellow';
					break;
				}
			}

			$result .= '<img class="b-log-icon" src="/bitrix/images/yandex.market/' .  $imgType . '.gif" width="14" height="14" alt="" />';
			$result .= $option['VALUE'];
		}

		return $result;
	}

	protected static function getOption($arUserField, $id)
	{
		$result = false;

		if (static::$optionCache === null)
		{
			static::$optionCache = [];

			$query = call_user_func([ $arUserField['USER_TYPE']['CLASS_NAME'], 'GetList' ], $arUserField);

			while ($option = $query->fetch())
			{
				static::$optionCache[$option['ID']] = $option;

				if ($option['ID'] == $id)
				{
					$result = $option;
				}
			}
		}
		else if (isset(static::$optionCache[$id]))
		{
			$result = static::$optionCache[$id];
		}

		return $result;
	}
}