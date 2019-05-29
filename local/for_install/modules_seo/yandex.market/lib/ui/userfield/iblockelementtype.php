<?php

namespace Yandex\Market\Ui\UserField;

use Bitrix\Main;

class IblockElementType extends \CUserTypeString
{
	protected static $elementNameCache = [];

	function GetAdminListViewHTML($arUserField, $arHtmlControl)
	{
		$result = '';

		if (strlen($arHtmlControl['VALUE']) > 0)
		{
			$elementName = static::getIblockElementName($arHtmlControl['VALUE']);

			$result = '[' . $arHtmlControl['VALUE'] . '] ' . $elementName;
		}

		return $result;
	}

	protected static function getIblockElementName($id)
	{
		$result = false;
		$id = (int)$id;

		if ($id <= 0)
		{
			// nothing
		}
		else if (isset(static::$elementNameCache[$id]))
		{
			$result = static::$elementNameCache[$id];
		}
		else if (Main\Loader::includeModule('iblock'))
		{
			$query = \CIBlockElement::GetList(
				[],
				[ '=ID' => $id ],
				false,
				false,
				[ 'NAME' ]
			);

			if ($item = $query->Fetch())
			{
				$result = $item['NAME'];
			}

			static::$elementNameCache[$id] = $result;
		}

		return $result;
	}
}