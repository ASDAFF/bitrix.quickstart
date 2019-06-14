<?php

IncludeModuleLangFile(__FILE__);
define ('ASD_UT_CHECKBOX', 'SASDCheckbox');
define ('ASD_UT_CHECKBOX_VAL_FALSE', 'N');
define ('ASD_UT_CHECKBOX_VAL_TRUE', 'Y');

define ('ASD_UT_CHECKBOX_NUM', 'SASDCheckboxNum');
define ('ASD_UT_CHECKBOX_VAL_NUM_FALSE', 0);
define ('ASD_UT_CHECKBOX_VAL_NUM_TRUE', 1);

define ('ASD_UT_PALETTE', 'SASDPalette');

class CASDiblockPropCheckbox {
	public static function GetUserTypeDescription() {
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => ASD_UT_CHECKBOX,
			'DESCRIPTION' => GetMessage('ASD_UT_CHECKBOX_DESCR'),
			'ConvertToDB' => array(__CLASS__, 'ConvertToDB'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetAdminListViewHTML' => array(__CLASS__,'GetAdminListViewHTML'),
			'GetPublicViewHTML' => array(__CLASS__, 'GetPublicViewHTML'),
			'GetPublicEditHTML' => array(__CLASS__, 'GetPublicEditHTML'),
			'GetAdminFilterHTML' => array(__CLASS__,'GetAdminFilterHTML'),
			'GetSettingsHTML' => array(__CLASS__,'GetSettingsHTML'),
			'PrepareSettings' => array(__CLASS__,'PrepareSettings'),
		);
	}

	public static function ConvertToDB($arProperty, $value) {
		if (empty($value['VALUE']) || ASD_UT_CHECKBOX_VAL_TRUE != $value['VALUE']) {
			$value['VALUE'] = ASD_UT_CHECKBOX_VAL_FALSE;
		}
		return $value;
	}

	public static function GetSettingsHTML($arFields,$strHTMLControlName, &$arPropertyFields) {
		$arPropertyFields = array(
			'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'MULTIPLE_CNT', 'WITH_DESCRIPTION'),
			'USER_TYPE_SETTINGS_TITLE' => GetMessage('ASD_UT_CHECKBOX_SETTING_TITLE'),
		);

		$arSettings = self::PrepareSettings($arFields);

		ob_start();
		?><tr>
			<td><?php echo GetMessage('ASD_UT_CHECKBOX_SETTING_VALUE_N'); ?></td>
			<td><input type="text" name="<?php echo $strHTMLControlName['NAME'];?>[VIEW][<?php echo ASD_UT_CHECKBOX_VAL_FALSE; ?>]" value="<?php echo htmlspecialcharsbx($arSettings['VIEW'][ASD_UT_CHECKBOX_VAL_FALSE]); ?>"></td>
			</tr>
			<tr>
			<td><?php echo GetMessage('ASD_UT_CHECKBOX_SETTING_VALUE_Y'); ?></td>
			<td><input type="text" name="<?php echo $strHTMLControlName['NAME'];?>[VIEW][<?php echo ASD_UT_CHECKBOX_VAL_TRUE; ?>]" value="<?php echo htmlspecialcharsbx($arSettings['VIEW'][ASD_UT_CHECKBOX_VAL_TRUE]); ?>"></td>
		</tr><?php
		$strResult = ob_get_contents();
		ob_end_clean();

		return $strResult;
	}

	public static function GetPropertyFieldHtml($arProperty, $arValue, $strHTMLControlName) {
		if (empty($arValue['VALUE'])) {
			$arValue['VALUE'] = $arProperty['DEFAULT_VALUE'];
		}
		if (ASD_UT_CHECKBOX_VAL_TRUE != $value['VALUE']) {
			$value['VALUE'] = ASD_UT_CHECKBOX_VAL_FALSE;
		}
		$strResult = '<input type="hidden" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_N" value="'.ASD_UT_CHECKBOX_VAL_FALSE.'" />'.
			'<input type="checkbox" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_Y" value="'.ASD_UT_CHECKBOX_VAL_TRUE.'" '.(ASD_UT_CHECKBOX_VAL_TRUE == $arValue['VALUE'] ? 'checked="checked"' : '').'/>';
		return $strResult;
	}

