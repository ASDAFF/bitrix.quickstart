<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/general/cataloggroup.php");

class CCatalogGroup extends CAllCatalogGroup
{
	function GetByID($ID, $lang = LANGUAGE_ID)
	{
		global $DB, $USER;
		if (!$USER->IsAdmin())
		{
			$strSql =
				"SELECT CG.ID, CG.NAME, CG.BASE, CG.SORT, IF(CGG.ID IS NULL, 'N', 'Y') as CAN_ACCESS, CGL.NAME as NAME_LANG, IF(CGG1.ID IS NULL, 'N', 'Y') as CAN_BUY ".
				"FROM b_catalog_group CG ".
				"	LEFT JOIN b_catalog_group2group CGG ON (CG.ID = CGG.CATALOG_GROUP_ID AND CGG.GROUP_ID IN (".$USER->GetGroups().") AND CGG.BUY <> 'Y') ".
				"	LEFT JOIN b_catalog_group2group CGG1 ON (CG.ID = CGG1.CATALOG_GROUP_ID AND CGG1.GROUP_ID IN (".$USER->GetGroups().") AND CGG1.BUY = 'Y') ".
				"	LEFT JOIN b_catalog_group_lang CGL ON (CG.ID = CGL.CATALOG_GROUP_ID AND CGL.LID = '".$DB->ForSql($lang)."') ".
				"WHERE CG.ID = ".IntVal($ID)." ".
				"GROUP BY CG.ID, CG.NAME, CG.BASE ";
		}
		else
		{
			$strSql =
				"SELECT CG.ID, CG.NAME, CG.BASE, CG.SORT, 'Y' as CAN_ACCESS, CGL.NAME as NAME_LANG, 'Y' as CAN_BUY ".
				"FROM b_catalog_group CG ".
				"	LEFT JOIN b_catalog_group_lang CGL ON (CG.ID = CGL.CATALOG_GROUP_ID AND CGL.LID = '".$DB->ForSql($lang)."') ".
				"WHERE CG.ID = ".IntVal($ID)." ";
		}
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if ($res = $db_res->Fetch())
			return $res;
		return false;
	}


