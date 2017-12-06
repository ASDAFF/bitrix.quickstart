<?php
IncludeModuleLangFile(__FILE__);

define('ASD_UT_SECTION', 'SASDSection');

class CASDiblockPropSection {
	const VIEW_MODE_SELECT = 0;
	const VIEW_MODE_WINDOW = 1;

	protected static $cache = array();
	protected static $treeCache = null;

	public static function GetUserTypeDescription() {
		return array(
			'PROPERTY_TYPE' => 'N',
			'USER_TYPE' => ASD_UT_SECTION,
			'DESCRIPTION' => GetMessage('ASD_UT_SECTION_DESCR'),
			'CheckFields' => array(__CLASS__, 'CheckFields'),
			'ConvertToDB' => array(__CLASS__, 'ConvertToDB'),
			'ConvertFromDB' => array(__CLASS__, 'ConvertFromDB'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetAdminListViewHTML' => array(__CLASS__,'GetAdminListViewHTML'),
			'GetPublicViewHTML' => array(__CLASS__, 'GetPublicViewHTML'),
			'GetAdminFilterHTML' => array(__CLASS__,'GetAdminFilterHTML'),
			'GetSettingsHTML' => array(__CLASS__,'GetSettingsHTML'),
			'PrepareSettings' => array(__CLASS__,'PrepareSettings')
		);
	}

	protected static function GetTree($intIBlockID, $intMaxLevel) {
		if (self::$treeCache === null) {
			$arFilter = array('IBLOCK_ID' => $intIBlockID);
			if ($intMaxLevel > 0) {
				$arFilter['<=DEPTH_LEVEL'] = $intMaxLevel;
			}
			$rsSections = CIBlockSection::GetList(
				array('LEFT_MARGIN' => 'ASC'),
				$arFilter,
				false,
				array('ID', 'NAME', 'IBLOCK_ID', 'DEPTH_LEVEL')
			);
			while ($arSection = $rsSections->Fetch()) {
				self::$treeCache[] = $arSection;
				self::$cache[$arSection['ID']] = $arSection;
			}
		}
	}

	protected static function GetValue($intIBlockID, $intSectionID) {
		$strResult = '';
		if (isset($intSectionID)) {
			if (!isset(self::$cache[$intSectionID])) {
				$rsSections = CIBlockSection::GetList(
					array(),
					array('ID' => $intSectionID, 'IBLOCK_ID' => $intIBlockID),
					false,
					array('ID', 'IBLOCK_ID', 'NAME', 'DEPTH_LEVEL')
				);
				if ($arSection = $rsSections->Fetch()) {
					self::$cache[$intSectionID] = $arSection;
				}
			}
			if (isset(self::$cache[$intSectionID])) {
				$strResult = self::$cache[$intSectionID]['NAME'];
			}
		}
		return $strResult;
	}

	public static function CheckFields($arProperty, $value) {
		$arResult = array();
		if (isset($value['VALUE']) && !empty($value['VALUE']) && !empty($arProperty['IBLOCK_ID'])) {
			if (!isset(self::$cache[$value['VALUE']])) {
				$rsSections = CIBlockSection::GetList(
					array(),
					array('ID' => $value['VALUE']),
					false,
					array('ID', 'IBLOCK_ID', 'NAME', 'DEPTH_LEVEL')
				);
				if ($arSection = $rsSections->Fetch()) {
					self::$cache[$value['VALUE']] = $arSection;
				}
			}
			if (isset(self::$cache[$value['VALUE']])) {
				if (self::$cache[$value['VALUE']]['IBLOCK_ID'] != $arProperty['IBLOCK_ID']) {
					$arResult[] = GetMessage('ASD_UT_SECTION_NO_PARENT_IBLOCK');
				}
			}
		}
		return $arResult;
	}

	public function ConvertToDB($arProperty, $value) {
		if (isset($value['VALUE']) && $value['VALUE']>0) {
			$value['VALUE'] = intval($value['VALUE']);
		}
		return $value;
	}

	public static function ConvertFromDB($arProperty, $value) {
		if (isset($value['VALUE'])) {
			$value['VALUE'] = intval($value['VALUE']);
		}
		return $value;
	}

	public static function GetPropertyFieldHtml($arProperty, $arValue, $strHTMLControlName) {
		$strResult = '';
		$arSettings = self::PrepareSettings($arProperty);
		if (
			$arSettings['VIEW_MODE'] == self::VIEW_MODE_SELECT ||
			(isset($strHTMLControlName['MODE']) && $strHTMLControlName['MODE'] == 'EDIT_FORM')
		) {
			if (self::$treeCache === null) {
				self::GetTree($arProperty['IBLOCK_ID'], $arSettings['MAX_LEVEL']);
			}
			ob_start();
			?>
			<select name="<? echo $strHTMLControlName["VALUE"]; ?>" id="<? echo $strHTMLControlName["VALUE"]; ?>">
			<option value=""><? echo GetMessage('ASD_UT_SECTION_MESS_TOP_LEVEL'); ?></option>
			<?
			foreach (self::$treeCache as $arSection) {
				?>
				<option value="<? echo $arSection['ID']; ?>"<? echo ($arSection['ID'] == $arValue['VALUE'] ? ' selected' : ''); ?>><?
				echo str_repeat(' . ', $arSection['DEPTH_LEVEL']-1).htmlspecialcharsex($arSection['NAME']);
				?></option><?
			}
			?>
			</select>
			<?
			$strResult = ob_get_contents();
			ob_end_clean();
		}
		elseif ($arSettings['VIEW_MODE'] == self::VIEW_MODE_WINDOW) {
			ob_start();
			$strItemID = preg_replace("/[^a-zA-Z0-9_]/", "x", $strHTMLControlName['VALUE']);
			$strLink = '/bitrix/admin/iblock_section_search.php?lang='.LANGUAGE_ID.'&IBLOCK_ID='.$arProperty['IBLOCK_ID'].'&n='.$strItemID;
			if ($arValue['VALUE'] == 0) {
				?><input type="text" id="<? echo $strItemID; ?>" name="<? echo htmlspecialcharsbx($strHTMLControlName['VALUE']); ?>" value="" size="5">
				<input type="button" value="..." onclick="jsUtils.OpenWindow('<? echo $strLink; ?>', 900, 700);">
				<span id="sp_<? echo $strItemID; ?>"></span>
				<?
			} else {
				$strName = self::GetValue($arProperty['IBLOCK_ID'], $arValue['VALUE']);
				?><input type="text" id="<? echo $strItemID; ?>" name="<? echo htmlspecialcharsbx($strHTMLControlName['VALUE']); ?>" value="<? echo $arValue['VALUE']; ?>" size="5">
				<input type="button" value="..." onclick="jsUtils.OpenWindow('<? echo $strLink; ?>', 900, 700);">
				<span id="sp_<? echo $strItemID; ?>"><? echo htmlspecialcharsex($strName); ?></span>
				<?
			}
			$strResult = ob_get_contents();
			ob_end_clean();
		}

		return $strResult;
	}

	public static function GetAdminListViewHTML($arProperty, $arValue, $strHTMLControlName) {
		$strResult = '';
		$strName = self::GetValue($arProperty['IBLOCK_ID'], $arValue['VALUE']);
		if ($strName != '') {
			$strResult = htmlspecialcharsbx($strName).
			' [<a href="'.
			htmlspecialcharsbx(CIBlock::GetAdminSectionEditLink($arProperty['IBLOCK_ID'], $arValue['VALUE'])).
			'" title="'.GetMessage("IBEL_A_SEC_EDIT").'">'.$arValue['VALUE'].'</a>]';
		}
		return $strResult;
	}

	public static function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName) {
		$strResult= self::GetValue($arProperty['IBLOCK_ID'], $arValue['VALUE']);
		if ($strResult != '') {
			$strResult = htmlspecialcharsbx($strResult);
		}
		return $strResult;
	}