	public static function GetAdminListViewHTML($arProperty, $arValue, $strHTMLControlName) {
		$arSettings = static::PrepareSettings($arProperty);
		if (ASD_UT_CHECKBOX_VAL_TRUE != $arValue['VALUE']) {
			$arValue['VALUE'] = ASD_UT_CHECKBOX_VAL_FALSE;
		}
		return htmlspecialcharsex($arSettings['VIEW'][$arValue['VALUE']]);
	}

	public static function GetAdminFilterHTML($arProperty, $strHTMLControlName) {

		$arSettings = static::PrepareSettings($arProperty);

		$strCurValue = '';
		if (array_key_exists($strHTMLControlName['VALUE'], $_REQUEST) && (ASD_UT_CHECKBOX_VAL_TRUE==$_REQUEST[$strHTMLControlName['VALUE']] || ASD_UT_CHECKBOX_VAL_FALSE==$_REQUEST[$strHTMLControlName['VALUE']])) {
			$strCurValue = $_REQUEST[$strHTMLControlName['VALUE']];
		} elseif (isset($GLOBALS[$strHTMLControlName['VALUE']]) && (ASD_UT_CHECKBOX_VAL_TRUE==$GLOBALS[$strHTMLControlName['VALUE']] || ASD_UT_CHECKBOX_VAL_FALSE==$GLOBALS[$strHTMLControlName['VALUE']])) {
			$strCurValue = $GLOBALS[$strHTMLControlName['VALUE']];
		}

		$strResult = '<select name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="filter_'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'">';
		$strResult .= '<option value=""'.(empty($strCurValue) ? ' selected="selected"' : '').'>'.htmlspecialcharsex(GetMessage('ASD_UT_CHECKBOX_VALUE_EMPTY')).'</option>';
		foreach ($arSettings['VIEW'] as $key => $value) {
			$strResult .= '<option value="'.htmlspecialcharsbx($key).'"'.($key == $strCurValue ? ' selected="selected"' : '').'>'.htmlspecialcharsex($value).'</option>';
		}
		$strResult .= '</select>';

		return $strResult;
	}

	public static function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName) {
		$arSettings = static::PrepareSettings($arProperty);
		if (ASD_UT_CHECKBOX_VAL_TRUE != $arValue['VALUE']) {
			$arValue['VALUE'] = ASD_UT_CHECKBOX_VAL_FALSE;
		}
		return htmlspecialcharsex($arSettings['VIEW'][$arValue['VALUE']]);
	}

	public static function GetPublicEditHtml($arProperty, $arValue, $strHTMLControlName) {
		if (empty($arValue['VALUE'])) {
			$arValue['VALUE'] = $arProperty['DEFAULT_VALUE'];
		}
		if (ASD_UT_CHECKBOX_VAL_TRUE != $value['VALUE']) {
			$value['VALUE'] = ASD_UT_CHECKBOX_VAL_FALSE;
		}
		$strResult = '<input type="hidden" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_N" value="'.ASD_UT_CHECKBOX_VAL_FALSE.'" />'.
			'<input type="checkbox" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_Y" value="'.ASD_UT_CHECKBOX_VAL_TRUE.'" '.(ASD_UT_CHECKBOX_VAL_TRUE == $arValue['VALUE'] ? 'checked="checked"' : '').'/>';
		return $strResult;
	}

	public static function PrepareSettings($arFields) {
		$arDefView = self::GetDefaultListValues();
		$arView = array();
		if (
			array_key_exists('USER_TYPE_SETTINGS', $arFields) && is_array($arFields['USER_TYPE_SETTINGS']) &&
			array_key_exists('VIEW', $arFields['USER_TYPE_SETTINGS']) &&
			!empty($arFields['USER_TYPE_SETTINGS']['VIEW']) && is_array($arFields['USER_TYPE_SETTINGS']['VIEW'])
		) {
			$arView = $arFields['USER_TYPE_SETTINGS']['VIEW'];
		}

		if (empty($arView)) {
			$arView = $arDefView;
		}

		return array(
			'VIEW' => $arView
		);
	}

	protected function GetDefaultListValues() {
		return array(
			ASD_UT_CHECKBOX_VAL_FALSE => GetMessage('ASD_UT_CHECKBOX_VALUE_N'),
			ASD_UT_CHECKBOX_VAL_TRUE => GetMessage('ASD_UT_CHECKBOX_VALUE_Y')
		);
	}
}

