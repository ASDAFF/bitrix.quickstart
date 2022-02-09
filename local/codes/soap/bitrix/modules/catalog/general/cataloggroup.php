<?
$GLOBALS["CATALOG_BASE_GROUP"] = array();

class CAllCatalogGroup
{
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		if ((is_set($arFields, "BASE") || $ACTION=="ADD") && $arFields["BASE"] != "Y")
			$arFields["BASE"] = "N";

		if (is_set($arFields, "SORT") || $ACTION=="ADD")
		{
			$arFields["SORT"] = IntVal($arFields["SORT"]);
			if ($arFields["SORT"] < 0 || $arFields["SORT"] > 1000)
				$arFields["SORT"] = 100;
		}

		return True;
	}

	function GetGroupsList($arFilter = Array())
	{
		global $DB;

		// FIELDS -->
		$arFields = array(
			"ID" => array("FIELD" => "CGG.ID", "TYPE" => "int"),
			"CATALOG_GROUP_ID" => array("FIELD" => "CGG.CATALOG_GROUP_ID", "TYPE" => "int"),
			"GROUP_ID" => array("FIELD" => "CGG.GROUP_ID", "TYPE" => "int"),
			"BUY" => array("FIELD" => "CGG.BUY", "TYPE" => "char")
		);
		// <-- FIELDS

		$arSqls = CCatalog::PrepareSql($arFields, array(), $arFilter, false, false);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_catalog_group2group CGG ".
			"	".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $dbRes;
	}

	function GetGroupsPerms($arUserGroups = array(), $arCatalogGroupsFilter = array())
	{
		global $USER;

		if (!is_array($arUserGroups))
			$arUserGroups = array($arUserGroups);

		if (count($arUserGroups) <= 0)
			$arUserGroups = $USER->GetUserGroupArray();

		$arUserGroupsFilter = array();
		for ($i = 0, $cnt = count($arUserGroups); $i < $cnt; $i++)
		{
			$arUserGroups[$i] = IntVal($arUserGroups[$i]);
			if ($arUserGroups[$i] > 0)
				$arUserGroupsFilter[] = $arUserGroups[$i];
		}

		$arResult = array();
		$arResult["view"] = array();
		$arResult["buy"] = array();

		if (count($arUserGroupsFilter) <= 0)
			return $arResult;

		$arData = array();

		if (defined("CATALOG_SKIP_CACHE") && CATALOG_SKIP_CACHE)
		{
			$dbPriceGroups = CCatalogGroup::GetGroupsList(array("GROUP_ID" => $arUserGroupsFilter));
			while ($arPriceGroup = $dbPriceGroups->Fetch())
			{
				$arPriceGroup["CATALOG_GROUP_ID"] = IntVal($arPriceGroup["CATALOG_GROUP_ID"]);

				$key = (($arPriceGroup["BUY"] == "Y") ? "buy" : "view");
				if ($key == "view")
					if (count($arCatalogGroupsFilter) > 0)
						if (!in_array($arPriceGroup["CATALOG_GROUP_ID"], $arCatalogGroupsFilter))
							continue;

				if (!in_array($arPriceGroup["CATALOG_GROUP_ID"], $arResult[$key]))
					$arResult[$key][] = $arPriceGroup["CATALOG_GROUP_ID"];
			}

			return $arResult;
		}

		$cacheTime = CATALOG_CACHE_DEFAULT_TIME;
		if (defined("CATALOG_CACHE_TIME"))
			$cacheTime = IntVal(CATALOG_CACHE_TIME);

		global $CACHE_MANAGER;
		if ($CACHE_MANAGER->Read($cacheTime, "catalog_group_perms"))
		{
			$arData = $CACHE_MANAGER->Get("catalog_group_perms");
		}
		else
		{
			$dbPriceGroups = CCatalogGroup::GetGroupsList(array());
			while ($arPriceGroup = $dbPriceGroups->Fetch())
			{
				$arPriceGroup["GROUP_ID"] = IntVal($arPriceGroup["GROUP_ID"]);
				$arPriceGroup["CATALOG_GROUP_ID"] = IntVal($arPriceGroup["CATALOG_GROUP_ID"]);

				$key = (($arPriceGroup["BUY"] == "Y") ? "buy" : "view");

				$arData[$arPriceGroup["GROUP_ID"]][$key][] = IntVal($arPriceGroup["CATALOG_GROUP_ID"]);
			}
			$CACHE_MANAGER->Set("catalog_group_perms", $arData);
		}

		for ($i = 0, $cnt = count($arUserGroupsFilter); $i < $cnt; $i++)
		{
			if (array_key_exists($arUserGroupsFilter[$i], $arData))
			{
				if (array_key_exists("view", $arData[$arUserGroupsFilter[$i]]))
					$arResult["view"] = array_merge($arResult["view"], $arData[$arUserGroupsFilter[$i]]["view"]);
				if (array_key_exists("buy", $arData[$arUserGroupsFilter[$i]]))
					$arResult["buy"] = array_merge($arResult["buy"], $arData[$arUserGroupsFilter[$i]]["buy"]);
			}
		}

		$arResult["view"] = array_unique($arResult["view"]);
		$arResult["buy"] = array_unique($arResult["buy"]);

		if (count($arCatalogGroupsFilter) > 0)
		{
			$arTmp = array();
			foreach ($arResult["view"] as $i => $arView)
			//for ($i = 0, $cnt = count($arResult["view"]); $i < $cnt; $i++)
			{
				if (in_array($arResult["view"][$i], $arCatalogGroupsFilter))
					$arTmp[] = $arResult["view"][$i];
			}
			$arResult["view"] = $arTmp;
		}

		return $arResult;
	}

	function GetListArray()
	{
		$arResult = array();

		if (defined("CATALOG_SKIP_CACHE") && CATALOG_SKIP_CACHE)
		{
			$dbRes = CCatalogGroup::GetListEx(
				array("SORT" => "ASC"),
				array(),
				false,
				false,
				array("ID", "NAME", "BASE", "SORT", "NAME_LANG")
			);
			while ($arRes = $dbRes->Fetch())
				$arResult[$arRes["ID"]] = $arRes;
		}
		else
		{
			$cacheTime = CATALOG_CACHE_DEFAULT_TIME;
			if (defined("CATALOG_CACHE_TIME"))
				$cacheTime = IntVal(CATALOG_CACHE_TIME);

			global $CACHE_MANAGER;
			if ($CACHE_MANAGER->Read($cacheTime, "catalog_group_".LANGUAGE_ID, "catalog_group"))
			{
				$arResult = $CACHE_MANAGER->Get("catalog_group_".LANGUAGE_ID);
			}
			else
			{
				$dbRes = CCatalogGroup::GetListEx(
					array("SORT" => "ASC"),
					array(),
					false,
					false,
					array("ID", "NAME", "BASE", "SORT", "NAME_LANG")
				);
				while ($arRes = $dbRes->Fetch())
					$arResult[$arRes["ID"]] = $arRes;

				$CACHE_MANAGER->Set("catalog_group_".LANGUAGE_ID, $arResult);
			}
		}

		return $arResult;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		$ID = intval($ID);

		if (!CCatalogGroup::CheckFields("UPDATE", $arFields, $ID))
			return False;

		$db_events = GetModuleEvents("catalog", "OnBeforeGroupUpdate");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID, &$arFields))===false)
				return false;

		if ($arFields["BASE"] == "Y")
		{
			$dbBaseGroup = CCatalogGroup::GetList(
					array(),
					array(
							"BASE" => "Y",
							"!ID" => $ID
						)
				);
			while ($arBaseGroup = $dbBaseGroup->Fetch())
				CCatalogGroup::Update($arBaseGroup["ID"], array("BASE" => "N"));
		}

		$strUpdate = $DB->PrepareUpdate("b_catalog_group", $arFields);
		$strSql = "UPDATE b_catalog_group SET ".$strUpdate." WHERE ID = ".$ID." ";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if (isset($arFields["USER_GROUP"]) && is_array($arFields["USER_GROUP"]))
		{
			$DB->Query("DELETE FROM b_catalog_group2group WHERE CATALOG_GROUP_ID = ".$ID." AND BUY <> 'Y'");
			for ($i = 0; $i < count($arFields["USER_GROUP"]); $i++)
			{
				if (IntVal($arFields["USER_GROUP"][$i])>0)
				{
					$strSql =
						"INSERT INTO b_catalog_group2group(CATALOG_GROUP_ID, GROUP_ID, BUY) ".
						"VALUES(".$ID.", ".IntVal($arFields["USER_GROUP"][$i]).", 'N')";
					$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				}
			}
		}

		if (isset($arFields["USER_GROUP_BUY"]) && is_array($arFields["USER_GROUP_BUY"]))
		{
			$DB->Query("DELETE FROM b_catalog_group2group WHERE CATALOG_GROUP_ID = ".$ID." AND BUY = 'Y'");
			for ($i = 0; $i < count($arFields["USER_GROUP_BUY"]); $i++)
			{
				if (IntVal($arFields["USER_GROUP_BUY"][$i])>0)
				{
					$strSql =
						"INSERT INTO b_catalog_group2group(CATALOG_GROUP_ID, GROUP_ID, BUY) ".
						"VALUES(".$ID.", ".IntVal($arFields["USER_GROUP_BUY"][$i]).", 'Y')";
					$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				}
			}
		}

		if (isset($arFields["USER_LANG"]) && is_array($arFields["USER_LANG"]))
		{
			$DB->Query("DELETE FROM b_catalog_group_lang WHERE CATALOG_GROUP_ID = ".$ID." ");
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
			$GLOBALS["CACHE_MANAGER"]->CleanDir("catalog_group");
			$GLOBALS["CACHE_MANAGER"]->Clean("catalog_group_perms");
		}

		$GLOBALS["stackCacheManager"]->Clear("catalog_GetQueryBuildArrays");
		$GLOBALS["stackCacheManager"]->Clear("catalog_discount");

		$events = GetModuleEvents("catalog", "OnGroupUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		return true;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = IntVal($ID);
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
					$GLOBALS["CACHE_MANAGER"]->CleanDir("catalog_group");
					$GLOBALS["CACHE_MANAGER"]->Clean("catalog_group_perms");
				}

				$GLOBALS["stackCacheManager"]->Clear("catalog_GetQueryBuildArrays");
				$GLOBALS["stackCacheManager"]->Clear("catalog_discount");

				$DB->Query("DELETE FROM b_catalog_price WHERE CATALOG_GROUP_ID = ".$ID." ");
				$DB->Query("DELETE FROM b_catalog_group2group WHERE CATALOG_GROUP_ID = ".$ID." ");
				$DB->Query("DELETE FROM b_catalog_group_lang WHERE CATALOG_GROUP_ID = ".$ID." ");
				return $DB->Query("DELETE FROM b_catalog_group WHERE ID = ".$ID." ", true);
			}
		}

		return false;
	}

	function GetBaseGroup()
	{
		$CATALOG_BASE_GROUP = $GLOBALS["CATALOG_BASE_GROUP"];
		if (!isset($CATALOG_BASE_GROUP) || count($CATALOG_BASE_GROUP)<3 || intval($CATALOG_BASE_GROUP["ID"])<=0)
		{
			$db_res = CCatalogGroup::GetList(array("NAME" => "ASC"), array("BASE"=>"Y"));
			if ($res = $db_res->Fetch())
			{
				$CATALOG_BASE_GROUP["ID"] = intval($res["ID"]);
				$CATALOG_BASE_GROUP["NAME"] = $res["NAME"];
				$CATALOG_BASE_GROUP["NAME_LANG"] = (!empty($res["NAME_LANG"]) ? $res["NAME_LANG"] : '');
				$GLOBALS["CATALOG_BASE_GROUP"] = $CATALOG_BASE_GROUP;
			}
			else
			{
				unset($GLOBALS["CATALOG_BASE_GROUP"]);
				return false;
			}
		}

		return $CATALOG_BASE_GROUP;
	}
}
?>