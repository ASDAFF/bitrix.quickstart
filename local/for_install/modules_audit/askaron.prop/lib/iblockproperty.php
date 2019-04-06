<?
namespace Askaron\Prop;

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
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),               //
			'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
		);
	}

	/**
	 * Метод должен вернуть безопасный HTML отображения значения свойства в списке элементов административной части.
	 *
	 * @param $arProperty           - Свойства элементов инфоблока
	 * @param $value                - Значение свойства. array("VALUE" => значение,"DESCRIPTION" => описание,);
	 * @param $strHTMLControlName - array(
	 *                            "VALUE" => html безопасное имя для значения,
	 *                            "DESCRIPTION" => html безопасное имя для описания,
	 *                            "MODE" => может принимать зачение "FORM_FILL" при вызове из формы редактирования элемента или "iblock_element_admin" при редактировании в режиме просмотра списка элементов, а также "EDIT_FORM" при редактировании инфоблока.
	 *                           "FORM_NAME" => имя формы в которую будет встроен элемент управления.);
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

	/**
	 * Метод должен вернуть HTML отображения элемента управления для редактирования значений свойства в административной части.
	 *
	 * @param $arProperty
	 * @param $value
	 * @param $strHTMLControlName
	 */
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

	public static function GetAllItems($idPropHere, $idIblock)
	{
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