class CASDiblockPropCheckboxNum {
	public static function GetUserTypeDescription() {
		return array(
			'PROPERTY_TYPE' => 'N',
			'USER_TYPE' => ASD_UT_CHECKBOX_NUM,
			'DESCRIPTION' => GetMessage('ASD_UT_CHECKBOX_NUM_DESCR'),
			'ConvertToDB' => array(__CLASS__, 'ConvertToDB'),
			'ConvertFromDB' => array(__CLASS__, 'ConvertFromDB'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetAdminListViewHTML' => array(__CLASS__,'GetAdminListViewHTML'),
			'GetPublicViewHTML' => array(__CLASS__, 'GetPublicViewHTML'),
			'GetPublicEditHTML' => array(__CLASS__, 'GetPublicEditHTML'),
			'GetAdminFilterHTML' => array(__CLASS__,'GetAdminFilterHTML'),
			'GetSettingsHTML' => array(__CLASS__,'GetSettingsHTML'),
			'PrepareSettings' => array(__CLASS__,'PrepareSettings'),
		);
	}

	public static function ConvertToDB($arProperty, $value) {
		$value['VALUE'] = intval($value['VALUE']);
		if (ASD_UT_CHECKBOX_VAL_NUM_TRUE != $value['VALUE']) {
			$value['VALUE'] = ASD_UT_CHECKBOX_VAL_NUM_FALSE;
		}
		return $value;
	}

	public static function ConvertFromDB($arProperty, $value)
	{
		$value['VALUE'] = intval($value['VALUE']);
		if (ASD_UT_CHECKBOX_VAL_NUM_TRUE != $value['VALUE']) {
			$value['VALUE'] = ASD_UT_CHECKBOX_VAL_NUM_FALSE;
		}
		return $value;
	}

	public static function GetSettingsHTML($arFields,$strHTMLControlName, &$arPropertyFields) {
		$arPropertyFields = array(
			'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'MULTIPLE_CNT', 'WITH_DESCRIPTION'),
			'USER_TYPE_SETTINGS_TITLE' => GetMessage('ASD_UT_CHECKBOX_NUM_SETTING_TITLE'),
		);

		$arSettings = self::PrepareSettings($arFields);

		ob_start();
		?><tr>
			<td><?php echo GetMessage('ASD_UT_CHECKBOX_NUM_SETTING_VALUE_N'); ?></td>
			<td><input type="text" name="<?php echo $strHTMLControlName['NAME']; ?>[VIEW][<?php echo ASD_UT_CHECKBOX_VAL_NUM_FALSE; ?>]" value="<?php echo htmlspecialcharsbx($arSettings['VIEW'][ASD_UT_CHECKBOX_VAL_NUM_FALSE]); ?>"></td>
			</tr>
			<tr>
			<td><?php echo GetMessage('ASD_UT_CHECKBOX_NUM_SETTING_VALUE_Y'); ?></td>
			<td><input type="text" name="<?php echo $strHTMLControlName['NAME']; ?>[VIEW][<?php echo ASD_UT_CHECKBOX_VAL_NUM_TRUE; ?>]" value="<?php echo htmlspecialcharsbx($arSettings['VIEW'][ASD_UT_CHECKBOX_VAL_NUM_TRUE]); ?>"></td>
		</tr><?php
		$strResult = ob_get_contents();
		ob_end_clean();

		return $strResult;
	}

	public static function GetPropertyFieldHtml($arProperty, $arValue, $strHTMLControlName) {
		if (empty($arValue['VALUE'])) {
			$arValue['VALUE'] = $arProperty['DEFAULT_VALUE'];
		}
		$arValue['VALUE'] = intval($arValue['VALUE']);
		if (ASD_UT_CHECKBOX_VAL_NUM_TRUE != $value['VALUE']) {
			$value['VALUE'] = ASD_UT_CHECKBOX_VAL_NUM_FALSE;
		}

		$strResult = '<input type="hidden" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_N" value="'.ASD_UT_CHECKBOX_VAL_NUM_FALSE.'" />'.
			'<input type="checkbox" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_Y" value="'.ASD_UT_CHECKBOX_VAL_NUM_TRUE.'" '.(ASD_UT_CHECKBOX_VAL_NUM_TRUE == $arValue['VALUE'] ? 'checked="checked"' : '').'/>';
		return $strResult;
	}

	public static function GetAdminListViewHTML($arProperty, $arValue, $strHTMLControlName) {
		$arSettings = static::PrepareSettings($arProperty);
		if (ASD_UT_CHECKBOX_VAL_NUM_TRUE != $arValue['VALUE']) {
			$arValue['VALUE'] = ASD_UT_CHECKBOX_VAL_NUM_FALSE;
		}
		return htmlspecialcharsex($arSettings['VIEW'][$arValue['VALUE']]);
	}

	public static function GetAdminFilterHTML($arProperty, $strHTMLControlName) {

		$arSettings = static::PrepareSettings($arProperty);

		$strCurValue = '';
		if (array_key_exists($strHTMLControlName['VALUE'], $_REQUEST) && (ASD_UT_CHECKBOX_VAL_NUM_TRUE==$_REQUEST[$strHTMLControlName['VALUE']] || ASD_UT_CHECKBOX_VAL_NUM_FALSE==$_REQUEST[$strHTMLControlName['VALUE']])) {
			$strCurValue = $_REQUEST[$strHTMLControlName['VALUE']];
		} elseif (isset($GLOBALS[$strHTMLControlName['VALUE']]) && (ASD_UT_CHECKBOX_VAL_NUM_TRUE==$GLOBALS[$strHTMLControlName['VALUE']] || ASD_UT_CHECKBOX_VAL_NUM_FALSE==$GLOBALS[$strHTMLControlName['VALUE']])) {
			$strCurValue = $GLOBALS[$strHTMLControlName['VALUE']];
		}

		$strResult = '<select name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="filter_'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'">';
		$strResult .= '<option value=""'.(empty($strCurValue) ? ' selected="selected"' : '').'>'.htmlspecialcharsex(GetMessage('ASD_UT_CHECKBOX_VALUE_NUM_EMPTY')).'</option>';
		foreach ($arSettings['VIEW'] as $key => $value) {
			$strResult .= '<option value="'.intval($key).'"'.($key == $strCurValue ? ' selected="selected"' : '').'>'.htmlspecialcharsex($value).'</option>';
		}
		$strResult .= '</select>';

		return $strResult;
	}

	public static function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName) {
		$arSettings = static::PrepareSettings($arProperty);
		$arValue['VALUE'] = intval($arValue['VALUE']);
		if (ASD_UT_CHECKBOX_VAL_NUM_TRUE != $arValue['VALUE']) {
			$arValue['VALUE'] = ASD_UT_CHECKBOX_VAL_NUM_FALSE;
		}
		return htmlspecialcharsex($arSettings['VIEW'][$arValue['VALUE']]);
	}

