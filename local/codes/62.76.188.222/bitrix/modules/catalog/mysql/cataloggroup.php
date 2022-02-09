<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/general/cataloggroup.php");

class CCatalogGroup extends CAllCatalogGroup
{
	function GetByID($ID, $lang = LANGUAGE_ID)
	{
		$ID = intval($ID);
		if (0 >= $ID)
			return false;

		global $DB, $USER;
		if (!$USER->IsAdmin())
		{
			$strSql =
				"SELECT CG.ID, CG.NAME, CG.BASE, CG.SORT, CG.XML_ID, IF(CGG.ID IS NULL, 'N', 'Y') as CAN_ACCESS, CGL.NAME as NAME_LANG, IF(CGG1.ID IS NULL, 'N', 'Y') as CAN_BUY ".
				"FROM b_catalog_group CG ".
				"	LEFT JOIN b_catalog_group2group CGG ON (CG.ID = CGG.CATALOG_GROUP_ID AND CGG.GROUP_ID IN (".$USER->GetGroups().") AND CGG.BUY <> 'Y') ".
				"	LEFT JOIN b_catalog_group2group CGG1 ON (CG.ID = CGG1.CATALOG_GROUP_ID AND CGG1.GROUP_ID IN (".$USER->GetGroups().") AND CGG1.BUY = 'Y') ".
				"	LEFT JOIN b_catalog_group_lang CGL ON (CG.ID = CGL.CATALOG_GROUP_ID AND CGL.LID = '".$DB->ForSql($lang)."') ".
				"WHERE CG.ID = ".$ID." GROUP BY CG.ID, CG.NAME, CG.BASE, CG.XML_ID, CGL.NAME";
		}
		else
		{
			$strSql =
				"SELECT CG.ID, CG.NAME, CG.BASE, CG.SORT, CG.XML_ID, 'Y' as CAN_ACCESS, CGL.NAME as NAME_LANG, 'Y' as CAN_BUY ".
				"FROM b_catalog_group CG ".
				"	LEFT JOIN b_catalog_group_lang CGL ON (CG.ID = CGL.CATALOG_GROUP_ID AND CGL.LID = '".$DB->ForSql($lang)."') ".
				"WHERE CG.ID = ".$ID;
		}
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if ($res = $db_res->Fetch())
			return $res;
		return false;
	}

