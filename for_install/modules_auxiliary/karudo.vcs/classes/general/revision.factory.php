<?php
IncludeModuleLangFile(__FILE__);

class CVCSRevisionDBResult extends CDBResult {
	public function __construct($res) {
		$this->CDBResult($res);
	}

	public function Fetch() {
		$arr = parent::Fetch();
		if (!empty($arr['USER_ID'])) {
			$arUser = CUser::GetByID($arr['USER_ID'])->Fetch();
			$arr['USER_LOGIN'] = $arUser['LOGIN'];
		}

		return $arr;
	}
}

class CVCSRevisionFactory {
	public static function AddNewRevision($description) {
		global $USER;
		$DB = CVCSMain::GetDB();
		$APPLICATION = CVCSMain::GetAPPLICATION();

		if (strlen($description) < 1) {
			//throw new CVCSAjaxExceptionServiceError();
			$APPLICATION->ThrowException(GetMessage('VCS_EMPTY_REV_DESC'));
			return false;
		}

		return (int) $DB->Add(CVCSConfig::TBL_REVISIONS, array(
			'DESCRIPTION' => $description,
			'USER_ID' => $USER->GetID(),
			'~DATEADD' => $DB->GetNowFunction()
		));
	}

	public static function GetList($arOrder = array(), $arFilter = array()) {
		$DB = CVCSMain::GetDB();
		$arFields = array(
			'ID' => array(
				'TABLE_ALIAS' => 'R',
				'FIELD_NAME' => 'R.ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'DESCRIPTION' => array(
				'TABLE_ALIAS' => 'R',
				'FIELD_NAME' => 'R.DESCRIPTION',
				'FIELD_TYPE' => 'string', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'DATEADD' => array(
				'TABLE_ALIAS' => 'R',
				'FIELD_NAME' => 'R.DATEADD',
				'FIELD_TYPE' => 'datetime', //int, double, file, enum, int, string, date, datetime
				'JOIN' => false,
			),
			'USER_ID' => array(
				'TABLE_ALIAS' => 'R',
				'FIELD_NAME' => 'R.USER_ID',
				'FIELD_TYPE' => 'int', //int, double, file, enum, int, string, date, datetime
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

		$strQuery = "SELECT
				$sSelectFields, COUNT(I.ID) as COUNT_ITEMS
			FROM " . CVCSConfig::TBL_REVISIONS . " R LEFT JOIN " . CVCSConfig::TBL_ITEMS . " I ON (R.ID = I.REVISION_ID) ";
		if (strlen($sWhere) > 0){
			$strQuery .= ' WHERE ' . $sWhere;
		}

		$strQuery .= ' GROUP BY R.ID ';

		if (strlen($sOrder)> 0) {
			$strQuery .= ' ORDER BY ' . $sOrder;
		}

		return $DB->Query($strQuery);
	}
}