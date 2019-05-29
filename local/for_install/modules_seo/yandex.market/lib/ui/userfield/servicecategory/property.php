<?php

namespace Yandex\Market\Ui\UserField\ServiceCategory;

use Yandex\Market;
use Bitrix\Main;
use Bitrix\Iblock;

Main\Localization\Loc::loadMessages(__FILE__);

class Property extends Market\Ui\UserField\Autocomplete\Property
{
	public static function GetUserTypeDescription()
	{
		$langKey = static::getLangKey();

		return [
			'PROPERTY_TYPE' => 'N',
			'USER_TYPE' => 'ym_service_category',
			'DESCRIPTION' => Market\Config::getLang($langKey . 'DESCRIPTION'),
			'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],
			'GetPropertyFieldHtmlMulty' => [__CLASS__,'GetPropertyFieldHtmlMulty'],
			'GetAdminListViewHTML' => [__CLASS__,'GetAdminListViewHTML'],
			'GetAdminFilterHTML' => [__CLASS__,'GetAdminFilterHTML'],
			'GetPublicFilterHTML' => [__CLASS__,'GetPublicFilterHTML'],
			'GetSettingsHTML' => [__CLASS__,'GetSettingsHTML'],
			'PrepareSettings' => [__CLASS__,'PrepareSettings'],
			'AddFilterFields' => [__CLASS__,'AddFilterFields'],
		];
	}

	public static function getDataProvider()
	{
		return Provider::getClassName();
	}

	public static function getLangKey()
	{
		return 'UI_USERFIELD_SERVICECATEGORY_PROPERTY_';
	}

	public static function AddFilterFields($property, $controlName, &$filter, &$isApplied)
	{
		$isApplied = false;
		$requestValueList = static::GetFilterRequestValue($controlName);
		$propertyFilter = [];

		foreach ($requestValueList as $value)
		{
			$valueInteger = (int)$value;
			
			if ($valueInteger > 0)
			{
				$propertyFilter[] = $valueInteger;
			}
		}

		if (!empty($propertyFilter))
		{
			$filter['=PROPERTY_' . $property['ID']] = $propertyFilter;
			$isApplied = true;
		}
	}
}