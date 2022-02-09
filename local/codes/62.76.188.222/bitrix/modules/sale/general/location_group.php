<?
class CAllSaleLocationGroup
{
	function GetLocationList($arFilter=Array())
	{
		global $DB;
		$arSqlSearch = Array();

		if(!is_array($arFilter))
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		$countFieldKey = count($filter_keys);
		for($i=0; $i < $countFieldKey; $i++)
		{
			$val = $DB->ForSql($arFilter[$filter_keys[$i]]);
			if (strlen($val)<=0) continue;

			$key = $filter_keys[$i];
			if ($key[0]=="!")
			{
				$key = substr($key, 1);
				$bInvert = true;
			}
			else
				$bInvert = false;

			switch(ToUpper($key))
			{
			case "LOCATION_ID":
				$arSqlSearch[] = "LOCATION_ID ".($bInvert?"<>":"=")." ".IntVal($val)." ";
				break;
			case "LOCATION_GROUP_ID":
				$arSqlSearch[] = "LOCATION_GROUP_ID ".($bInvert?"<>":"=")." ".IntVal($val)." ";
				break;
			}
		}

		$strSqlSearch = "";
		$countSqlSearch = count($arSqlSearch);
		for($i=0; $i < $countSqlSearch; $i++)
		{
			$strSqlSearch .= " AND ";
			$strSqlSearch .= " (".$arSqlSearch[$i].") ";
		}

		$strSql =
			"SELECT LOCATION_ID, LOCATION_GROUP_ID ".
			"FROM b_sale_location2location_group ".
			"WHERE 1 = 1 ".
			"	".$strSqlSearch." ";

		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}

	function GetGroupLangByID($ID, $strLang = LANGUAGE_ID)
	{
		global $DB;

		$ID = IntVal($ID);
		$strSql =
			"SELECT ID, LOCATION_GROUP_ID, LID, NAME ".
			"FROM b_sale_location_group_lang ".
			"WHERE LOCATION_GROUP_ID = ".$ID." ".
			"	AND LID = '".$DB->ForSql($strLang, 2)."'";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}


	function CheckFields($ACTION, &$arFields)
	{
		global $DB;

		if (is_set($arFields, "SORT") && IntVal($arFields["SORT"])<=0)
			$arFields["SORT"] = 100;

		if (is_set($arFields, "LOCATION_ID") && (!is_array($arFields["LOCATION_ID"]) || count($arFields["LOCATION_ID"])<=0))
			return false;

		if (is_set($arFields, "LANG"))
		{
			$db_lang = CLangAdmin::GetList(($b="sort"), ($o="asc"), array("ACTIVE" => "Y"));
			while ($arLang = $db_lang->Fetch())
			{
				$bFound = False;
				$coountarFieldLang = count($arFields["LANG"]);
				for ($i = 0; $i < $coountarFieldLang; $i++)
				{
					if ($arFields["LANG"][$i]["LID"]==$arLang["LID"] && strlen($arFields["LANG"][$i]["NAME"])>0)
					{
						$bFound = True;
					}
				}
				if (!$bFound)
					return false;
			}
		}

		return True;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		$ID = IntVal($ID);
		if (!CSaleLocationGroup::CheckFields("UPDATE", $arFields))
			return false;

		$db_events = GetModuleEvents("sale", "OnBeforeLocationGroupUpdate");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID, &$arFields))===false)
				return false;

		$events = GetModuleEvents("sale", "OnLocationGroupUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		$strUpdate = $DB->PrepareUpdate("b_sale_location_group", $arFields);
		$strSql = "UPDATE b_sale_location_group SET ".$strUpdate." WHERE ID = ".$ID."";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if (is_set($arFields, "LANG"))
		{
			$DB->Query("DELETE FROM b_sale_location_group_lang WHERE LOCATION_GROUP_ID = ".$ID."");

			$countFieldLang = count($arFields["LANG"]);
			for ($i = 0; $i < $countFieldLang; $i++)
			{
				$arInsert = $DB->PrepareInsert("b_sale_location_group_lang", $arFields["LANG"][$i]);
				$strSql =
					"INSERT INTO b_sale_location_group_lang(LOCATION_GROUP_ID, ".$arInsert[0].") ".
					"VALUES(".$ID.", ".$arInsert[1].")";
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
		}

		if (is_set($arFields, "LOCATION_ID"))
		{
			$DB->Query("DELETE FROM b_sale_location2location_group WHERE LOCATION_GROUP_ID = ".$ID."");

			$countArFieldLoc = count($arFields["LOCATION_ID"]);
			for ($i = 0; $i < $countArFieldLoc; $i++)
			{
				$strSql =
					"INSERT INTO b_sale_location2location_group(LOCATION_ID, LOCATION_GROUP_ID) ".
					"VALUES(".$arFields["LOCATION_ID"][$i].", ".$ID.")";
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
		}

		return $ID;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = IntVal($ID);

		$db_events = GetModuleEvents("sale", "OnBeforeLocationGroupDelete");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
				return false;

		$events = GetModuleEvents("sale", "OnLocationGroupDelete");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID));

		$DB->Query("DELETE FROM b_sale_delivery2location WHERE LOCATION_ID = ".$ID." AND LOCATION_TYPE = 'G'", true);
		$DB->Query("DELETE FROM b_sale_location2location_group WHERE LOCATION_GROUP_ID = ".$ID."", true);
		$DB->Query("DELETE FROM b_sale_location_group_lang WHERE LOCATION_GROUP_ID = ".$ID."", true);

		return $DB->Query("DELETE FROM b_sale_location_group WHERE ID = ".$ID."", true);
	}

	function OnLangDelete($strLang)
	{
		global $DB;
		$DB->Query("DELETE FROM b_sale_location_group_lang WHERE LID = '".$DB->ForSql($strLang)."'", true);
		return True;
	}
}
?>