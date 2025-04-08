<?php
namespace Helper\UserProp;
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class IblockPropertyEnum
{
	private static $OPTION_CACHE_TTL = 36000;
	private static $OPTION_CACHE_PATH = '';
	private static $cacheProperty = array();

	public static function GetPropertyDescription()
	{
		return array(
			'PROPERTY_TYPE' => 'N',
			'USER_TYPE' => 'ASKARON_PROP_Iblock_Property_Enum',
			'DESCRIPTION' => Loc::getMessage( 'ASKARON_PROP_IBLOCK_PROPERTY_ENUM_DESCRIPTION' ),
			"PrepareSettings" =>array(__CLASS__, "PrepareSettings"),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),               //
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),     //
			'GetAdminFilterHTML' => array(__CLASS__, 'GetAdminFilterHTML'),         //
			"GetUIFilterProperty" => array(__CLASS__, "GetUIFilterProperty"),
			'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
			"GetUIEntityEditorProperty" => [__CLASS__,"GetUIEntityEditorProperty"],
		);
	}

	public static function GetUIEntityEditorProperty($settings, $value)
	{
		$items = [];

		//dd($settings);

		if ( $settings["USER_TYPE_SETTINGS"]["LINK_PROPERTY_ID"] )
		{
			$arAllItems = static::GetAllItems( $settings["USER_TYPE_SETTINGS"]["LINK_PROPERTY_ID"] );
			foreach ($arAllItems as $key => $name)
			{
				$items[] = [
					'NAME' => "[" . $key . "] " . htmlspecialcharsbx($name),
					'VALUE' => $key,
					'ID' => $key,
				];
			}
		}

		return [
			'type' => ($settings['MULTIPLE'] === 'Y') ? 'multilist' : 'list',
			'data' => [
				//'isProductProperty' => true,
				'enableEmptyItem' => true,
				'items' => $items
			]
		];
	}

	public static function PrepareSettings($arProperty)
	{
		$property_id = "";
		if(is_array($arProperty["USER_TYPE_SETTINGS"]))
			$property_id = intval($arProperty["USER_TYPE_SETTINGS"]["LINK_PROPERTY_ID"]);
		if($property_id <= 0)
			$property_id = "";


		$size = 1;
		if(is_array($arProperty["USER_TYPE_SETTINGS"]))
			$size = intval($arProperty["USER_TYPE_SETTINGS"]["SIZE"]);
		if($size <= 0)
			$size = 1;


		if(is_array($arProperty["USER_TYPE_SETTINGS"]) && $arProperty["USER_TYPE_SETTINGS"]["SHOW_KEY"] === "Y")
			$show_key = "Y";
		else
			$show_key = "N";

		return array(
			"SIZE" =>  $size,
			"LINK_PROPERTY_ID" => $property_id,
			"SHOW_KEY" => $show_key
//			"group" => $group,

		);
	}

	public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
	{
		$settings = static::PrepareSettings($arProperty);

		$arPropertyFields = array(
			'HIDE' => array('DEFAULT_VALUE', "ROW_COUNT", "COL_COUNT", "MULTIPLE_CNT"), //'COL_COUNT'
			"USER_TYPE_SETTINGS_TITLE" => Loc::getMessage( 'ASKARON_PROP_IBLOCK_PROPERTY_ENUM_SETTINGS_FORM' )
		);

		$arIblocks = array();

		$res = \CIBlock::GetList( array("ID" => "ASC"), array("CHECK_PERMISSION" => "N") );
		while ( $arFields = $res->GetNext() )
		{
			$arIblocks[ $arFields["ID"] ] = array(
				"ID" => $arFields["ID"],
				"NAME" => $arFields["NAME"],
				"PROPERTIES" => array(),
			);
		}

		$res = \CIBlockProperty::GetList( array("ID" => "ASC"), array("CHECK_PERMISSION" => "N", "PROPERTY_TYPE" => "L" ) );
		while ($arFields = $res->GetNext())
		{
			$arIblocks[ $arFields["IBLOCK_ID"] ]["PROPERTIES" ][ $arFields["ID"] ] = array(
				"ID" => $arFields["ID"],
				"NAME" => $arFields["NAME"],
				"CODE" => $arFields["CODE"],
			);
		}


		ob_start();
		?>
		<tr>
			<td><?=Loc::getMessage('ASKARON_PROP_IBLOCK_PROPERTY_ENUM_SETTINGS_LINK_PROPERTY_ID')?></td>
			<td>
				<select name="<?=$strHTMLControlName["NAME"]?>[LINK_PROPERTY_ID]">
					<option value=""><?=Loc::getMessage('ASKARON_PROP_IBLOCK_PROPERTY_ENUM_SETTINGS_LINK_PROPERTY_ID_EMPTY')?></option>
					<?foreach ($arIblocks as $arIblock):?>
						<?if ($arIblock["PROPERTIES"]):?>
							<optgroup label="<?=$arIblock["NAME"]?> [<?=$arIblock["ID"]?>]">
								<?foreach ( $arIblock["PROPERTIES"] as $arItem ):?>
									<option value="<?=$arItem["ID"]?>"
										<?if ( $arItem["ID"] == $settings["LINK_PROPERTY_ID"] ):?>
											selected
										<?endif?>
									>[<?=$arItem["ID"]?>] <?=$arItem["NAME"]?></option>
								<?endforeach?>
							</optgroup>
						<?endif?>
					<?endforeach?>
				</select>
				<?/*
					<input type="text" size="5" name="<?=$strHTMLControlName["NAME"]?>[PROPERTY_ID]" value="<?=$settings["PROPERTY_ID"]?>">
				*/?>
			</td>
		</tr>
		<tr>
			<td><?=Loc::getMessage('ASKARON_PROP_IBLOCK_PROPERTY_ENUM_SETTINGS_SIZE')?></td>
			<td>
				<input type="text" size="5" name="<?=$strHTMLControlName["NAME"]?>[SIZE]" value="<?=$settings["SIZE"]?>">
			</td>
		</tr>
		<tr>
			<td><?=Loc::getMessage('ASKARON_PROP_IBLOCK_PROPERTY_ENUM_SETTINGS_SHOW_KEY')?></td>
			<td>
				<input type="checkbox"
					<?if ($settings["SHOW_KEY"] == "Y"  ):?>
						checked
					<?endif?>
					name="<?=$strHTMLControlName["NAME"]?>[SHOW_KEY]"
					value="Y">
			</td>
		</tr>
		<?
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
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
	 *                           "FORM_NAME" => imya formi v kotoruyu budet vstroem element upravleniya.);
	 */
	public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		$html = "";
		$settings = static::PrepareSettings($arProperty);

		$arItems = static::GetAllItems( $settings["LINK_PROPERTY_ID"] );
		$value = intval($value['VALUE']);
		if ($value > 0 )
		{
			//$html='<div style="text-align: left;">['.$value.'] '.htmlspecialcharsbx( $arItems[$value] ).'</div>';
			$html= '<span title="'.$value.'">'.htmlspecialcharsbx( $arItems[$value] ).'</span>';
		}

		return $html;
	}



	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		//dd($value);

		$settings = static::PrepareSettings($arProperty);
		$arItems = static::GetAllItems( $settings["LINK_PROPERTY_ID"] );

		$idPropValue = intval($value['VALUE']);

		$size = min( $settings['SIZE'], count($arItems) + 1 );
		if ($size <=0 )
		{
			$size = 1;
		}

		$html = '<div><div style = "display: inline-block;"><select size = ' .$size . '" name = "' . $strHTMLControlName['VALUE'] . '">';
		$html .= '<option value = "" ' . (0 == $idPropValue ? 'selected' : '') . ' > ' . GetMessage('ASKARON_PROP_NO_VALUE') . ' </option > ';
		foreach($arItems as $idP => $item)
		{
			//$html .= '<option value="'.$idP.'" '.($idP == $idPropValue ? 'selected' : '').' >'.htmlspecialcharsbx($item).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;['.$idP.']</option>';
			$html .= '<option value="'.$idP.'" '.($idP == $idPropValue ? 'selected' : '').' >';

				if ( $settings["SHOW_KEY"] == "Y" )
				{
					$html .= '['.$idP."] ";
				}

				$html .= htmlspecialcharsbx($item);


			$html .= '</option>';
		}
		$html .= '</select></div></div>';

		return $html;
	}

	// old filter
	public static function GetAdminFilterHTML($arProperty, $strHTMLControlName)
	{
		$settings = static::PrepareSettings($arProperty);
		$arItems = static::GetAllItems( $settings["LINK_PROPERTY_ID"] );

		$size = min( $settings['SIZE'], count($arItems) + 1 );
		if ($size <=0 )
		{
			$size = 1;
		}

		$html = '<select size = "'.$size.'" name="'.$strHTMLControlName['VALUE'].'">';
		$html .= '<option value="">'. GetMessage('ASKARON_PROP_ANY_VALUE') .'</option>';
		foreach($arItems as $idP => $item)
		{
			//$html .= '<option value="'.$idP.'" '.($idP == $idPropValue ? 'selected' : '').' >'.htmlspecialcharsbx($item).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;['.$idP.']</option>';
			$html .= '<option value="'.$idP.'" >'.htmlspecialcharsbx($item).'</option>';
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

		$settings = static::PrepareSettings($arProperty);
		$arItems = static::GetAllItems( $settings["LINK_PROPERTY_ID"] );
		foreach ($arItems as $key=>$item)
		{
			$fields["items"][ $key ] = htmlspecialcharsbx( $item );
		}
	}

	public static function GetAllItems( $LINK_PROPERTY_ID )
	{
		static $arCache = array();
		$arResult = array();

		$LINK_PROPERTY_ID = intval($LINK_PROPERTY_ID);
		if ($LINK_PROPERTY_ID > 0)
		{
			if ( isset( $arCache[ $LINK_PROPERTY_ID ] ) )
			{
				$arResult = $arCache[ $LINK_PROPERTY_ID ];
			}
			else
			{
				$arResult = static::GetAllItemsData( $LINK_PROPERTY_ID );
				$arCache[ $LINK_PROPERTY_ID ] = $arResult;

				// TODO: cache
			}
		}

		return $arResult;

//		if(empty(self::$cacheProperty))
//		{
//			self::$OPTION_CACHE_PATH = str_replace('\\', '/', __CLASS__) . '/'. $idIblock;
//			$cache_ttl = self::$OPTION_CACHE_TTL;
//			$cache_id = md5(self::$OPTION_CACHE_PATH.'/'.__FUNCTION__);
//			$cache_dir =  self::$OPTION_CACHE_PATH.'/'.__FUNCTION__;
//			$obCache = \Bitrix\Main\Data\Cache::createInstance();
//
//			if($obCache->initCache($cache_ttl, $cache_id, $cache_dir))
//			{
//				self::$cacheProperty = $obCache->getVars();
//			}
//			elseif($obCache->startDataCache($cache_ttl, $cache_id, $cache_dir))
//			{
//				$result = self::GetIblockProperty($idPropHere, $idIblock);
//				if(empty($result))
//				{
//					$obCache->abortDataCache();
//				}
//				else
//				{
//					$obCache->endDataCache($result);
//					self::$cacheProperty = $result;
//				}
//			}
//		}
//		return self::$cacheProperty;
	}

	public static function GetAllItemsData($LINK_PROPERTY_ID)
	{
		$arResult = array();

		if ( $LINK_PROPERTY_ID > 0 )
		{
			$property_enums = \CIBlockPropertyEnum::GetList(
					Array('SORT' => 'ASC', 'VALUE' => "ASC", 'ID' => 'ASC'),
					Array("PROPERTY_ID" => $LINK_PROPERTY_ID )
			);
			while($enum_fields = $property_enums->Fetch())
			{
				$arResult[ $enum_fields["ID"] ] = $enum_fields["VALUE"];
			}
		}

		return $arResult;
	}

//	public static function CleanCache($idIblock)
//	{
//		self::$OPTION_CACHE_PATH = str_replace('\\', '/', __CLASS__) . '/'. $idIblock;
//		$obCache = \Bitrix\Main\Data\Cache::createInstance();
//		$obCache->cleanDir(self::$OPTION_CACHE_PATH);
//	}
//
//	public static function OnBeforeIBlockPropertyAdd(&$arParams)
//	{
//		self::CleanCache($arParams['IBLOCK_ID']);
//	}
//
//	public static function OnBeforeIBlockPropertyUpdate(&$arParams)
//	{
//		self::CleanCache($arParams['IBLOCK_ID']);
//	}
//
//	public static function OnBeforeIBlockPropertyDelete($ID)
//	{
//		if(\Bitrix\Main\Loader::includeModule('iblock'))
//		{
//			$idBlock = \Bitrix\Iblock\PropertyTable::getList(array(
//				'select' => array('IBLOCK_ID'),
//				'filter' => array('ID' => $ID)
//			))->fetch();
//			self::CleanCache($idBlock);
//		}
//	}
}