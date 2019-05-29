<?php

namespace Yandex\Market\Ui\UserField;

class LogRowType extends \CUserTypeString
{
	function GetAdminListViewHTML($arUserField, $arHtmlControl)
	{
		$result = '';
		$value = null;

		if (!empty($arHtmlControl['VALUE']))
		{
			$value = $arHtmlControl['VALUE'];
		}
		else if (!empty($arUserField['VALUE']))
		{
			$value = $arUserField['VALUE'];
		}

		if (!empty($value['MESSAGE']))
		{
			$result = $value['MESSAGE'];
		}

		return $result;
	}

	function GetAdminListViewHtmlMulty($arUserField, $arHtmlControl)
	{
		$result = '';

		if (!empty($arUserField['VALUE']))
		{
			$result = null;

			foreach ($arUserField['VALUE'] as $value)
			{
				$valueHtml = static::GetAdminListViewHTML($arUserField, [
					'VALUE' => $value
				]);

				if ($result === null)
				{
					$result = $valueHtml;
				}
				else
				{
					$result .= '<br />' . $valueHtml;
				}
			}
		}

		return $result;
	}
}