	public static function GetPublicEditHtml($arProperty, $arValue, $strHTMLControlName) {
		if (empty($arValue['VALUE'])) {
			$arValue['VALUE'] = $arProperty['DEFAULT_VALUE'];
		}
		$arValue['VALUE'] = intval($arValue['VALUE']);
		if (ASD_UT_CHECKBOX_VAL_NUM_TRUE != $value['VALUE']) {
			$value['VALUE'] = ASD_UT_CHECKBOX_VAL_NUM_FALSE;
		}

		$strResult = '<input type="hidden" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_N" value="'.ASD_UT_CHECKBOX_VAL_NUM_FALSE.'" />'.
			'<input type="checkbox" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_Y" value="'.ASD_UT_CHECKBOX_VAL_NUM_TRUE.'" '.(ASD_UT_CHECKBOX_VAL_NUM_TRUE == $arValue['VALUE'] ? 'checked="checked"' : '').'/>';
		return $strResult;
	}

	public static function PrepareSettings($arFields) {
		$arDefView = self::GetDefaultListValues();
		$arView = array();
		if (
			array_key_exists('USER_TYPE_SETTINGS', $arFields) && is_array($arFields['USER_TYPE_SETTINGS']) &&
			array_key_exists('VIEW', $arFields['USER_TYPE_SETTINGS']) &&
			!empty($arFields['USER_TYPE_SETTINGS']['VIEW']) && is_array($arFields['USER_TYPE_SETTINGS']['VIEW'])
		) {
			$arView = $arFields['USER_TYPE_SETTINGS']['VIEW'];
		}

		if (empty($arView)) {
			$arView = $arDefView;
		}

		return array(
			'VIEW' => $arView
		);
	}