	public static function GetAdminFilterHTML($arProperty, $strHTMLControlName) {
		$strResult = '';
		ob_start();
		$strItemID = preg_replace("/[^a-zA-Z0-9_]/", "x", $strHTMLControlName['VALUE']);
		$strLink = '/bitrix/admin/iblock_section_search.php?lang='.LANGUAGE_ID.'&IBLOCK_ID='.$arProperty['IBLOCK_ID'].'&n='.$strItemID;
		$strValue = '';
		if (isset($GLOBALS[$strHTMLControlName['VALUE']]))
			$strValue = $GLOBALS[$strHTMLControlName['VALUE']];
		if ($strValue == 0) {
			?><input type="text" id="<? echo $strItemID; ?>" name="<? echo htmlspecialcharsbx($strHTMLControlName['VALUE']); ?>" value="" size="5">
			<input type="button" value="..." onclick="jsUtils.OpenWindow('<? echo $strLink; ?>', 900, 700);">
			 <span id="sp_<? echo $strItemID; ?>"></span>
			<?
		} else {
			$strName = self::GetValue($arProperty['IBLOCK_ID'], $strValue);
			?><input type="text" id="<? echo $strItemID; ?>" name="<? echo htmlspecialcharsbx($strHTMLControlName['VALUE']); ?>" value="<? echo $strValue; ?>" size="5">
			<input type="button" value="..." onclick="jsUtils.OpenWindow('<? echo $strLink; ?>', 900, 700);">
			 <span id="sp_<? echo $strItemID; ?>"><? echo htmlspecialcharsex($strName); ?></span>
			<?
		}
		$strResult = ob_get_contents();
		ob_end_clean();
		return $strResult;
	}