	function Add($arFields)
	{
		global $DB;
		global $CACHE_MANAGER;
		global $stackCacheManager;
		global $CATALOG_BASE_GROUP;

		$groupID = 0;

		if (!CCatalogGroup::CheckFields("ADD", $arFields, 0))
			return false;

		$db_events = GetModuleEvents("catalog", "OnBeforeGroupAdd");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array(&$arFields))===false)
				return false;

		if ($arFields["BASE"] == "Y")
		{
			$strSql = "UPDATE b_catalog_group SET BASE = 'N' WHERE BASE = 'Y'";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if (isset($CATALOG_BASE_GROUP))
				unset($CATALOG_BASE_GROUP);
		}

		$arInsert = $DB->PrepareInsert("b_catalog_group", $arFields);

		$strSql = "INSERT INTO b_catalog_group(".$arInsert[0].") VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$groupID = intval($DB->LastID());

		foreach ($arFields["USER_GROUP"] as &$intValue)
		{
			$strSql = "INSERT INTO b_catalog_group2group(CATALOG_GROUP_ID, GROUP_ID, BUY) VALUES(".$groupID.", ".$intValue.", 'N')";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		if (isset($intValue))
			unset($intValue);

		foreach ($arFields["USER_GROUP_BUY"] as &$intValue)
		{
			$strSql = "INSERT INTO b_catalog_group2group(CATALOG_GROUP_ID, GROUP_ID, BUY) VALUES(".$groupID.", ".$intValue.", 'Y')";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		if (isset($intValue))
			unset($intValue);

		if (isset($arFields["USER_LANG"]) && is_array($arFields["USER_LANG"]) && !empty($arFields["USER_LANG"]))
		{
			foreach ($arFields["USER_LANG"] as $key => $value)
			{
				$strSql =
					"INSERT INTO b_catalog_group_lang(CATALOG_GROUP_ID, LID, NAME) ".
					"VALUES(".$groupID.", '".$DB->ForSql($key)."', '".$DB->ForSql($value)."')";
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
		}

		if (!defined("CATALOG_SKIP_CACHE") || !CATALOG_SKIP_CACHE)
		{
			$CACHE_MANAGER->CleanDir("catalog_group");
			$CACHE_MANAGER->Clean("catalog_group_perms");
		}

		$stackCacheManager->Clear("catalog_GetQueryBuildArrays");
		$stackCacheManager->Clear("catalog_discount");

		$events = GetModuleEvents("catalog", "OnGroupUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($groupID, $arFields));

		return $groupID;
	}

	function Update($ID, $arFields)
	{
		global $DB;
		global $CACHE_MANAGER;
		global $stackCacheManager;
		global $CATALOG_BASE_GROUP;

		$ID = intval($ID);
		if (0 >= $ID)
			return false;

		if (!CCatalogGroup::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$db_events = GetModuleEvents("catalog", "OnBeforeGroupUpdate");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID, &$arFields))===false)
				return false;

		if (isset($arFields["BASE"]) && $arFields["BASE"] == "Y")
		{
			$strSql = "UPDATE b_catalog_group SET BASE = 'N' WHERE ID != ".$ID." AND BASE = 'Y'";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if (isset($CATALOG_BASE_GROUP))
				unset($CATALOG_BASE_GROUP);
		}

		$strUpdate = $DB->PrepareUpdate("b_catalog_group", $arFields);
		$strSql = "UPDATE b_catalog_group SET ".$strUpdate." WHERE ID = ".$ID;
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if (isset($arFields["USER_GROUP"]) && is_array($arFields["USER_GROUP"]) && !empty($arFields["USER_GROUP"]))
		{
			$DB->Query("DELETE FROM b_catalog_group2group WHERE CATALOG_GROUP_ID = ".$ID." AND BUY <> 'Y'");
			foreach ($arFields["USER_GROUP"] as &$intValue)
			{
				$strSql = "INSERT INTO b_catalog_group2group(CATALOG_GROUP_ID, GROUP_ID, BUY) VALUES(".$ID.", ".$intValue.", 'N')";
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
			if (isset($intValue))
				unset($intValue);
		}

		if (isset($arFields["USER_GROUP_BUY"]) && is_array($arFields["USER_GROUP_BUY"]) && !empty($arFields["USER_GROUP_BUY"]))
		{
			$DB->Query("DELETE FROM b_catalog_group2group WHERE CATALOG_GROUP_ID = ".$ID." AND BUY = 'Y'");
			foreach ($arFields["USER_GROUP_BUY"] as &$intValue)
			{
				$strSql = "INSERT INTO b_catalog_group2group(CATALOG_GROUP_ID, GROUP_ID, BUY) VALUES(".$ID.", ".$intValue.", 'Y')";
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
			if (isset($intValue))
				unset($intValue);
		}

		if (isset($arFields["USER_LANG"]) && is_array($arFields["USER_LANG"]) && !empty($arFields["USER_LANG"]))
		{
			$DB->Query("DELETE FROM b_catalog_group_lang WHERE CATALOG_GROUP_ID = ".$ID);
			foreach ($arFields["USER_LANG"] as $key => $value)
			{
				$strSql =
					"INSERT INTO b_catalog_group_lang(CATALOG_GROUP_ID, LID, NAME) ".
					"VALUES(".$ID.", '".$DB->ForSql($key)."', '".$DB->ForSql($value)."')";
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
		}

		if (!defined("CATALOG_SKIP_CACHE") || !CATALOG_SKIP_CACHE)
		{
			$CACHE_MANAGER->CleanDir("catalog_group");
			$CACHE_MANAGER->Clean("catalog_group_perms");
		}

		$stackCacheManager->Clear("catalog_GetQueryBuildArrays");
		$stackCacheManager->Clear("catalog_discount");

		$events = GetModuleEvents("catalog", "OnGroupUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		return true;
	}

	function Delete($ID)
	{
		global $DB;
		global $CACHE_MANAGER;
		global $stackCacheManager;
		global $APPLICATION;

		$ID = intval($ID);
		if (0 >= $ID)
			return false;

		if ($res = CCatalogGroup::GetByID($ID))
		{
			if ($res["BASE"] != "Y")
			{
				$db_events = GetModuleEvents("catalog", "OnBeforeGroupDelete");
				while ($arEvent = $db_events->Fetch())
					if (ExecuteModuleEventEx($arEvent, array($ID))===false)
						return false;

				$events = GetModuleEvents("catalog", "OnGroupDelete");
				while ($arEvent = $events->Fetch())
					ExecuteModuleEventEx($arEvent, array($ID));

				if (!defined("CATALOG_SKIP_CACHE") || !CATALOG_SKIP_CACHE)
				{
					$CACHE_MANAGER->CleanDir("catalog_group");
					$CACHE_MANAGER->Clean("catalog_group_perms");
				}

				$stackCacheManager->Clear("catalog_GetQueryBuildArrays");
				$stackCacheManager->Clear("catalog_discount");

				$DB->Query("DELETE FROM b_catalog_price WHERE CATALOG_GROUP_ID = ".$ID);
				$DB->Query("DELETE FROM b_catalog_group2group WHERE CATALOG_GROUP_ID = ".$ID);
				$DB->Query("DELETE FROM b_catalog_group_lang WHERE CATALOG_GROUP_ID = ".$ID);
				return $DB->Query("DELETE FROM b_catalog_group WHERE ID = ".$ID, true);
			}
			else
			{
				$APPLICATION->ThrowException(GetMessage('BT_MOD_CAT_GROUP_ERR_CANNOT_DELETE_BASE_TYPE'), 'BASE');
			}
		}

		return false;
	}

	function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB, $USER;

		// for old-style execution
		if (!is_array($arOrder) && !is_array($arFilter))
		{
			$arOrder = strval($arOrder);
			$arFilter = strval($arFilter);
			if (strlen($arOrder) > 0 && strlen($arFilter) > 0)
				$arOrder = array($arOrder => $arFilter);
			else
				$arOrder = array();
			if (is_array($arGroupBy))
				$arFilter = $arGroupBy;
			else
				$arFilter = array();
			$arGroupBy = false;
			if ($arNavStartParams != false && strlen($arNavStartParams) > 0)
				$arFilter["LID"] = $arNavStartParams;
			else
				$arFilter["LID"] = LANGUAGE_ID;
		}
		if (!isset($arFilter['LID']))
			$arFilter['LID'] = LANGUAGE_ID;

		if (empty($arSelectFields))
			$arSelectFields = array("ID", "NAME", "BASE", "SORT", "NAME_LANG", "CAN_ACCESS", "CAN_BUY", "XML_ID");
		if ($arGroupBy == false)
			$arGroupBy = array("ID", "NAME", "BASE", "SORT", "XML_ID", "NAME_LANG");

		$arFields = array(
			"ID" => array("FIELD" => "CG.ID", "TYPE" => "int"),
			"NAME" => array("FIELD" => "CG.NAME", "TYPE" => "string"),
			"BASE" => array("FIELD" => "CG.BASE", "TYPE" => "char"),
			"SORT" => array("FIELD" => "CG.SORT", "TYPE" => "int"),
			"XML_ID" => array("FIELD" => "CG.XML_ID", "TYPE" => "string"),
			"NAME_LANG" => array("FIELD" => "CGL.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_catalog_group_lang CGL ON (CG.ID = CGL.CATALOG_GROUP_ID AND CGL.LID = '".$DB->ForSql($arFilter["LID"], 2)."')"),
		);
		if (!$USER->IsAdmin())
		{
			$arFields["CAN_ACCESS"] = array(
					"FIELD" => "IF(CGG.ID IS NULL, 'N', 'Y')",
					"TYPE" => "char",
					"FROM" => "LEFT JOIN b_catalog_group2group CGG ON (CG.ID = CGG.CATALOG_GROUP_ID AND CGG.GROUP_ID IN (".$USER->GetGroups().") AND CGG.BUY <> 'Y')",
					"GROUPED" => "N"
				);
			$arFields["CAN_BUY"] = array(
					"FIELD" => "IF(CGG1.ID IS NULL, 'N', 'Y')",
					"TYPE" => "char",
					"FROM" => "LEFT JOIN b_catalog_group2group CGG1 ON (CG.ID = CGG1.CATALOG_GROUP_ID AND CGG1.GROUP_ID IN (".$USER->GetGroups().") AND CGG1.BUY = 'Y')",
					"GROUPED" => "N"
				);
		}
		else
		{
			$arFields["CAN_ACCESS"] = array("FIELD" => "'Y'", "TYPE" => "char");
			$arFields["CAN_BUY"] = array("FIELD" => "'Y'", "TYPE" => "char");
		}

		$arSqls = CCatalog::_PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && empty($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_catalog_group CG ".$arSqls["FROM"]." ";
			if (!empty($arSqls["WHERE"]))
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (!empty($arSqls["GROUPBY"]))
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
			if (!empty($arSqls["HAVING"]))
				$strSql .= "HAVING ".$arSqls["HAVING"]." ";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return false;
		}

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_catalog_group CG ".$arSqls["FROM"]." ";
		if (!empty($arSqls["WHERE"]))
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (!empty($arSqls["GROUPBY"]))
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (!empty($arSqls["HAVING"]))
			$strSql .= "HAVING ".$arSqls["HAVING"]." ";
		if (!empty($arSqls["ORDERBY"]))
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && intval($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_catalog_group CG ".$arSqls["FROM"]." ";
			if (!empty($arSqls["WHERE"]))
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (!empty($arSqls["GROUPBY"]))
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";
			if (!empty($arSqls["HAVING"]))
				$strSql_tmp .= "HAVING ".$arSqls["HAVING"]." ";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (empty($arSqls["GROUPBY"]))
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && intval($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".intval($arNavStartParams["nTopCount"]);

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}

	function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB, $USER;

		if (empty($arSelectFields))
			$arSelectFields = array("ID", "NAME", "BASE", "SORT", "NAME_LANG", "XML_ID");

		$arFields = array(
			"ID" => array("FIELD" => "CG.ID", "TYPE" => "int"),
			"NAME" => array("FIELD" => "CG.NAME", "TYPE" => "string"),
			"BASE" => array("FIELD" => "CG.BASE", "TYPE" => "char"),
			"SORT" => array("FIELD" => "CG.SORT", "TYPE" => "int"),
			"XML_ID" => array("FIELD" => "CG.XML_ID", "TYPE" => "string"),

			"GROUP_ID" => array("FIELD" => "CG2G.ID", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_group2group CG2G ON (CG.ID = CG2G.CATALOG_GROUP_ID)"),
			"GROUP_CATALOG_GROUP_ID" => array("FIELD" => "CG2G.CATALOG_GROUP_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_group2group CG2G ON (CG.ID = CG2G.CATALOG_GROUP_ID)"),
			"GROUP_GROUP_ID" => array("FIELD" => "CG2G.GROUP_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_group2group CG2G ON (CG.ID = CG2G.CATALOG_GROUP_ID)"),
			"GROUP_BUY" => array("FIELD" => "CG2G.BUY", "TYPE" => "char", "FROM" => "INNER JOIN b_catalog_group2group CG2G ON (CG.ID = CG2G.CATALOG_GROUP_ID)"),

			"NAME_LANG" => array("FIELD" => "CGL.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_catalog_group_lang CGL ON (CG.ID = CGL.CATALOG_GROUP_ID AND CGL.LID = '".$DB->ForSql(LANGUAGE_ID, 2)."')"),
		);

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && empty($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_catalog_group CG ".$arSqls["FROM"]." ";
			if (!empty($arSqls["WHERE"]))
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (!empty($arSqls["GROUPBY"]))
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
			if (!empty($arSqls["HAVING"]))
				$strSql .= "HAVING ".$arSqls["HAVING"]." ";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return false;
		}

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_catalog_group CG ".$arSqls["FROM"]." ";
		if (!empty($arSqls["WHERE"]))
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (!empty($arSqls["GROUPBY"]))
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (!empty($arSqls["HAVING"]))
			$strSql .= "HAVING ".$arSqls["HAVING"]." ";
		if (!empty($arSqls["ORDERBY"]))
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && intval($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_catalog_group CG ".$arSqls["FROM"]." ";
			if (!empty($arSqls["WHERE"]))
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (!empty($arSqls["GROUPBY"]))
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";
			if (!empty($arSqls["HAVING"]))
				$strSql_tmp .= "HAVING ".$arSqls["HAVING"]." ";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (empty($arSqls["GROUPBY"]))
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && intval($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".intval($arNavStartParams["nTopCount"]);

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}

	function GetGroupsList($arFilter = array())
	{
		global $DB;

		$arFields = array(
			"ID" => array("FIELD" => "CGG.ID", "TYPE" => "int"),
			"CATALOG_GROUP_ID" => array("FIELD" => "CGG.CATALOG_GROUP_ID", "TYPE" => "int"),
			"GROUP_ID" => array("FIELD" => "CGG.GROUP_ID", "TYPE" => "int"),
			"BUY" => array("FIELD" => "CGG.BUY", "TYPE" => "char")
		);

		$arSqls = CCatalog::PrepareSql($arFields, array(), $arFilter, false, false);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_catalog_group2group CGG ".$arSqls["FROM"]." ";
		if (!empty($arSqls["WHERE"]))
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (!empty($arSqls["GROUPBY"]))
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (!empty($arSqls["ORDERBY"]))
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $dbRes;
	}

	function GetLangList($arFilter = array())
	{
		global $DB;

		$arFields = array(
			"ID" => array("FIELD" => "CGL.ID", "TYPE" => "int"),
			"CATALOG_GROUP_ID" => array("FIELD" => "CGL.CATALOG_GROUP_ID", "TYPE" => "int"),
			"LID" => array("FIELD" => "CGL.LID", "TYPE" => "string"),
			"NAME" => array("FIELD" => "CGL.NAME", "TYPE" => "string")
		);

		$arSqls = CCatalog::PrepareSql($arFields, array(), $arFilter, false, false);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_catalog_group_lang CGL ".$arSqls["FROM"]." ";
		if (!empty($arSqls["WHERE"]))
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (!empty($arSqls["GROUPBY"]))
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (!empty($arSqls["ORDERBY"]))
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $dbRes;
	}
}
?>