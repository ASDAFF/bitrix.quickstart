<?php

class CVCSItemFactory {

	public static function GetItemsArray($arOrder = array(), $arFilter = array(), $arParams = array(), $arSelect = false) {
		$arItems = array();
		$rs = self::GetList($arOrder, $arFilter, $arParams, $arSelect);
		while ($arr = $rs->Fetch()) {
			$arItems[] = $arr;
		}

		return $arItems;
	}

	public static function GetList($arOrder = array(), $arFilter = array(), $arParams = array(), $arSelect = false) {
		$DB = CVCSMain::GetDB();
		$arFields = array(
			'ID' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'DRIVER_CODE' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.DRIVER_CODE',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'ORIG_ID' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.ORIG_ID',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'ORIG_ID_HASH' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.ORIG_ID_HASH',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'DELETED' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.DELETED',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'FIRST_REVISION_ID' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.FIRST_REVISION_ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'REVISION_ID' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.REVISION_ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'TIMESTAMP_X' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.TIMESTAMP_X',
				'FIELD_TYPE' => 'datetime', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'SOURCE_REVISION_ID' => array(
				'TABLE_ALIAS' => 'S',
				'FIELD_NAME' => 'S.REVISION_ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => 'INNER JOIN ' . CVCSConfig::TBL_SOURCES . ' S ON ( I.ID = S.ITEM_ID )',
			),
			'REVISIONS_COUNT' => array(
				'TABLE_ALIAS' => 'S2',
				'FIELD_NAME' => 'COUNT(S2.ID)',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => 'LEFT JOIN ' . CVCSConfig::TBL_SOURCES . ' S2 ON ( I.ID = S2.ITEM_ID )',
			),
			'DELETED_IN_REVISION' => array(
				'TABLE_ALIAS' => 'S3',
				'FIELD_NAME' => 'S3.REVISION_ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => 'LEFT JOIN ' . CVCSConfig::TBL_SOURCES . ' S3 ON ( I.ID = S3.ITEM_ID AND S3.DELETED = 1)',
			),
		);

		$obQueryWhere = new CSQLWhere;
		$obQueryWhere->SetFields($arFields);
		$sWhere = $obQueryWhere->GetQuery($arFilter);
		$sJoins = $obQueryWhere->GetJoins();

		$bSelectRevCount = is_array($arSelect) && in_array('REVISIONS_COUNT', $arSelect);

		$sSelectFields = '';
		foreach ($arFields as $field => $arFieldInfo) {
			if ((is_array($arSelect) && in_array($field, $arSelect)) || ($arSelect === false && $arFieldInfo['TABLE_ALIAS'] === 'I')) {
				if (strlen($sSelectFields) > 0) {
					$sSelectFields .= ', ';
				}
				$sSelectFields .= (($arFieldInfo['FIELD_TYPE'] === 'datetime') ? $DB->DateToCharFunction($arFieldInfo['FIELD_NAME']) : $arFieldInfo['FIELD_NAME']) . ' ' . $field;

				if (!empty($arFieldInfo['JOIN']) && strpos($sJoins, $arFieldInfo['JOIN']) === false) {
					$sJoins .= "\n" . $arFieldInfo['JOIN'];
				}
			}
		}

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

					if ($arFields[$k]['TABLE_ALIAS'] != 'I') {
						$sOrder .= $k . ' ' . $v;
					} else {
						$sOrder .= $arFields[$k]['FIELD_NAME'] . ' ' . $v;
					}
				}
			}
		}

		$strQuery = 'SELECT DISTINCT ' . $sSelectFields . ' FROM ' . CVCSConfig::TBL_ITEMS . ' I ';

		if (strlen($sJoins) > 0) {
			$strQuery .= $sJoins . ' ';
		}

		if (strlen($sWhere) > 0) {
			$strQuery .= ' WHERE ' . $sWhere;
		}
		if ($bSelectRevCount) {
			$strQuery .= ' GROUP BY I.ID ';
		}
		if (strlen($sOrder) > 0) {
			$strQuery .= ' ORDER BY ' . $sOrder;
		}
		if (!empty($arParams['limit'])) {
			$strQuery .= ' LIMIT ' . intval($arParams['limit']);
		}


		return $DB->Query($strQuery, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	public static function GetNotChangedItems($last_id = 0, $drivers = array()) {
		$arResult = array();
		$sql = 'SELECT I.ID ID, I.DRIVER_CODE DRIVER_CODE, I.ORIG_ID ORIG_ID
		FROM ' . CVCSConfig::TBL_ITEMS . ' I
		LEFT JOIN ' . CVCSConfig::TBL_CHANGED_ITEMS . ' CI ON ( I.DRIVER_CODE = CI.DRIVER_CODE
		AND I.ORIG_ID_HASH = CI.ORIG_ID_HASH)
		WHERE I.ID > '.intval($last_id).' AND I.DELETED=0 AND CI.ID IS NULL
		'.(empty($drivers) ? '' : 'AND I.DRIVER_CODE IN ('.implode(',', array_map(array('CVCSMain', 'DBForSql'), $drivers)).')').'
		ORDER BY I.ID ASC
		LIMIT 100';

		$rs = CVCSMain::GetDB()->Query($sql);
		while ($arr = $rs->Fetch()) {
			$arResult[] = $arr;
		}

		return $arResult;
	}
}

