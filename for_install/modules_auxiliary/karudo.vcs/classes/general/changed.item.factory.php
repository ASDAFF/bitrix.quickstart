<?php

class CVCSChangedItemFactory {

	public static function GetItemsCount() {
		$DB = CVCSMain::GetDB();
		$arr = $DB->Query('SELECT COUNT(ID) as cnt FROM ' . CVCSConfig::TBL_CHANGED_ITEMS)->Fetch();

		return intval($arr['cnt']);
	}

	public static function Clear() {
		$DB = CVCSMain::GetDB();
		$DB->Query('DELETE FROM ' . CVCSConfig::TBL_CHANGED_ITEMS);
	}

	public static function Delete($ID) {
		$DB = CVCSMain::GetDB();
		$DB->Query('DELETE FROM ' . CVCSConfig::TBL_CHANGED_ITEMS . ' WHERE ID=' . intval($ID));
	}

	public static function DeleteByDiverCodeOrigIdHash($driver_code, $orig_id_hash) {
		$DB = CVCSMain::GetDB();
		$DB->Query(
			'DELETE FROM ' . CVCSConfig::TBL_CHANGED_ITEMS . ' WHERE DRIVER_CODE=' . CVCSMain::DBForSql($driver_code)
				. ' AND ORIG_ID_HASH=' . CVCSMain::DBForSql($orig_id_hash)
		);
	}

	public static function AddItem(CVCSDriverItemAbstract $DriverItem) {
		$DB = CVCSMain::GetDB();

		$DB->StartTransaction();

		$status = '';
		$bAddChangedItem = true;
		if ($DriverItem->IsExists()) {
			$Item = CVCSItem::GetByDiverCodeOrigIdHash($DriverItem->GetDriverCode(), $DriverItem->GetIDHash());
			if ($Item) {
				if ($Item->GetDeleted() || ($DriverItem->GetSourceHash() === $Item->GetSourceHash())) {
					$bAddChangedItem = false;
				} else {
					$status = CVCSConfig::CIST_UPD;
				}
			} else {
				$status = CVCSConfig::CIST_NEW;
			}
		} else {
			$status = CVCSConfig::CIST_DEL;
		}

		if($bAddChangedItem) {
			$arItem = array(
				'DRIVER_CODE' => $DriverItem->GetDriverCode(),
				'ORIG_ID' => $DriverItem->GetID(),
				'ORIG_ID_HASH' => $DriverItem->GetIDHash(),
				'STATUS' => $status,
				'~TIMESTAMP_X' => $DB->GetNowFunction(),
			);

			if ($status != CVCSConfig::CIST_DEL) {
				$arItem['SOURCE_HASH'] = $DriverItem->GetSourceHash();
				$arItem['SOURCE'] = $DriverItem->GetSource();
			}

			$ID = (int) $DB->Add(CVCSConfig::TBL_CHANGED_ITEMS, $arItem, array('SOURCE'), '', false);
		}

		$DB->Commit();

		return isset($ID) ? $ID : $bAddChangedItem;
	}

	public static function GetItemsArray($arFilter = array(), $limit =  100) {
		$arResult = array();
		$rs = self::GetList(array(), $arFilter, array('limit' => $limit));
		while ($arr = $rs->Fetch()) {
			unset($arr['ID'], $arr['TIMESTAMP_X']);
			$arResult[] = $arr;
		}

		return $arResult;
	}

	public static function GetList($arOrder = array(), $arFilter = array(), $arParams = array()) {
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
			'STATUS' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.STATUS',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			/*'IS_NEW' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.IS_NEW',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),*/
			'TIMESTAMP_X' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.TIMESTAMP_X',
				'FIELD_TYPE' => 'datetime', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'SOURCE' => array(
				'TABLE_ALIAS' => 'I',
				'FIELD_NAME' => 'I.SOURCE',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
		);

		$sSelectFields = '';
		foreach ($arFields as $field => $arFieldInfo) {
			if ($field != 'SOURCE' || !empty($arParams['select_source'])) {
				if (strlen($sSelectFields) > 0) {
					$sSelectFields .= ', ';
				}
				$sSelectFields .= (($arFieldInfo['FIELD_TYPE'] === 'datetime') ? $DB->DateToCharFunction($arFieldInfo['FIELD_NAME']) : $arFieldInfo['FIELD_NAME']) . ' ' . $field;
			}
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

		$strSql = "SELECT $sSelectFields FROM " . CVCSConfig::TBL_CHANGED_ITEMS . " I";

		if (strlen($sWhere) > 0) {
			$strSql .= " WHERE " . $sWhere;
		}

		if (strlen($sOrder) > 0) {
			$strSql .= " ORDER BY " . $sOrder;
		}

		if (!empty($arParams['limit'])) {
			$strSql .= " LIMIT " . intval($arParams['limit']);
		}

		return $DB->Query($strSql, false, "File: " . __FILE__ . ' Line: ' . __LINE__);
	}

	public static function SetDeleted($id) {
		$DB = CVCSMain::GetDB();
		$DB->Update(CVCSConfig::TBL_CHANGED_ITEMS, array(
			'SOURCE' => 'NULL',
			'SOURCE_HASH' => 'NULL',
			'STATUS' => CVCSMain::DBForSql(CVCSConfig::CIST_DEL),
		), "WHERE ID=" . intval($id));
	}

	public static function AddForDelete(CVCSItem $Item) {
		$DB = CVCSMain::GetDB();

		self::DeleteByDiverCodeOrigIdHash($Item->GetDriverCode(), $Item->GetOrigIDHash());

		$arItem = array(
			'DRIVER_CODE' => $Item->GetDriverCode(),
			'ORIG_ID' => $Item->GetOrigID(),
			'ORIG_ID_HASH' => $Item->GetOrigIDHash(),
			'STATUS' => CVCSConfig::CIST_DEL,
			'~TIMESTAMP_X' => $DB->GetNowFunction(),
		);

		return (int) $DB->Add(CVCSConfig::TBL_CHANGED_ITEMS, $arItem, array('SOURCE'));
	}
}