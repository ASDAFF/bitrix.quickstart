<?php
namespace Askaron\Prop;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class Price
{
	private static $OPTION_CACHE_TTL = 36000;

	public static function GetPropertyDescription()
	{
		return array(
			'PROPERTY_TYPE' => 'N',
			'USER_TYPE' => 'ASKARON_PROP_Price',
			'DESCRIPTION' => GetMessage('ASKARON_PROP_PRICE_DESCRIPTION'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetAdminFilterHTML' => array(__CLASS__, 'GetAdminFilterHTML'),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
			'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
		);
	}

	public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		$ePrice = self::GetAllItems();
		foreach($ePrice as $item)
		{
			if($item['ID'] == $value['VALUE'])
			{
				return '<div style="text-align: left;">[' . $item['ID'] . '] ' . $item['NAME'] . '</div>';
			}
		}
		return $value['VALUE'];
	}

	public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
	{
		$arPropertyFields = array(
			'HIDE' => array('DEFAULT_VALUE')
		);
		return '';
	}

	public static function GetAdminFilterHTML($arProperty, $strHTMLControlName)
	{
		$ePrice = self::GetAllItems();
		$html = '<select size="' . $arProperty['ROW_COUNT'] . '" name="' . $strHTMLControlName['VALUE'] . '">';
		$html .= '<option value>'. GetMessage('ASKARON_PROP_ANY_VALUE') .'</option>';
		foreach($ePrice as $item)
		{
			$html .= '<option value="' . $item['ID'] . '" >' . '[' . $item['ID'] . '] ' . $item['NAME'] . '</option>';
		}
		$html .= '</select>';
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
				$cache = self::GetPriceElements();
//				if(!$cache)
//				{
//					$obCache->AbortDataCache();
//				}
				$obCache->EndDataCache($cache);
			}
		}
		return $cache;
	}

	private static function GetPriceElements()
	{
		$arResult = array();
		if(Loader::includeModule('catalog'))
		{
			$res = \CCatalogGroup::GetList(array('ID' => 'ASC'), array(), false, false, array('ID', 'NAME'));
			$arResult = array();
			while($arItem = $res->Fetch())
			{
				$arResult[] = $arItem;
			}
		}
		return $arResult;
	}

	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		$ePrice = self::GetAllItems();
		$idPrice = (empty($value['VALUE']) ? 0 : (int)$value['VALUE']);
		$html = '<div><div style="display: inline-block;"><select size="' . $arProperty['ROW_COUNT'] . '" name="' . $strHTMLControlName['VALUE'] . '">';
		$html .= '<option value="" ' . (0 == $idPrice ? 'selected' : '') . '>' . GetMessage('ASKARON_PROP_NO_VALUE') . '</option>';
		foreach($ePrice as $item)
		{
			$html .= '<option value="' . $item['ID'] . '" ' . ($item['ID'] == $idPrice ? 'selected' : '') . '>' . '[' . $item['ID'] . '] ' . $item['NAME'] . '</option>';
		}
		$html .= '</select></div>';
		if($arProperty['WITH_DESCRIPTION'] == 'Y')
		{
			$html .= '<div style="display: inline-block; vertical-align: top;"><span>&nbsp;' . GetMessage('ASKARON_PROP_FIELD_DESCRIPTION') . ':&nbsp;</span><input type="text" name="' . $strHTMLControlName['DESCRIPTION'] . '" value="' . (empty($value['DESCRIPTION']) ? '' : $value['DESCRIPTION']) . '"/>';
		}
		$html .= '</div></div>';
		return $html;
	}

	public static function OnGroupUpdate()
	{
		self::CleanCache();
	}

	public static function OnGroupDelete()
	{
		self::CleanCache();
	}

	public static function OnGroupAdd()
	{
		self::CleanCache();
	}

	private static function CleanCache()
	{
		$cache_path = str_replace('\\', "/", __CLASS__ . '/' . __FUNCTION__);
		$obCache = new \CPHPCache();
		$obCache->CleanDir($cache_path);
	}
}