<?php

class CVCSItem {
	private $id = false;
	private $driver;
	private $orig_id;
	private $orig_id_hash;
	//private $loaded = false;
	private $revision_id;
	private $first_revision_id;
	private $deleted;

	public function __construct($id = false) {
		if (false !== $id) {
			$this->id = intval($id);
		}
	}

	public function GetID() {
		return $this->id;
	}

	public function GetDriverCode() {
		return $this->driver;
	}

	public function GetOrigID() {
		return $this->orig_id;
	}

	public function GetOrigIDHash() {
		return $this->orig_id_hash;
	}

	public function GetLastRevisionID() {
		return $this->revision_id;
	}

	public function GetDeleted() {
		return $this->deleted;
	}

	public function save() {
		$DB = CVCSMain::GetDB();
		if (empty($this->id)) {
			$ID = $DB->Add(CVCSConfig::TBL_ITEMS,  array(
				'DRIVER_CODE' => $this->driver,
				'ORIG_ID' => $this->orig_id,
				'ORIG_ID_HASH' => $this->orig_id_hash,
				'FIRST_REVISION_ID' => $this->first_revision_id,
				//'REVISION_ID'
			));
			$this->id = intval($ID);
		}
	}

	public function AddNewRevisionFromCI($revision_id) {
		$DB = CVCSMain::GetDB();
		$DB->StartTransaction();
		$this->save();
		if (!empty($this->id)) {
			$this->revision_id = (int) $revision_id;
			$strSql = 'INSERT INTO ' . CVCSConfig::TBL_SOURCES . ' (ITEM_ID, REVISION_ID, SOURCE_HASH, SOURCE, TIMESTAMP_X, IS_NEW) ' .
				'SELECT ' . CVCSMain::DBForSql($this->id) . ', ' . CVCSMain::DBForSql($this->revision_id) . ', CI.SOURCE_HASH, CI.SOURCE,  ' . $DB->GetNowFunction() . ', if(CI.`STATUS`='.CVCSMain::DBForSql(CVCSConfig::CIST_NEW).', 1, 0) ' .
				'FROM ' . CVCSConfig::TBL_CHANGED_ITEMS . ' CI ' .
				'WHERE CI.DRIVER_CODE=' . CVCSMain::DBForSql($this->driver) . ' AND CI.ORIG_ID_HASH=' . CVCSMain::DBForSql($this->orig_id_hash);
			$DB->Query($strSql);
			$DB->Update(CVCSConfig::TBL_ITEMS, array('REVISION_ID' => $this->revision_id, 'TIMESTAMP_X' => $DB->GetNowFunction()), ' WHERE ID='.$this->id);
			$DB->Query('DELETE FROM ' . CVCSConfig::TBL_CHANGED_ITEMS . ' WHERE DRIVER_CODE=' . CVCSMain::DBForSql($this->driver) . ' AND ORIG_ID_HASH=' . CVCSMain::DBForSql($this->orig_id_hash) );
		}
		$DB->Commit();
	}

	public function DeleteByCI($revision_id) {
		$DB = CVCSMain::GetDB();
		if (!empty($this->id)) {
			$this->deleted = true;
			$DB->Add(CVCSConfig::TBL_SOURCES, array(
				'ITEM_ID' => $this->id,
				'REVISION_ID' => $revision_id,
				'DELETED' => 1,
				'~TIMESTAMP_X' => $DB->GetNowFunction(),
			));
			$DB->Update(CVCSConfig::TBL_ITEMS, array('DELETED' => 1), ' WHERE ID=' . intval($this->id));
			$DB->Query('DELETE FROM ' . CVCSConfig::TBL_CHANGED_ITEMS . ' WHERE DRIVER_CODE=' . CVCSMain::DBForSql($this->driver) . ' AND ORIG_ID_HASH=' . CVCSMain::DBForSql($this->orig_id_hash) );
		}
	}

