<?php
namespace Helper\UserProp;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class Store
{
	private static $OPTION_CACHE_TTL = 36000;

	public static function GetPropertyDescription()
	{
		return array(
			'PROPERTY_TYPE' => 'N',
			'USER_TYPE' => 'ASKARON_PROP_Store',
			'DESCRIPTION' => GetMessage('ASKARON_PROP_STORE_DESCRIPTION'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetAdminFilterHTML' => array(__CLASS__, 'GetAdminFilterHTML'),
			"GetUIFilterProperty" => array(__CLASS__, "GetUIFilterProperty"),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
			'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
//			'GetPublicViewHTML' => array(__CLASS__, 'GetPublicViewHTML'),
//			'GetPublicEditHTML' => array(__CLASS__, 'GetPublicEditHTML'),
			"GetUIEntityEditorProperty" => [__CLASS__,"GetUIEntityEditorProperty"],
//			"GetUIEntityEditorPropertyEditHtml" => [__CLASS__,"GetUIEntityEditorPropertyEditHtml"],
//			"GetUIEntityEditorPropertyViewHtml" => [__CLASS__,"GetUIEntityEditorPropertyViewHtml"],
		);
	}

//	public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName)
//	{
//		return "!!edit ".htmlspecialcharsbx($value);
//	}
//	public static function GetPublicViewHTML($value)
//	{
//		return "!!view ".htmlspecialcharsbx($value);
//	}

	public static function GetUIEntityEditorProperty($settings, $value)
	{
		$items = [];

		$arAllItems = self::GetAllItems();
		foreach ($arAllItems as $key => $arItem)
		{
			$items[] = [
				'NAME' => "[".$arItem['ID']."] ".htmlspecialcharsbx($arItem['TITLE']),
				'VALUE' => $arItem['ID'],
				'ID' => $arItem['ID'],
			];
		}

//		foreach (\CIBlockPropertyElementList::GetElements($settings['LINK_IBLOCK_ID']) as $element)
//		{
//			$items[] = [
//				'NAME' => $element['NAME'],
//				'VALUE' => $element['ID'],
//				'ID' => $element['ID'],
//			];
//		}

		return [
			'type' => ($settings['MULTIPLE'] === 'Y') ? 'multilist' : 'list',
			'data' => [
				//'isProductProperty' => true,
				'enableEmptyItem' => true,
				'items' => $items
			]
		];
	}


//	public static function GetUIEntityEditorProperty($settings, $value)
//	{
//		return [
//			'type' => 'custom'
//		];
//	}

//	public static function GetUIEntityEditorPropertyViewHtml(array $params = [])
//	{
//		return "11";
//	}
//
//	public static function GetUIEntityEditorPropertyEditHtml(array $params = [])
//	{
//
//		return "22";
//	}

//namespace Bitrix\UI\EntityForm\Control;
//class Type
//{
//	public const COLUMN = 'column';
//	public const SECTION = 'section';
//	public const TEXT = 'text';
//	public const MULTI_TEXT = 'multitext';
//	public const TEXTAREA = 'textarea';
//	public const NUMBER = 'number';
//	public const MULTI_NUMBER = 'multinumber';
//	public const DATETIME = 'datetime';
//	public const MULTI_DATETIME = 'multidatetime';
//	public const BOOLEAN = 'boolean';
//	public const LIST = 'list';
//	public const MULTI_LIST = 'multilist';
//	public const HTML = 'html';
//	public const LINK = 'link';
//	public const IMAGE = 'image';
//	public const CUSTOM = 'custom';
//	public const MONEY = 'money';
//	public const MULTI_MONEY = 'multimoney';
//	public const USER = 'user';
//	public const INCLUDED_AREA = 'included_area';
//}

	public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		$eStore = self::GetAllItems();
		foreach($eStore as $item)
		{
			if($item['ID'] == $value['VALUE'])
			{
				return '<div style="text-align: left;">[' . $item['ID'] . '] ' . $item['TITLE'] . '</div>';
			}
		}
		return $value['VALUE'];
	}