	protected function GetDefaultListValues() {
		return array(
			ASD_UT_CHECKBOX_VAL_NUM_FALSE => GetMessage('ASD_UT_CHECKBOX_NUM_VALUE_N'),
			ASD_UT_CHECKBOX_VAL_NUM_TRUE => GetMessage('ASD_UT_CHECKBOX_NUM_VALUE_Y')
		);
	}
}

class CASDiblockPropPalette {
	public static function GetUserTypeDescription() {
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => ASD_UT_PALETTE,
			'DESCRIPTION' => GetMessage('ASD_UT_PALETTE_DESCR'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
		);
	}

	public static function GetPropertyFieldHtml($arProperty, $arValue, $strHTMLControlName) {
		$strID = preg_replace('/[^a-zA-Z0-9_]/i', 'x', $strHTMLControlName['VALUE']);
		if (array_key_exists('MODE', $strHTMLControlName) && ('iblock_element_admin' == $strHTMLControlName['MODE'])) {
			$strResult = '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.htmlspecialcharsbx($strID).'" value="'.htmlspecialcharsbx($arValue['VALUE']).'" />';
		} else {
			CJSCore::Init(array('asd_palette'));
			$strResult = '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.htmlspecialcharsbx($strID).'" value="'.htmlspecialcharsbx($arValue['VALUE']).'" />';
			$strResult .= '<script type="text/javascript">
				BX.ready(function()
    			{
      				$(\'#'.htmlspecialcharsbx($strID).'\').jPicker({images: {clientPath : \'/bitrix/js/asd.iblock/jpicker/images/\'}});
    			});
				</script>';
		}
		return $strResult;
	}

	public static function GetPublicEditHtml($arProperty, $arValue, $strHTMLControlName) {
		$strID = preg_replace('/[^a-zA-Z0-9_]/i', 'x', $strHTMLControlName["VALUE"]);
		CJSCore::Init(array('asd_palette'));
		$strResult = '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.htmlspecialcharsbx($strID).'" value="'.htmlspecialcharsbx($arValue['VALUE']).'" />';
		$strResult .= '<script type="text/javascript">
			BX.ready(function()
   			{
     			$(\'#'.htmlspecialcharsbx($strID).'\').jPicker({images: {clientPath : \'/bitrix/js/asd.iblock/jpicker/images/\'}});
   			});
			</script>';
		return $strResult;
	}
}