	function Add($arFields)
	{
		global $DB;

		$groupID = 0;

		if (!CCatalogGroup::CheckFields("ADD", $arFields, 0))
			return False;

		$db_events = GetModuleEvents("catalog", "OnBeforeGroupAdd");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array(&$arFields))===false)
				return false;

		if ($arFields["BASE"] == "Y")
		{
			$dbBaseGroup = CCatalogGroup::GetList(
					array(),
					array("BASE" => "Y")
				);
			while ($arBaseGroup = $dbBaseGroup->Fetch())
				CCatalogGroup::Update($arBaseGroup["ID"], array("BASE" => "N"));
		}

		$arInsert = $DB->PrepareInsert("b_catalog_group", $arFields);

		$strSql =
			"INSERT INTO b_catalog_group(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$groupID = IntVal($DB->LastID());

		if (isset($arFields["USER_GROUP"]) && is_array($arFields["USER_GROUP"]))
		{
			for ($i = 0; $i < count($arFields["USER_GROUP"]); $i++)
			{
				if (IntVal($arFields["USER_GROUP"][$i]) > 0)
				{
					$strSql =
						"INSERT INTO b_catalog_group2group(CATALOG_GROUP_ID, GROUP_ID, BUY) ".
						"VALUES(".$groupID.", ".IntVal($arFields["USER_GROUP"][$i]).", 'N')";
					$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				}
			}
		}

		if (isset($arFields["USER_GROUP_BUY"]) && is_array($arFields["USER_GROUP_BUY"]))
		{
			for ($i = 0; $i < count($arFields["USER_GROUP_BUY"]); $i++)
			{
				if (IntVal($arFields["USER_GROUP_BUY"][$i])>0)
				{
					$strSql =
						"INSERT INTO b_catalog_group2group(CATALOG_GROUP_ID, GROUP_ID, BUY) ".
						"VALUES(".$groupID.", ".IntVal($arFields["USER_GROUP_BUY"][$i]).", 'Y')";
					$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				}
			}
		}

		if (isset($arFields["USER_LANG"]) && is_array($arFields["USER_LANG"]))
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
			$GLOBALS["CACHE_MANAGER"]->CleanDir("catalog_group");
			$GLOBALS["CACHE_MANAGER"]->Clean("catalog_group_perms");
		}

		$GLOBALS["stackCacheManager"]->Clear("catalog_GetQueryBuildArrays");
		$GLOBALS["stackCacheManager"]->Clear("catalog_discount");

		$events = GetModuleEvents("catalog", "OnGroupUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($groupID, $arFields));

		return $groupID;
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

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "NAME", "BASE", "SORT", "NAME_LANG", "CAN_ACCESS", "CAN_BUY");
		if ($arGroupBy == false)
			$arGroupBy = array("ID", "NAME", "BASE", "SORT", "NAME_LANG");

		// FIELDS -->
		$arFields = array(
			"ID" => array("FIELD" => "CG.ID", "TYPE" => "int"),
			"NAME" => array("FIELD" => "CG.NAME", "TYPE" => "string"),
			"BASE" => array("FIELD" => "CG.BASE", "TYPE" => "char"),
			"SORT" => array("FIELD" => "CG.SORT", "TYPE" => "int"),

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
		// <-- FIELDS

		$arSqls = CCatalog::_PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_catalog_group CG ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
			if (strlen($arSqls["HAVING"]) > 0)
				$strSql .= "HAVING ".$arSqls["HAVING"]." ";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_catalog_group CG ".
			"	".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["HAVING"]) > 0)
			$strSql .= "HAVING ".$arSqls["HAVING"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_catalog_group CG ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";
			if (strlen($arSqls["HAVING"]) > 0)
				$strSql_tmp .= "HAVING ".$arSqls["HAVING"]." ";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (strlen($arSqls["GROUPBY"]) <= 0)
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
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".intval($arNavStartParams["nTopCount"]);

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}

	function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB, $USER;

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "NAME", "BASE", "SORT", "NAME_LANG");

		// FIELDS -->
		$arFields = array(
			"ID" => array("FIELD" => "CG.ID", "TYPE" => "int"),
			"NAME" => array("FIELD" => "CG.NAME", "TYPE" => "string"),
			"BASE" => array("FIELD" => "CG.BASE", "TYPE" => "char"),
			"SORT" => array("FIELD" => "CG.SORT", "TYPE" => "int"),

			"GROUP_ID" => array("FIELD" => "CG2G.ID", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_group2group CG2G ON (CG.ID = CG2G.CATALOG_GROUP_ID)"),
			"GROUP_CATALOG_GROUP_ID" => array("FIELD" => "CG2G.CATALOG_GROUP_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_group2group CG2G ON (CG.ID = CG2G.CATALOG_GROUP_ID)"),
			"GROUP_GROUP_ID" => array("FIELD" => "CG2G.GROUP_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_group2group CG2G ON (CG.ID = CG2G.CATALOG_GROUP_ID)"),
			"GROUP_BUY" => array("FIELD" => "CG2G.BUY", "TYPE" => "char", "FROM" => "INNER JOIN b_catalog_group2group CG2G ON (CG.ID = CG2G.CATALOG_GROUP_ID)"),

			"NAME_LANG" => array("FIELD" => "CGL.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_catalog_group_lang CGL ON (CG.ID = CGL.CATALOG_GROUP_ID AND CGL.LID = '".$DB->ForSql(LANGUAGE_ID, 2)."')"),
		);
		// <-- FIELDS

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_catalog_group CG ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
			if (strlen($arSqls["HAVING"]) > 0)
				$strSql .= "HAVING ".$arSqls["HAVING"]." ";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_catalog_group CG ".
			"	".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["HAVING"]) > 0)
			$strSql .= "HAVING ".$arSqls["HAVING"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_catalog_group CG ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";
			if (strlen($arSqls["HAVING"]) > 0)
				$strSql_tmp .= "HAVING ".$arSqls["HAVING"]." ";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (strlen($arSqls["GROUPBY"]) <= 0)
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
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".intval($arNavStartParams["nTopCount"]);

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}
}
?>