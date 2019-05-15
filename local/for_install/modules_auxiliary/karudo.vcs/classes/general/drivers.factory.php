<?php
IncludeModuleLangFile(__FILE__);

class CVCSDriversFactory {

	private static function CheckFields(&$arFields, $ID = 0) {
		unset($arFields['ID']);
		if ($ID > 0) {
			unset($arFields['DRIVER_CODE']);
		} else {
			if (empty($arFields['DRIVER_CODE'])) {
				CVCSMain::GetAPPLICATION()->ThrowException(GetMessage('VCS_ERR_EMPTY_DRIVER_CODE'));
				return false;
			}

			if (!preg_match('#^[a-z][a-z0-9_]+#', $arFields['DRIVER_CODE'])) {
				CVCSMain::GetAPPLICATION()->ThrowException(GetMessage('VCS_ERR_INVALID_SYMBOLS'));
				return false;
			}
			if (self::GetList(false, array('DRIVER_CODE' => $arFields['DRIVER_CODE']))->Fetch()) {
				CVCSMain::GetAPPLICATION()->ThrowException(GetMessage('VCS_ERR_DRIVER_CODE_EXISTS'));
				return false;
			}
		}

		if (
			(array_key_exists('NAME', $arFields) && strlen($arFields['NAME']) < 1) ||
			($ID < 1 && (!array_key_exists('NAME', $arFields) || strlen($arFields['NAME']) < 1))
		) {
			CVCSMain::GetAPPLICATION()->ThrowException(GetMessage('VCS_ERR_EMPTY_NAME'));
			return false;
		}

		if (array_key_exists('SETTINGS', $arFields) && is_array($arFields['SETTINGS'])) {
			$arFields['SETTINGS'] = serialize($arFields['SETTINGS']);
		}

		return true;
	}

	public static function Add($arFields) {
		if (!self::CheckFields($arFields)) {
			return false;
		}
		$DB = CVCSMain::GetDB();
		unset($arFields['TIMESTAMP_X']);
		$arFields['~TIMESTAMP_X'] = $DB->GetNowFunction();

		return $DB->Add(CVCSConfig::TBL_DRIVERS, $arFields);
	}

	public static function Update($ID, $arFields) {
		$DB = CVCSMain::GetDB();
		$ID = intval($ID);

		if (!self::CheckFields($arFields, $ID)) {
			return false;
		}
		foreach ($arFields as $k => $v) {
			if (substr($k, 0, 1) == '~') {
				$arFields[ltrim($k, '~')] = $v;
				unset($arFields[$k]);
			} else {
				$arFields[$k] = CVCSMain::DBForSql($v);
			}
		}

		$arFields['TIMESTAMP_X'] = $DB->GetNowFunction();
		return (int) $DB->Update(CVCSConfig::TBL_DRIVERS, $arFields, 'WHERE ID=' . $ID);
	}

	public static function GetList($arOrder = array(), $arFilter = array()) {
		$DB = CVCSMain::GetDB();
		$arFields = array(
			'ID' => array(
				'TABLE_ALIAS' => 'D',
				'FIELD_NAME' => 'D.ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'DRIVER_CODE' => array(
				'TABLE_ALIAS' => 'D',
				'FIELD_NAME' => 'D.DRIVER_CODE',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'NAME' => array(
				'TABLE_ALIAS' => 'D',
				'FIELD_NAME' => 'D.NAME',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'ACTIVE' => array(
				'TABLE_ALIAS' => 'D',
				'FIELD_NAME' => 'D.ACTIVE',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'SETTINGS' => array(
				'TABLE_ALIAS' => 'D',
				'FIELD_NAME' => 'D.SETTINGS',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'TIMESTAMP_X' => array(
				'TABLE_ALIAS' => 'D',
				'FIELD_NAME' => 'D.TIMESTAMP_X',
				'FIELD_TYPE' => 'datetime', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'LAST_CHECK' => array(
				'TABLE_ALIAS' => 'D',
				'FIELD_NAME' => 'D.LAST_CHECK',
				'FIELD_TYPE' => 'datetime', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
		);

		$sSelectFields = '';
		foreach ($arFields as $field => $arFieldInfo) {
			if (strlen($sSelectFields) > 0) {
				$sSelectFields .= ', ';
			}
			$sSelectFields .= (($arFieldInfo['FIELD_TYPE'] === 'datetime') ? $DB->DateToCharFunction($arFieldInfo['FIELD_NAME']) : $arFieldInfo['FIELD_NAME']) . ' ' . $field;
		}

		$obQueryWhere = new CSQLWhere;
		$obQueryWhere->SetFields($arFields);

		$sWhere = $obQueryWhere->GetQuery($arFilter);

		$sOrder = '';
		if (is_array($arOrder)) {
			foreach ($arOrder as $k => $v) {
				if (array_key_exists($k, $arFields)) {
					$v = strtoupper($v);
					if ($v != 'DESC') {
						$v  ='ASC';
					}
					if (strlen($sOrder) > 0) {
						$sOrder .= ', ';
					}
					$sOrder .= $arFields[$k]['FIELD_NAME'] . ' ' . $v;
				}
			}
		}

		$strQuery = "SELECT $sSelectFields FROM " . CVCSConfig::TBL_DRIVERS . " D ";
		if (strlen($sWhere) > 0){
			$strQuery .= ' WHERE ' . $sWhere;
		}

		if (strlen($sOrder)> 0) {
			$strQuery .= ' ORDER BY ' . $sOrder;
		}

		return $DB->Query($strQuery);
	}

	public static function UpdateLastCheck($code) {
		$arr = self::GetList(array(), array('=DRIVER_CODE' => $code))->Fetch();
		if ($arr) {
			self::Update($arr['ID'], array('~LAST_CHECK' => CVCSMain::GetDB()->GetNowFunction()));
		}
	}

}