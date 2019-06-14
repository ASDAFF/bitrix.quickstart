<?
IncludeModuleLangFile(__FILE__);

/**
 * Class %CLASS%
 */
class %CLASS%
{

	/**
	 * @param $aGlobalMenu
	 * @param $aModuleMenu
	 *
	 * @return bool
	 */
	function OnBuildGlobalMenuHandler(&$aGlobalMenu, &$aModuleMenu){
		$haveSection = false;
		$arMenu = array(
			"text" => GetMessage(self::$MLANG."MENU_NAME"),
			"url"  => "%UNDER%.php?lang=".LANGUAGE_ID,
			"icon" => "fav_menu_icon_yellow",
			"page_icon" => "fav_page_icon_yellow",
			"more_url"  => array(),
			"title" => GetMessage(self::$MLANG."MENU_TITLE")
		);

		foreach($aModuleMenu as $k => $v){
			if($v["parent_menu"] == "global_menu_services" && $v["items_id"] == "menu_%UNDER%"){
				$haveSection = true;
				$aModuleMenu[$k]["items"][] = $arMenu;
			}
		}
		if(!$haveSection){
			$customMenu = array(
				"parent_menu" => "global_menu_services", // поместим в раздел "Сервис"
				"sort"        => 1000,                    // вес пункта меню
				"text"        => GetMessage(self::$MLANG."SECTION_MENU_NAME"),       // текст пункта меню
				"title"       => GetMessage(self::$MLANG."SECTION_MENU_TITLE"), // текст всплывающей подсказки
				"icon"        => "fav_menu_icon_yellow", // малая иконка
				"page_icon"   => "fav_page_icon_yellow", // большая иконка
				"items_id"    => "menu_%UNDER%",  // идентификатор ветви
				"items"       => array($arMenu),          // остальные уровни меню сформируем ниже.
			);
			$aModuleMenu[] = $customMenu;
		}
		return true;
	}
	

	private static $table = '%UNDER%';
	private static $tableGroup = '%UNDER%_group';
	private static $MLANG = '%UNDER_CAPS%_';

	private static $LAST_ERROR = '';

	private static $arRequiredField = array('NAME', 'SITE_ID');


	/**
	 * Возвращает название таблицы
	 *
	 * @return string
	 */
	public static function GetTable()
	{
		return self::$table;
	}

	/**
	 * Возвращает название таблицы позиций ключевых слов
	 *
	 * @return string
	 */
	public static function GetTableGroup()
	{
		return self::$tableGroup;
	}

	/**
	 * Проверка на корректность заполнения полей
	 *
	 * @param      $arFields
	 * @param bool $ID
	 *
	 * @return bool
	 */
	public static function CheckFields(&$arFields, $ID = false)
	{
		$aMsg = array();

		global $DB, $APPLICATION;

		$APPLICATION->ResetException();
		foreach (self::$arRequiredField as $FIELD_ID)
		{
			if ($ID === false || array_key_exists($FIELD_ID, $arFields))
			{
				if (is_array($arFields[$FIELD_ID]))
					$val = implode('', $arFields[$FIELD_ID]);
				else
					$val = $arFields[$FIELD_ID];

				if (strlen($val) <= 0)
				{
					$aMsg[] = array("id" => $FIELD_ID, "text" => GetMessage("BAD_FIELD", array("#FIELD_NAME#" => GetMessage('FIELD_'.$FIELD_ID))));
				}
			}
		}

		$APPLICATION->ResetException();

		if (!empty($aMsg))
		{
			$e = new CAdminException(array_reverse($aMsg));
			$APPLICATION->ThrowException($e);

			return false;
		}

		return true;
	}

