<?
IncludeModuleLangFile(__FILE__);

/***********************************************************************/
/***********  CCatalogProductGroups  ***********************************/
/***********************************************************************/
class CAllCatalogProductGroups
{
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		if ((is_set($arFields, "PRODUCT_ID") || $ACTION=="ADD") && intval($arFields["PRODUCT_ID"]) <= 0)
			return false;

		if ((is_set($arFields, "GROUP_ID") || $ACTION=="ADD") && intval($arFields["GROUP_ID"]) <= 0)
			return false;

		if ((is_set($arFields, "ACCESS_LENGTH") || $ACTION=="ADD"))
		{
			$arFields["ACCESS_LENGTH"] = intval($arFields["ACCESS_LENGTH"]);
			if ($arFields["ACCESS_LENGTH"] < 0)
				$arFields["ACCESS_LENGTH"] = 0;
		}

		if ((is_set($arFields, "ACCESS_LENGTH_TYPE") || $ACTION=="ADD") && !array_key_exists($arFields["ACCESS_LENGTH_TYPE"], $GLOBALS["CATALOG_TIME_PERIOD_TYPES"]))
		{
			$arTypeKeys = array_keys($GLOBALS["CATALOG_TIME_PERIOD_TYPES"]);
			$arFields["ACCESS_LENGTH_TYPE"] = $arRecurSchemeKeys[1];
		}

		return True;
	}

	function GetByID($ID)
	{
		global $DB;
		$ID = intval($ID);

		$strSql =
			"SELECT CPG.ID, CPG.PRODUCT_ID, CPG.GROUP_ID, CPG.ACCESS_LENGTH, CPG.ACCESS_LENGTH_TYPE ".
			"FROM b_catalog_product2group CPG ".
			"WHERE CPG.ID = ".$ID." ";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if ($res = $db_res->Fetch())
			return $res;

		return false;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		$ID = intval($ID);
		if ($ID <= 0)
			return false;

		if (!CCatalogProductGroups::CheckFields("UPDATE", $arFields, $ID))
			return False;

		$strUpdate = $DB->PrepareUpdate("b_catalog_product2group", $arFields);
		$strUpdate = Trim($strUpdate);
		if (StrLen($strUpdate) > 0)
		{
			$strSql = "UPDATE b_catalog_product2group SET ".$strUpdate." WHERE ID = ".$ID." ";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $ID;
	}

	function Delete($ID)
	{
		global $DB;

		$ID = intval($ID);
		if ($ID <= 0)
			return false;

		return $DB->Query("DELETE FROM b_catalog_product2group WHERE ID = ".$ID." ", True);
	}

	function DeleteByGroup($ID)
	{
		global $DB;

		$ID = intval($ID);
		if ($ID <= 0)
			return false;

		return $DB->Query("DELETE FROM b_catalog_product2group WHERE GROUP_ID = ".$ID." ", True);
	}

	function OnGroupDelete($ID)
	{
		CCatalogProductGroups::DeleteByGroup($ID);
	}
}
?>