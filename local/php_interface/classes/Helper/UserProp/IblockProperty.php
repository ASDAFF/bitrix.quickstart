<?php
namespace Helper\UserProp;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class IblockProperty
{
	private static $OPTION_CACHE_TTL = 36000;
	private static $OPTION_CACHE_PATH = '';
	private static $cacheProperty = array();
	private static $cacheIblock = array();

	public static function GetPropertyDescription()
	{
		return array(
			'PROPERTY_TYPE' => 'N',
			'USER_TYPE' => 'ASKARON_PROP_Iblock_Property',
			'DESCRIPTION' => GetMessage('ASKARON_PROP_IBLOCK_PROPERTY_DESCRIPTION'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),     //
			'GetAdminFilterHTML' => array(__CLASS__, 'GetAdminFilterHTML'),         //
			"GetUIFilterProperty" => array(__CLASS__, "GetUIFilterProperty"),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),               //
			'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
			"GetUIEntityEditorProperty" => [__CLASS__,"GetUIEntityEditorProperty"],
		);
	}

	public static function GetUIEntityEditorProperty($settings, $value)
	{
		$items = [];

		$arAllItems = self::GetAllItems($settings["ID"], $settings["IBLOCK_ID"]);
		foreach ($arAllItems as $key => $name)
		{
			$items[] = [
				'NAME' => "[".$key."] ".htmlspecialcharsbx($name),
				'VALUE' => $key,
				'ID' => $key,
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

	/**
	 * bezopasniy HTML otobrazhenia svoistva v spiske elementov v administrativnoy chasti
	 *
	 * @param $arProperty           - svojstva elementov infobloka
	 * @param $value                - znachenie svoistva array("VALUE" => znachenie,"DESCRIPTION" => opisanie,);
	 * @param $strHTMLControlName - array(
	 *                            "VALUE" => html bezopasnoe znacheniya,
	 *                            "DESCRIPTION" => html bezopasnoe dlya opisaniya,
	 *                            "MODE" => "FORM_FILL" privizove iz formi redaktirovaniya elementa ili "iblock_element_admin" priredaktirovanii v spiske, ili "EDIT_FORM" pri redaktirovanii infobloka
	 *                           "FORM_NAME" => imia formi v kotoruu budet vstroen element upravlenia );
	 */
	public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		$idIblock = $arProperty['IBLOCK_ID'];
		$idPropHere = $arProperty['ID'];
		$idPropValue = (empty($value['VALUE']) ? 0 : (int)$value['VALUE']);
		$property = self::GetAllItems($idPropHere, $idIblock);
		return '<div style="text-align: left;">[' . $idPropValue . '] ' . $property[$idPropValue] . '</div>';
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
		$idIblock = $arProperty['IBLOCK_ID'];
		$idPropHere = $arProperty['ID'];
		$idPropValue = (empty($value['VALUE']) ? 0 : (int)$value['VALUE']);
		$property = self::GetAllItems($idPropHere, $idIblock);

		$html = '<div><div style = "display: inline-block;"><select size = ' . $arProperty['ROW_COUNT'] . '" name = "' . $strHTMLControlName['VALUE'] . '">';
		$html .= '<option value = "" ' . (0 == $idPropValue ? 'selected' : '') . ' > ' . GetMessage('ASKARON_PROP_NO_VALUE') . ' </option > ';
		foreach($property as $idP => $item)
		{
			$html .= '<option value = "' . $idP . '" ' . ($idP == $idPropValue ? 'selected' : '') . ' > ' . '[' . $idP . '] ' . $item . ' </option > ';
		}
		$html .= '</select></div></div>';

		return $html;
	}

	// old filter
	public static function GetAdminFilterHTML($arProperty, $strHTMLControlName)
	{
		$idIblock = $arProperty['IBLOCK_ID'];
		$idPropHere = $arProperty['ID'];
		$property = self::GetAllItems($idPropHere, $idIblock);

		$html = '<select size = "' . $arProperty['ROW_COUNT'] . '" name = "' . $strHTMLControlName['VALUE'] . '">';
		$html .= '<option value>'. GetMessage('ASKARON_PROP_ANY_VALUE') .'</option>';
		foreach($property as $idP =>$item)
		{
			$html .= '<option value = "' . $idP . '">' . '[' . $idP . '] ' . $item . '</option>';
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

		$idIblock = $arProperty['IBLOCK_ID'];
		$idPropHere = $arProperty['ID'];

		$arItems = self::GetAllItems($idPropHere, $idIblock);
		foreach ($arItems as $idP =>$item)
		{
			$fields["items"][ $idP ] = "[".$idP."] ".htmlspecialcharsbx( $item );
		}
	}

	public static function GetAllItems($idPropHere, $idIblock)
	{
		// TODO fix cache params
		if(empty(self::$cacheProperty))
		{
			self::$OPTION_CACHE_PATH = str_replace('\\', '/', __CLASS__) . '/'. $idIblock;
			$cache_ttl = self::$OPTION_CACHE_TTL;
			$cache_id = md5(self::$OPTION_CACHE_PATH.'/'.__FUNCTION__);
			$cache_dir =  self::$OPTION_CACHE_PATH.'/'.__FUNCTION__;
			$obCache = \Bitrix\Main\Data\Cache::createInstance();

			if($obCache->initCache($cache_ttl, $cache_id, $cache_dir))
			{
				self::$cacheProperty = $obCache->getVars();
			}
			elseif($obCache->startDataCache($cache_ttl, $cache_id, $cache_dir))
			{
				$result = self::GetIblockProperty($idPropHere, $idIblock);
				if(empty($result))
				{
					$obCache->abortDataCache();
				}
				else
				{
					$obCache->endDataCache($result);
					self::$cacheProperty = $result;
				}
			}
		}
		return self::$cacheProperty;
	}

	public static function GetIblock()
	{
		$arResult = array();
		if(empty(self::$cacheIblock))
		{
			$arIblock = \Askaron\Prop\Iblock::GetAllItems();
			foreach($arIblock as $item)
			{
				$arResult[$item['ID']] = array('NAME' => $item['NAME']);
			}
			self::$cacheIblock = $arResult;
		}
		return self::$cacheIblock;
	}

	public static function GetIblockProperty($idPropHere, $idIblock)
	{
		$arResult = array();
		if(\Bitrix\Main\Loader::includeModule('iblock'))
		{
			$arProperty = \Bitrix\Iblock\PropertyTable::getList(array(
				'select' => array('ID', 'NAME'),
				'filter' => array('ACTIVE' => 'Y', 'IBLOCK_ID' => $idIblock, '!ID' => $idPropHere)
			))->fetchAll();
			foreach($arProperty as $item)
			{
				$arResult[$item['ID']] = $item['NAME'];
			}
		}
		return $arResult;
	}

	public static function CleanCache($idIblock)
	{
		self::$OPTION_CACHE_PATH = str_replace('\\', '/', __CLASS__) . '/'. $idIblock;
		$obCache = \Bitrix\Main\Data\Cache::createInstance();
		$obCache->cleanDir(self::$OPTION_CACHE_PATH);
	}

	public static function OnBeforeIBlockPropertyAdd(&$arParams)
	{
		self::CleanCache($arParams['IBLOCK_ID']);
	}

	public static function OnBeforeIBlockPropertyUpdate(&$arParams)
	{
		self::CleanCache($arParams['IBLOCK_ID']);
	}

	public static function OnBeforeIBlockPropertyDelete($ID)
	{
		if(\Bitrix\Main\Loader::includeModule('iblock'))
		{
			$idBlock = \Bitrix\Iblock\PropertyTable::getList(array(
				'select' => array('IBLOCK_ID'),
				'filter' => array('ID' => $ID)
			))->fetch();
			self::CleanCache($idBlock);
		}
	}
}