	/**
	 * Добавление нового файла
	 *
	 * @param $arFields
	 *
	 * @return bool|int|string
	 */
	public static function Add($arFields)
	{
		$strWarning = "";

		if (self::CheckFields($arFields) || strlen($strWarning))
		{
			self::$LAST_ERROR .= $strWarning;

			return false;
		}

		if (is_array($arFields))
		{
			global $DB;

			foreach ($arFields as $key => $value)
			{
				$arFields[$key] = "'".$DB->ForSQL($value)."'";
			}

			$ID = $DB->Insert(self::GetTable(), $arFields);

			if (intval($ID) > 0)
				return $ID;
			else
				return false;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Получение списка файлов
	 *
	 * @param array $arOrder
	 * @param array $arFilter
	 *
	 * @return bool|CDBResult
	 */
	public static function GetList($arOrder = array(), $arFilter = array(), $arGroupBy=false, $arNavStartParams=false, $bOnlyCount=false)
	{
		global $DB;

		$strFilter       = '';
		$strFilterByDate = '';
		if (isset($arFilter['DATE_CREATE_datesel']))
		{
			$strFilterByDate = ' DATE_CREATE BETWEEN ';
			switch ($arFilter['DATE_CREATE_datesel'])
			{
				case "today" :
					$strFilterByDate .= "'".date("Y-m-d")." 00:00:00' AND '".date("Y-m-d")." 23:59:59'";
					break;
				case "yesterday" :
					$strFilterByDate .= "'".date("Y-m-d", strtotime("-1 day"))." 00:00:00' AND '".date("Y-m-d", strtotime("-1 day"))." 23:59:59'";
					break;
				case "week" :
					$current_day = date("N");
					$strFilterByDate .= "'".date("Y-m-d", strtotime("-".($current_day - 1)." days"))." 00:00:00' AND '".date("Y-m-d", strtotime("+".(7 - $current_day)." days"))." 23:59:59'";
					break;
				case "week_ago" :
					$current_day = date("N");
					$start_day   = strtotime("-".(7 + $current_day - 1)." days");
					$end_day     = strtotime("-".($current_day)." days");
					$strFilterByDate .= "'".date("Y-m-d", $start_day)." 00:00:00' AND '".date("Y-m-d", $end_day)." 23:59:59'";
					break;
				case "month" :
					$strFilterByDate .= "'".date("Y-m-d", mktime(0, 0, 0, date("n"), 1, date("Y")))." 00:00:00' AND '".date("Y-m-d", mktime(23, 59, 59, date("n"), date("t"), date("Y")))." 23:59:59'";
					break;
				case "month_ago" :
					$month_ago           = strtotime("-1 month");
					$first_day_month_ago = date("Y", $month_ago)."-".date("m", $month_ago)."-01";
					$last_day_month_ago  = date("Y", $month_ago)."-".date("m", $month_ago)."-".date("t", $month_ago);
					$strFilterByDate .= "'".$first_day_month_ago." 00:00:00' AND '".$last_day_month_ago." 23:59:59'";
					break;
				case "days" :
					$strFilterByDate .= "'".date("Y-m-d", strtotime("-".$arFilter['DATE_CREATE_days']." days"))." 00:00:00' AND '".date("Y-m-d")." 23:59:59'";
					break;
				case "exact" :
					$day = explode(".", $arFilter['DATE_CREATE_from']);
					$strFilterByDate .= "'".date("Y-m-d", mktime(0, 0, 0, $day[1], $day[0], $day[2]))." 00:00:00' AND '".date("Y-m-d", mktime(0, 0, 0, $day[1], $day[0], $day[2]))." 23:59:59'";
					break;
				case "after" :
					$day = explode(".", $arFilter['DATE_CREATE_from']);
					$strFilterByDate .= "'".date("Y-m-d", mktime(0, 0, 0, $day[1], $day[0], $day[2]))." 00:00:00' AND '".date("Y-m-d")." 23:59:59'";
					break;
				case "before" :
					$day = explode(".", $arFilter['DATE_CREATE_to']);
					$strFilterByDate .= "'".date("Y-m-d", 0)." 00:00:00' AND '".date("Y-m-d", mktime(0, 0, 0, $day[1], $day[0], $day[2]))." 23:59:59'";
					break;
				case "interval" :
					$day  = explode(".", $arFilter['DATE_CREATE_from']);
					$day2 = explode(".", $arFilter['DATE_CREATE_to']);
					$strFilterByDate .= "'".date("Y-m-d", mktime(0, 0, 0, $day[1], $day[0], $day[2]))." 00:00:00' AND '".date("Y-m-d", mktime(0, 0, 0, $day2[1], $day2[0], $day2[2]))." 23:59:59'";
					break;
				default:
					break;
			}
			unset($arFilter['DATE_CREATE_datesel']);
			unset($arFilter['DATE_CREATE_days']);
			unset($arFilter['DATE_CREATE_from']);
			unset($arFilter['DATE_CREATE_to']);
		}
		$needAND = false;
		if ($strFilterByDate != '')
		{
			$needAND = true;
		}
		$needWHERE = true;
		foreach ($arFilter as $fName => $fValue)
		{
			if ($strFilter == "")
			{
				$strFilter = " WHERE ";
				$needWHERE = false;
			}
			else
			{
				$strFilter .= " AND ";
				$needAND = true;
			}

			if ($fName == 'NAME' || $fName == 'SHORT_NAME'
			)
			{
				$strFilter .= $fName." LIKE '".$fValue."'";
			}
			else
			{
				$strFilter .= $fName." = '".$fValue."'";
			}
		}

		$strOrder = "";
		foreach ($arOrder as $Okey => $Ovalue)
		{
			if (strlen($Ovalue))
			{
				if ($strOrder == "")
				{
					$strOrder = " ORDER BY ";
				}
				else
				{
					$strOrder .= ",";
				}
				$strOrder .= $Okey." ".$Ovalue;
			}
		}
		if ($needWHERE && $strFilterByDate != "")
		{
			$strFilterByDate = " WHERE ".$strFilterByDate;
		}
		if ($needAND)
		{
			$strFilterByDate = " AND ".$strFilterByDate;
		}

		if($bOnlyCount)
		{
			$res = $DB->Query("SELECT *, COUNT(id) AS CNT FROM ".self::GetTable()." ".$strFilter.$strFilterByDate.$strOrder, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			$res = $res->Fetch();
			return $res['CNT'];
		}

		if(is_array($arNavStartParams))
		{
			$nTopCount = intval($arNavStartParams["nTopCount"]);

			if($nTopCount > 0)
			{
				$strSql = "SELECT * FROM ".self::GetTable()." ".$strFilter.$strFilterByDate.$strOrder." LIMIT ".$nTopCount;
				$res = $DB->Query($strSql, false, " FILE: ".__FILE__."<br> LINE: ".__LINE__);
			}else{
				$strSql = "SELECT * FROM ".self::GetTable()." ".$strFilter.$strFilterByDate.$strOrder;
				$res = $DB->Query($strSql, false, " FILE: ".__FILE__."<br> LINE: ".__LINE__);
			}
		}
		else
		{
			$strSql = "SELECT * FROM ".self::GetTable()." ".$strFilter.$strFilterByDate.$strOrder;
			$res = $DB->Query($strSql, false, " FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}

		return $res;
	}

	/**
	 * Получение файла по его идентификатору
	 *
	 * @param $ID
	 *
	 * @return bool|CDBResult
	 */
	public static function GetByID($ID)
	{
		return self::GetList(array(), array("ID" => $ID));
	}

	/**
	 * Получение файлов по идентификатору элемента
	 *
	 * @param $ID
	 * @param $SORT
	 *
	 * @return bool|CDBResult
	 */
	public static function GetByElementID($ID, $SORT = array('SORT' => 'DESC'))
	{
		return self::GetList($SORT, array("ELEMENT_ID" => $ID));
	}

	/**
	 * Получение файлов по идентификатору элемента
	 *
	 * @param $ID
	 * @param $SORT
	 *
	 * @return bool|CDBResult
	 */
	public static function GetFirstByElementID($ID, $SORT = array('SORT' => 'DESC'))
	{
		return self::GetList($SORT, array("ELEMENT_ID" => $ID), false, Array ("nTopCount" => 1));
	}

	public static function GetCountByElementID($ID, $SORT = array('SORT' => 'DESC'))
	{
		return self::GetList($SORT, array("ELEMENT_ID" => $ID), false, false, true);
	}

	

	/**
	 * Удаление файла
	 *
	 * @param $ID
	 *
	 * @return bool
	 */
	public static function Delete($ID)
	{
		if (intval($ID) > 0)
		{
			global $DB;
			$DB->Query("DELETE FROM ".self::GetTable()." WHERE ID = '".$DB->ForSQL($ID)."'", false, " FILE: ".__FILE__."<br> LINE: ".__LINE__);

			return true;
		}

		return false;
	}

	/**
	 * @param       $ID
	 * @param array $arFields
	 *
	 * @return bool|int
	 */
	public static function Update($ID, $arFields = array())
	{
		$err_mess = "";
		global $DB;

		foreach ($arFields as $key => $value)
		{
			$arFields[$key] = "'".$DB->ForSQL($value)."'";
		}

		if (intval($ID) > 0)
		{
			$ID = $DB->Update(self::GetTable(), $arFields, "WHERE ID = '".$DB->ForSQL($ID)."'", $err_mess.__LINE__);

			return $ID;
		}

		return false;
	}

	/**
	 * @param $arFields
	 * @param $ID
	 *
	 * @return bool|int|string
	 */
	public static function Set($arFields, $ID)
	{
		$err_mess = "";
		global $DB;

		$arrKeys = array_keys($arFields);

		if (self::CheckFields($arFields, $ID))
		{
			$arFields_i = array();
			$ID         = intval($ID);

			if (in_array("ACTIVE", $arrKeys) && ($arFields["ACTIVE"] == "Y" || $arFields["ACTIVE"] == "N"))
				$arFields_i["ACTIVE"] = "'".$DB->ForSQL($arFields["ACTIVE"])."'";


			if ($ID > 0)
			{
				$DB->Update(self::GetTable(), $arFields_i, "WHERE ID='".$ID."'", $err_mess.__LINE__);
			}
			else
			{
				$ID = $DB->Insert(self::GetTable(), $arFields_i, $err_mess.__LINE__);
			}


			return $ID;
		}
		else
			return false;
	}


}
