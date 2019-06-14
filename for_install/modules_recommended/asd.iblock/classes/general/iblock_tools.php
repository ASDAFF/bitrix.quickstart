<?php

class CASDiblockTools {

	public static $arNotExport = array('ID', 'TIMESTAMP_X', 'IBLOCK_ID', 'TMP_ID', 'EXTERNAL_ID', 'PROPERTY_ID', 'PROPERTY_NAME', 'PROPERTY_SORT');

	public static function ExportSettingsToXML($BID, $arWhat) {
		$xml = '';
		if ($BID>0 && is_array($arWhat) && !empty($arWhat)) {
			if (in_array('forms', $arWhat)) {
				$xml .= '<form_element>';
				$xml .=		'<![CDATA['.array_pop(CUserOptions::GetOption('form', 'form_element_'.$BID, true)).']]>';
				$xml .= '</form_element>'."\n";
				$xml .= '<form_section>';
				$xml .=		'<![CDATA['.array_pop(CUserOptions::GetOption('form', 'form_section_'.$BID, true)).']]>';
				$xml .= '</form_section>'."\n";
			}
		}
		return $xml;
	}

	public static function ExportPropsToXML($BID, $arOnlyID=array()) {
		$xml = '';
		if (empty($arOnlyID)) {
			$arOnlyID = $_REQUEST['p'];
		}
		if ($BID>0 && CModule::IncludeModule('iblock')) {
			$xml .= "\t".'<props>'."\n";
			$arExported = array();
			$arCData = array('NAME', 'DEFAULT_VALUE', 'XML_ID', 'FILE_TYPE', 'USER_TYPE_SETTINGS', 'HINT', 'VALUE');
			$rsProp = CIBlockProperty::GetList(array(), array('IBLOCK_ID' => $BID));
			while ($arProp = $rsProp->Fetch()) {
				if (!empty($arOnlyID) && !isset($arOnlyID[$arProp['ID']])) {
					continue;
				}
				$arExported[] = $arProp['CODE'];
				$xml .= "\t\t".'<prop>'."\n";
				foreach ($arProp as $k => $v) {
					if ($k == 'ID') {
						$k = 'OLD_ID';
					}
					if (in_array($k, self::$arNotExport)) {
						continue;
					}
					if (in_array($k, $arCData) && strlen(trim($v))) {
						$v = '<![CDATA['.$v.']]>';
					}
					$xml .= "\t\t\t".'<'.strtolower($k).'>'.$v.'</'.strtolower($k).'>'."\n";
				}
				$xml .= "\t\t".'</prop>'."\n";
			}
			$xml .= "\t".'</props>'."\n";
			$xml .= "\t".'<enums>'."\n";
			$rsProp = CIBlockPropertyEnum::GetList(array(), array('IBLOCK_ID' => $BID));
			while ($arProp = $rsProp->Fetch()) {
				if (!in_array($arProp['PROPERTY_CODE'], $arExported)) {
					continue;
				}
				$xml .= "\t\t".'<enum>'."\n";
				foreach ($arProp as $k => $v) {
					if (in_array($k, self::$arNotExport)) {
						continue;
					}
					if (in_array($k, $arCData) && strlen(trim($v))) {
						$v = '<![CDATA['.$v.']]>';;
					}
					$xml .= "\t\t\t".'<'.strtolower($k).'>'.$v.'</'.strtolower($k).'>'."\n";
				}
				$xml .= "\t\t".'</enum>'."\n";
			}
			$xml .= "\t".'</enums>'."\n";
		}
		return $xml;
	}

	public static function ImportFormsFromXML($BID, $xmlPath, $arOldNewID) {
		if (file_exists($xmlPath) && $BID && CModule::IncludeModule('iblock')) {
			require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');
			$xml = new CDataXML();
			if ($xml->Load($xmlPath)) {
				if ($node = $xml->SelectNodes('/asd_iblock_props/form_element/')) {
					$tabs = $node->textContent();
					foreach ($arOldNewID as $old => $new) {
						$tabs = str_replace('--PROPERTY_'.$old.'--', '--PROPERTY_'.$new.'--', $tabs);
					}
					$arOptions = array(array(
						'd' => 'Y',
						'c' => 'form',
						'n' => 'form_element_'.$BID,
						'v' => array('tabs' => $tabs)
					));
					CUserOptions::SetOptionsFromArray($arOptions);
				}
				if ($node = $xml->SelectNodes('/asd_iblock_props/form_section/')) {
					$tabs = $node->textContent();
					$arOptions = array(array(
						'd' => 'Y',
						'c' => 'form',
						'n' => 'form_section_'.$BID,
						'v' => array('tabs' => $tabs)
					));
					CUserOptions::SetOptionsFromArray($arOptions);
				}
			}
		}
	}