	public function GetRevisionsList($arOrder = array(), $arFilter = array(), $arParams = array(), $arSelect = false) {
		$DB = CVCSMain::GetDB();
		if (empty($arOrder)) {
			$arOrder = array('REVISION_ID' => 'DESC');
		}
		$arFilter['ITEM_ID'] = $this->id;
		$arFields = array(
			'ID' => array(
				'TABLE_ALIAS' => 'S',
				'FIELD_NAME' => 'S.ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'ITEM_ID' => array(
				'TABLE_ALIAS' => 'S',
				'FIELD_NAME' => 'S.ITEM_ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'REVISION_ID' => array(
				'TABLE_ALIAS' => 'S',
				'FIELD_NAME' => 'S.REVISION_ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'SOURCE_HASH' => array(
				'TABLE_ALIAS' => 'S',
				'FIELD_NAME' => 'S.SOURCE_HASH',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'SOURCE' => array(
				'TABLE_ALIAS' => 'S',
				'FIELD_NAME' => 'S.SOURCE',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'TIMESTAMP_X' => array(
				'TABLE_ALIAS' => 'S',
				'FIELD_NAME' => 'S.TIMESTAMP_X',
				'FIELD_TYPE' => 'datetime', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'IS_NEW' => array(
				'TABLE_ALIAS' => 'S',
				'FIELD_NAME' => 'S.IS_NEW',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'DELETED' => array(
				'TABLE_ALIAS' => 'S',
				'FIELD_NAME' => 'S.DELETED',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'REVISION_DESC' => array(
				'TABLE_ALIAS' => 'R',
				'FIELD_NAME' => 'R.DESCRIPTION',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => "INNER JOIN " . CVCSConfig::TBL_REVISIONS . " R ON (S.REVISION_ID=R.ID)",
			),
			'USER_ID' => array(
				'TABLE_ALIAS' => 'R',
				'FIELD_NAME' => 'R.USER_ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => "INNER JOIN " . CVCSConfig::TBL_REVISIONS . " R ON (S.REVISION_ID=R.ID)",
			),
		);

		$sSelectFields = '';
		foreach ($arFields as $field => $arFieldInfo) {
			if ($arSelect === false || (is_array($arSelect) && in_array($field, $arSelect))) {
				if (strlen($sSelectFields) > 0) {
					$sSelectFields .= ', ';
				}
				$sSelectFields .= (($arFieldInfo['FIELD_TYPE'] === 'datetime') ? $DB->DateToCharFunction($arFieldInfo['FIELD_NAME']) : $arFieldInfo['FIELD_NAME']) . ' ' . $field;
			}
		}

		$obQueryWhere = new CSQLWhere;
		$obQueryWhere->SetFields($arFields);

		$sWhere = $obQueryWhere->GetQuery($arFilter);
		$sJoin = $obQueryWhere->GetJoins();

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

		$strSql = 'SELECT ' . $sSelectFields . ' FROM ' . CVCSConfig::TBL_SOURCES . ' S ';
		if (strlen($sJoin) > 0) {
			$strSql .= $sJoin . " ";
		}

		if (strlen($sWhere) > 0) {
			$strSql .= ' WHERE ' . $sWhere;
		}

		if (strlen($sOrder) > 0) {
			$strSql .= ' ORDER BY ' . $sOrder;
		}

		if (!empty($arParams['limit'])) {
			$strSql .= ' LIMIT ' . intval($arParams['limit']);
		}

		return $DB->Query($strSql);
	}

	private function  _GetFieldValueFromRevision($field, $revision_id) {
		$arItem = self::GetRevisionsList(
			array(),
			array('<=REVISION_ID' => $revision_id ? intval($revision_id) : $this->revision_id),
			array('limit' => 1),
			array($field)
		)->Fetch();

		if ($arItem) {
			return $arItem[$field];
		}

		return false;
	}

	/**
	 * @param int $revision_id
	 * @return string|bool
	 */
	public function GetSourceHash($revision_id = 0) {
		return $this->_GetFieldValueFromRevision('SOURCE_HASH', $revision_id);
	}

	/**
	 * @param int $revision_id
	 * @return string|bool
	 */
	public function GetSource($revision_id = 0) {
		return $this->_GetFieldValueFromRevision('SOURCE', $revision_id);
	}

	public function Delete() {
		$DB = CVCSMain::GetDB();
		if (!empty($this->id)) {
			$this->deleted = true;
			$DB->Update(CVCSConfig::TBL_ITEMS, array('DELETED' => 1), ' WHERE ID=' . intval($this->id));
		}
	}

	/**
	 * @static
	 * @param array $arItem
	 * @return CVCSItem
	 */
	public static function GetByItemArray($arItem) {
		$Item = new self(empty($arItem['ID']) ? false : $arItem['ID']);
		$Item->driver = $arItem['DRIVER_CODE'];
		$Item->orig_id = $arItem['ORIG_ID'];
		$Item->orig_id_hash = $arItem['ORIG_ID_HASH'];
		$Item->first_revision_id = (int) $arItem['FIRST_REVISION_ID'];
		$Item->revision_id = (int) $arItem['REVISION_ID'];
		$Item->deleted = !empty($arItem['DELETED']);

		return $Item;
	}

	/**
	 * @static
	 * @param string $driver_code
	 * @param string $orig_id_hash
	 * @return CVCSItem|null
	 */
	public static function GetByDiverCodeOrigIdHash($driver_code, $orig_id_hash) {
		$arItem = CVCSItemFactory::GetList(false, array('=DRIVER_CODE' => $driver_code, '=ORIG_ID_HASH' => $orig_id_hash))->Fetch();
		if ($arItem) {
			return self::GetByItemArray($arItem);
		}

		return null;
	}

	/**
	 * @static
	 * @param int $ID
	 * @return CVCSItem|null
	 */
	public static function GetByID($ID) {
		$arItem = CVCSItemFactory::GetList(false, array('=ID' => $ID))->Fetch();
		if ($arItem) {
			return self::GetByItemArray($arItem);
		}

		return null;
	}
}