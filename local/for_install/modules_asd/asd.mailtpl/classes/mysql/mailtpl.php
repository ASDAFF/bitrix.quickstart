<?php

class CASDMailTplDB {

	const MySQLerror = 'MySQL error in class CASDMailTplDB on line ';

	public static function Add($arFields) {
		$arEvents = $arFields['EVENTS'];
		if (is_array($arFields['SETTINGS'])) {
			$arFields['SETTINGS'] = serialize($arFields['SETTINGS']);
		}
		$arFields = array(
			'NAME' => trim($arFields['NAME']),
			'HEADER' => trim($arFields['HEADER']),
			'FOOTER' => trim($arFields['FOOTER']),
			'TYPE' => $arFields['TYPE']=='html' ? 'html' : 'text',
			'SETTINGS' => $arFields['SETTINGS'],
		);
		if (!strlen($arFields['NAME']) || !strlen($arFields['TYPE'])) {
			return false;
		}
		$ID = $GLOBALS['DB']->Add('b_asd_mailtpl', $arFields);
		self::SetEvents($ID, $arEvents);
		return $ID > 0 ? $ID : false;
	}

	public static function Update($ID, $arFields) {
		if ($ID <= 0) {
			return false;
		}
		$arFieldsUpd = array();
		if (strlen(trim($arFields['NAME']))) {
			$arFieldsUpd['NAME'] = "'".$GLOBALS['DB']->ForSQL(trim($arFields['NAME']), 250)."'";
		}
		if (array_key_exists('HEADER', $arFields)) {
			$arFieldsUpd['HEADER'] = "'".$GLOBALS['DB']->ForSQL(ltrim($arFields['HEADER']))."'";
		}
		if (array_key_exists('FOOTER', $arFields)) {
			$arFieldsUpd['FOOTER'] = "'".$GLOBALS['DB']->ForSQL(rtrim($arFields['FOOTER']))."'";
		}
		if (array_key_exists('TYPE', $arFields)) {
			$arFieldsUpd['TYPE'] = "'".(trim($arFields['TYPE'])=='html'?'html':'text')."'";
		}
		if (array_key_exists('SETTINGS', $arFields)) {
			$arFields['SETTINGS'] = serialize($arFields['SETTINGS']);
			$arFieldsUpd['SETTINGS'] = "'".$GLOBALS['DB']->ForSQL($arFields['SETTINGS'])."'";
		}
		if (empty($arFieldsUpd)) {
			return false;
		}
		self::SetEvents($ID, $arFields['EVENTS']);
		return $GLOBALS['DB']->Update('b_asd_mailtpl', $arFieldsUpd, 'WHERE ID='.intval($ID));
	}

	public static function Delete($ID) {
		$ID = intval($ID);
		$GLOBALS['DB']->Query('DELETE FROM b_asd_mailtpl WHERE ID='.$ID.';', false, self::MySQLerror.__LINE__);
		self::SetEvents($ID);
	}

	public static function SetEvents($ID, $arEvent=array()) {
		if (empty($arEvent) || !is_array($arEvent)) {
			$arEvent = array();
		}
		$ID = intval($ID);
		$GLOBALS['DB']->Query('DELETE FROM b_asd_mailtpl_events WHERE TPL_ID='.$ID.';', false, self::MySQLerror.__LINE__);
		foreach ($arEvent as $event) {
			$GLOBALS['DB']->Add('b_asd_mailtpl_events', array(
				'TPL_ID' => $ID,
				'EVENT' => trim($event)
			));
		}
	}

	public static function GetEvents($ID) {
		$ID = intval($ID);
		$arEvents = array();
		$rsEvents = $GLOBALS['DB']->Query('SELECT * FROM b_asd_mailtpl_events WHERE TPL_ID='.$ID.';', false, self::MySQLerror.__LINE__);
		while ($arEvent = $rsEvents->Fetch()) {
			$arEvents[] = $arEvent['EVENT'];
		}
		return $arEvents;
	}

	public static function GetList($arSort=array('ID' => 'DESC'), $arFilter=array()) {
		list($by, $order) = each($arSort);
		$by = strtoupper($by);
		$order = strtoupper($order);
		if ($order != 'DESC') {
			$order = 'ASC';
		}
		if (!in_array($by, array('ID', 'NAME', 'TYPE'))) {
			$by = 'ID';
		}
		$strWhere = '';
		if (isset($arFilter['ID'])) {
			$strWhere .= ' AND ID="' . intval($arFilter['ID']) . '"';
		}
		if (strlen($strWhere) > 0) {
			$strWhere = 'WHERE 1=1 ' . $strWhere;
		}
		return $GLOBALS['DB']->Query('SELECT * FROM  b_asd_mailtpl '.$strWhere.' ORDER BY '.$by.' '.$order.', ID DESC;', false, self::MySQLerror.__LINE__);
	}

	public static function GetByID($ID) {
		return self::GetList(array(), array('ID' => $ID));
	}

	public static function GetByEvent($event) {
		$event = trim($event);
		if (!strlen($event)) {
			return array();
		}
		static $arEvents = array();
		if (empty($arEvents)) {
			$arEvents['-1'] = array();
			$rsEvents = $GLOBALS['DB']->Query("	SELECT
													R.EVENT, T.* FROM b_asd_mailtpl_events R
												LEFT JOIN
													b_asd_mailtpl T ON (R.TPL_ID=T.ID)
												ORDER BY T.ID ASC;", false, self::MySQLerror.__LINE__);
			while ($arEvent = $rsEvents->Fetch()) {
				$arEvents[$arEvent['EVENT']] = $arEvent;
			}
		}
		return isset($arEvents[$event]) ? $arEvents[$event] : array();
	}

	/* //less memory, but more queries
	public static function GetByEvent($event) {
		$event = trim($event);
		if (!strlen($event)) {
			return array();
		}
		return $GLOBALS['DB']->Query("	SELECT
											R.EVENT, T.* FROM b_asd_mailtpl_events R
										LEFT JOIN
											b_asd_mailtpl T ON (R.TPL_ID=T.ID)
										WHERE
											EVENT='".$GLOBALS['DB']->ForSQL($event)."'
										ORDER BY T.ID DESC
										LIMIT 1;", false, self::MySQLerror.__LINE__)->Fetch();
	}*/
}