	public static function ImportPropsFromXML($BID, $xmlPath, &$arOldNewID) {
		if (file_exists($xmlPath) && $BID && CModule::IncludeModule('iblock')) {

			require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');

			$arExistProps = array();
			$rsProp = CIBlockProperty::GetList(array(), array('IBLOCK_ID' => $BID));
			while ($arProp = $rsProp->Fetch()) {
				$arExistProps[$arProp['CODE']] = $arProp;
			}

			$arExistEnums = array();
			$rsEnum = CIBlockPropertyEnum::GetList(array(), array('IBLOCK_ID' => $BID));
			while ($arEnum = $rsEnum->Fetch()) {
				$arExistEnums[$arEnum['PROPERTY_ID'].'_'.$arEnum['XML_ID']] = $arEnum;
			}

			$arOldNewID = array();
			$xml = new CDataXML();
			$ep = new CIBlockProperty();
			$en = new CIBlockPropertyEnum();
			if ($xml->Load($xmlPath)) {
				if ($node = $xml->SelectNodes('/asd_iblock_props/props/')) {
					foreach ($node->children() as $child) {
						$arProp = array_pop($child->__toArray());
						$arFields = array('IBLOCK_ID' => $BID);
						foreach ($arProp as $code => $v) {
							$arFields[strtoupper($code)] = isset($v[0]['#']['cdata-section']) && is_array($v[0]['#']['cdata-section']) ? $v[0]['#']['cdata-section'][0]['#'] : $v[0]['#'];
						}
						if (isset($arExistProps[$arFields['CODE']])) {
							$arOldNewID[$arFields['OLD_ID']] = $arExistProps[$arFields['CODE']]['ID'];
							$ep->Update($arExistProps[$arFields['CODE']]['ID'], $arFields);
						} else {
							$arOldNewID[$arFields['OLD_ID']] = $arFields['ID'] = $ep->Add($arFields);
							$arExistProps[$arFields['CODE']] = $arFields;
						}
					}
				}
				if ($node = $xml->SelectNodes('/asd_iblock_props/enums/')) {
					foreach ($node->children() as $child) {
						$arProp = array_pop($child->__toArray());
						$arFields = array('IBLOCK_ID' => $BID);
						foreach ($arProp as $code => $v) {
							$arFields[strtoupper($code)] = isset($v[0]['#']['cdata-section']) && is_array($v[0]['#']['cdata-section']) ? $v[0]['#']['cdata-section'][0]['#'] : $v[0]['#'];
						}
						$arFields['PROPERTY_ID'] = $arExistProps[$arFields['PROPERTY_CODE']]['ID'];
						if (isset($arExistEnums[$arFields['PROPERTY_ID'].'_'.$arFields['XML_ID']])) {
							$en->Update($arExistEnums[$arFields['PROPERTY_ID'].'_'.$arFields['XML_ID']]['ID'], $arFields);
						} else {
							$en->Add($arFields);
						}
					}
				}
			}
		}
	}

	public static function GetIBUF($BID, $CODE=false) {
		global $USER_FIELD_MANAGER, $APPLICATION;
		$arReturn = array();
		$arUserFields = $USER_FIELD_MANAGER->GetUserFields(CASDiblock::$UF_IBLOCK, $BID, LANGUAGE_ID);
		foreach($arUserFields as $FIELD_NAME => $arUserField) {
			if ($arUserField['USER_TYPE_ID'] == 'enumeration') {
				$arValue = array();
				$rsSecEnum = CUserFieldEnum::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), array('USER_FIELD_ID' => $arUserField['ID'], 'ID' => $arUserField['VALUE']));
				while ($arSecEnum = $rsSecEnum->Fetch()) {
					$arValue[$arSecEnum['ID']] = $arSecEnum['VALUE'];
				}
				$arReturn[$FIELD_NAME] = $arValue;
			} else {
				$arReturn[$FIELD_NAME] = $arUserField['VALUE'];
			}
		}
		return $CODE===false ? $arReturn : $arReturn[$CODE];
	}

	public static function SetIBUF($BID, $arFields) {
		global $USER_FIELD_MANAGER;
		$USER_FIELD_MANAGER->Update(CASDiblock::$UF_IBLOCK, $BID, $arFields);
	}
}

class CASDIblockElementTools {
	/**
	 * Get seo field templates.
	 *
	 * @param int $iblockId			Iblock ID.
	 * @param int $elementId		Element ID.
	 * @param bool $getAll			Get with inherited.
	 * @return array
	 */
	public static function getSeoFieldTemplates($iblockId, $elementId, $getAll = false) {
		$result = array();

		if (!CASDiblockVersion::checkMinVersion('14.0.0')) {
			return $result;
		}

		$getAll = ($getAll === true);
		$seoTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates($iblockId, $elementId);
		$elementTemplates = $seoTemplates->findTemplates();
		if (empty($elementTemplates) || !is_array($elementTemplates)) {
			return $result;
		}
		foreach ($elementTemplates as &$fieldTemplate) {
			if (!$getAll && (!isset($fieldTemplate['INHERITED']) || $fieldTemplate['INHERITED'] !== 'N')) {
				continue;
			}
			$result[$fieldTemplate['CODE']] = $fieldTemplate['TEMPLATE'];
		}
		unset($fieldName, $data);

		return $result;
	}
}