	public static function GetSettingsHTML($arFields, $strHTMLControlName, &$arPropertyFields) {
		$arPropertyFields = array(
			'USER_TYPE_SETTINGS_TITLE' => GetMessage('ASD_UT_SECTION_SETTING_TITLE'),
		);
		$arSettings = self::PrepareSettings($arFields);
		return '<tr>
		<td>'.GetMessage('ASD_UT_SECTION_SETTING_VIEW_MODE').'</td>
		<td>'.SelectBoxFromArray(
			$strHTMLControlName['NAME'].'[VIEW_MODE]',
			array(
				'REFERENCE' => array(
					GetMessage('ASD_UT_SECTION_SETTING_VIEW_MODE_SELECT'),
					GetMessage('ASD_UT_SECTION_SETTING_VIEW_MODE_WINDOW')
				),
				'REFERENCE_ID' => array(
					self::VIEW_MODE_SELECT,
					self::VIEW_MODE_WINDOW
				)
			),
			$arSettings['VIEW_MODE']
		).'</td>
		</tr>
		<tr>
		<td>'.GetMessage('ASD_UT_SECTION_SETTING_MAX_LEVEL').'</td>
		<td><input type="text" name="'.$strHTMLControlName['NAME'].'[MAX_LEVEL]" value="'.$arSettings['MAX_LEVEL'].'"> '.GetMessage('ASD_UT_SECTION_SETTING_MAX_LEVEL_DESCR').'</td>
		</tr>';
	}

	public static function PrepareSettings($arFields) {
		$intViewMode = self::VIEW_MODE_SELECT;
		$intMaxLevel = 0;
		if (isset($arFields['USER_TYPE_SETTINGS'])) {
			if (isset($arFields['USER_TYPE_SETTINGS']['VIEW_MODE'])) {
				$intViewMode = intval($arFields['USER_TYPE_SETTINGS']['VIEW_MODE']);
				if ($intViewMode != self::VIEW_MODE_WINDOW) {
					$intViewMode = self::VIEW_MODE_SELECT;
				}
			}
			if (isset($arFields['USER_TYPE_SETTINGS']['MAX_LEVEL'])) {
				$intMaxLevel = intval($arFields['USER_TYPE_SETTINGS']['MAX_LEVEL']);
			}
			if ($intMaxLevel < 0) {
				$intMaxLevel = 0;
			}
		}
		return array(
			'VIEW_MODE' => $intViewMode,
			'MAX_LEVEL' => $intMaxLevel
		);
	}
}