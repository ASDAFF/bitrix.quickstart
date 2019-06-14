<?php
namespace Askaron\Prop;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use	\Bitrix\Iblock\IblockTable;

Loc::loadMessages(__FILE__);

class Iblock
{
	private static $OPTION_CACHE_TTL = 36000;

	public static function GetPropertyDescription()
	{
		return array(
			'PROPERTY_TYPE' => 'N',
			'USER_TYPE' => 'ASKARON_PROP_Iblock',
			'DESCRIPTION' => GetMessage('ASKARON_PROP_IBLOCK_DESCRIPTION'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetAdminFilterHTML' => array(__CLASS__, 'GetAdminFilterHTML'),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
			'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
		);
	}

	public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		$eStore = self::GetAllItems();
		foreach($eStore as $item)
		{
			if($item['ID'] == $value['VALUE'])
			{
				return '<div style="text-align: left;">[' . $item['ID'] . '] ' . $item['NAME'] . '</div>';
			}
		}
		return $value['VALUE'];
	}

	public static function GetAdminFilterHTML($arProperty, $strHTMLControlName)
	{
		$eStore = self::GetAllItems();
		$html = '<select size = "' . $arProperty['ROW_COUNT'] . '" name = "' . $strHTMLControlName['VALUE'] . '">';
		$html .= '<option value>'. GetMessage('ASKARON_PROP_ANY_VALUE') .'</option>';
		foreach($eStore as $item)
		{
			$html .= '<option value = "' . $item['ID'] . '">' . '[' . $item['ID'] . '] ' . $item['NAME'] . '</option>';
		}
		$html .= '</select>';
		return $html;
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
			$html .= '<option value = "' . $item['ID'] . '" ' . ($item['ID'] == $idStore ? 'selected' : '') . ' > ' . '[' . $item['ID'] . '] ' . $item['NAME'] . ' </option > ';
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
				$cache = self::GetIblockElements();
			}
			$obCache->EndDataCache($cache);
		}

		return $cache;
	}

	protected static function GetIblockElements()
	{
		$arResult = array();
		if(Loader::includeModule('iblock'))
		{
			$arResult = IblockTable::getList(array(
				'select' => array('ID', 'NAME'),
				'filter' => array('ACTIVE' => 'Y'),
				'order' => array('ID'),
			))->fetchAll();
		}
		return $arResult;
	}

	public static function OnIblockUpdate()
	{
		self::CleanCache();
	}

	public static function OnIblockDelete()
	{
		self::CleanCache();
	}

	public static function OnIblockAdd()
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