	// old filter
	public static function GetAdminFilterHTML($arProperty, $strHTMLControlName)
	{
		$eStore = self::GetAllItems();
		$html = '<select size = "' . $arProperty['ROW_COUNT'] . '" name = "' . $strHTMLControlName['VALUE'] . '">';
		$html .= '<option value>'. GetMessage('ASKARON_PROP_ANY_VALUE') .'</option>';
		foreach($eStore as $item)
		{
			$html .= '<option value = "' . $item['ID'] . '">' . '[' . $item['ID'] . '] ' . $item['TITLE'] . '</option>';
		}
		$html .= '</select>';
		return $html;
	}

	// new filter
	public static function GetUIFilterProperty($arProperty, $strHTMLControlName, &$fields)
	{
		$fields["type"] = "list";
		$fields["items"] = array();
		$fields['params'] = ['multiple' => 'Y'];

		$arItems = self::GetAllItems();
		foreach ($arItems as $key=>$arItem)
		{
			$fields["items"][ $arItem["ID"] ] = "[".$arItem["ID"]."] ".htmlspecialcharsbx( $arItem["TITLE"] );
		}
	}


	public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
	{
		$arPropertyFields = array(
			'HIDE' => array('DEFAULT_VALUE')
		);
		return '';
	}

	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		$eStore = self::GetAllItems();
		$idStore = (empty($value['VALUE']) ? 0 : (int)$value['VALUE']);
		$html = '<div><div style = "display: inline-block;" ><select size = "' . $arProperty['ROW_COUNT'] . '" name = "' . $strHTMLControlName['VALUE'] . '"> ';
		$html .= '<option value = "" ' . (0 == $idStore ? 'selected' : '') . ' > ' . GetMessage('ASKARON_PROP_NO_VALUE') . ' </option > ';
		foreach($eStore as $item)
		{
			$html .= '<option value = "' . $item['ID'] . '" ' . ($item['ID'] == $idStore ? 'selected' : '') . ' > ' . '[' . $item['ID'] . '] ' . $item['TITLE'] . ' </option > ';
		}
		$html .= '</select></div > ';
		if($arProperty['WITH_DESCRIPTION'] == 'Y')
		{
			$html .= '<div style = "display: inline-block; vertical-align: top;"><span >&nbsp;' . GetMessage('ASKARON_PROP_FIELD_DESCRIPTION') . ':&nbsp;</span ><input type = "text" name = "' . $strHTMLControlName['DESCRIPTION'] . '" value = "' . (empty($value['DESCRIPTION']) ? '' : $value['DESCRIPTION']) . '" />';
		}
		$html .= '</div ></div > ';
		return $html;
	}

	public static function GetAllItems()
	{
		static $cache = false;

		if(!is_array($cache))
		{
			$OPTION_CACHE_PATH = str_replace('\\', "/", __CLASS__);
			$OPTION_CACHE_ID = $OPTION_CACHE_PATH;

			$cache_ttl = self::$OPTION_CACHE_TTL;
			$cache_id = md5($OPTION_CACHE_ID . "/" . __FUNCTION__);
			$cache_dir = $OPTION_CACHE_PATH . "/" . __FUNCTION__;

			$obCache = new \CPHPCache;

			if($obCache->InitCache($cache_ttl, $cache_id, $cache_dir))
			{
				$cache = $obCache->GetVars();
			}
			elseif($obCache->StartDataCache())
			{
				$cache = self::GetStoreElements();
//				if(!$cache)
//				{
//					$obCache->AbortDataCache();
//				}
			}
			$obCache->EndDataCache($cache);
		}

		return $cache;
	}

	protected static function GetStoreElements()
	{
		$arResult = array();
		if(Loader::includeModule("catalog"))
		{
			$res = \CCatalogStore::GetList(array('ID' => 'ASC'), array(), false, false, array('ID', 'TITLE'));
			while($arItem = $res->Fetch())
			{
				$arResult[] = $arItem;
			}
		}
		return $arResult;
	}

	public static function OnCatalogStoreUpdate()
	{
		self::CleanCache();
	}

	public static function OnCatalogStoreDelete()
	{
		self::CleanCache();
	}

	public static function OnCatalogStoreAdd()
	{
		self::CleanCache();
	}

	private static function CleanCache()
	{
		$cache_path = str_replace('\\', "/", __CLASS__);
		$obCache = new \CPHPCache();
		$obCache->CleanDir($cache_path);
	}
}