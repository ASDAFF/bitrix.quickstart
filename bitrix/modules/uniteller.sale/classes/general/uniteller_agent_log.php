<?php

IncludeModuleLangFile(__FILE__);

/**
 * Класс для работы с таблицей с логами ошибок.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */
class CUnitellerAgentLog {
	var $LAST_ERROR = '';

	/**
	 * Возвращает список записей в соответствии с фильтром и сортировкой.
	 * @param array $aSort
	 * @param array $aFilter
	 * @return object
	 */
	function GetList($aSort = array(), $aFilter = array()) {
		global $DB;

		$arFilter = array();
		foreach ($aFilter as $key => $val) {
			$val = $DB->ForSql($val);
			if (strlen($val) <= 0) {
				continue;
			}
			switch (strtoupper($key)) {
				case 'ID':
					$arFilter[] = "L.ID='" . $val . "'";
					break;
				case 'ORDER_ID':
					$arFilter[] = "L.ORDER_ID='" . $val . "'";
					break;
				case 'INSERT_DATATIME':
					$arFilter[] = "L.INSERT_DATATIME='" . $val . "'";
					break;
				case 'TYPE_ERROR':
					$arFilter[] = "L.TYPE_ERROR='" . $val . "'";
					break;
				case 'TEXT_ERROR':
					$arFilter[] = "L.TEXT_ERROR='" . $val . "'";
					break;
			}
		}

		$arOrder = array();
		foreach ($aSort as $key => $val) {
			$ord = (strtoupper($val) <> 'ASC' ? 'DESC' : 'ASC');
			switch (strtoupper($key)) {
				case 'ID':
					$arOrder[] = 'L.ID ' . $ord;
					break;
				case 'ORDER_ID':
					$arOrder[] = 'L.ORDER_ID ' . $ord;
					break;
				case 'INSERT_DATATIME':
					$arOrder[] = 'L.INSERT_DATATIME ' . $ord;
					break;
				case 'TYPE_ERROR':
					$arOrder[] = 'L.TYPE_ERROR ' . $ord;
					break;
				case 'TEXT_ERROR':
					$arOrder[] = 'L.TEXT_ERROR ' . $ord;
					break;
			}
		}
		if (count($arOrder) == 0) {
			$arOrder[] = 'L.ID DESC';
		}
		$sOrder = "\n" . 'ORDER BY ' . implode(', ', $arOrder);

		if(count($arFilter) == 0) {
			$sFilter = '';
		} else {
			$sFilter = "\n" . 'WHERE ' . implode("\n" . 'AND ', $arFilter);
		}

		$strSql = '
			SELECT
				L.ID
				,L.ORDER_ID
				,L.INSERT_DATATIME
				,L.TYPE_ERROR
				,L.TEXT_ERROR
			FROM
				b_uniteller_agent L
			' . $sFilter . $sOrder;

		return $DB->Query($strSql, false, 'File: ' . __FILE__ . '<br>Line: ' . __LINE__);
	}

	/**
	 * Возвращает список типов ошибок.
	 * @return object
	 */
	function GetTypeList() {
		global $DB;

		$strSql = '
			SELECT
				TYPE_ERROR
			FROM
				b_uniteller_agent
			GROUP BY
				TYPE_ERROR
			';

		return $DB->Query($strSql, false, 'File: ' . __FILE__ . '<br>Line: ' . __LINE__);
	}

	/**
	 * Удаляет запись.
	 * @param integer $ID
	 * @return object
	 */
	function Delete($ID) {
		global $DB;
		$ID = intval($ID);

		$DB->StartTransaction();

		$res = $DB->Query('DELETE FROM b_uniteller_agent WHERE ID=' . $ID, false, 'File: ' . __FILE__ . '<br>Line: ' . __LINE__);

		if ($res) {
			$DB->Commit();
		} else {
			$DB->Rollback();
		}

		return $res;
	}
}
?>