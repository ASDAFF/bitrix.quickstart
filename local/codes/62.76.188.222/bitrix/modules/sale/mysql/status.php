<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/status.php");

class CSaleStatus extends CAllSaleStatus
{
	function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		if (!is_array($arOrder) && !is_array($arFilter))
		{
			$arOrder = strval($arOrder);
			$arFilter = strval($arFilter);
			if (strlen($arOrder) > 0 && strlen($arFilter) > 0)
				$arOrder = array($arOrder => $arFilter);
			else
				$arOrder = array();

			$arFilter = array();
			$arFilter["LID"] = LANGUAGE_ID;
			if ($arGroupBy)
			{
				$arGroupBy = strval($arGroupBy);
				if (strlen($arGroupBy) > 0)
					$arFilter["LID"] = $arGroupBy;
			}
			$arGroupBy = false;

			$arSelectFields = array("ID", "SORT", "LID", "NAME", "DESCRIPTION");
		}

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "S.ID", "TYPE" => "char"),
				"SORT" => array("FIELD" => "S.SORT", "TYPE" => "int"),
				"GROUP_ID" => array("FIELD" => "SSG.GROUP_ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_sale_status2group SSG ON (S.ID = SSG.STATUS_ID)"),
				"PERM_VIEW" => array("FIELD" => "SSG.PERM_VIEW", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_status2group SSG ON (S.ID = SSG.STATUS_ID)"),
				"PERM_CANCEL" => array("FIELD" => "SSG.PERM_CANCEL", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_status2group SSG ON (S.ID = SSG.STATUS_ID)"),
				"PERM_DELIVERY" => array("FIELD" => "SSG.PERM_DELIVERY", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_status2group SSG ON (S.ID = SSG.STATUS_ID)"),
				"PERM_MARK" => array("FIELD" => "SSG.PERM_MARK", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_status2group SSG ON (S.ID = SSG.STATUS_ID)"),
				"PERM_DEDUCTION" => array("FIELD" => "SSG.PERM_DEDUCTION", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_status2group SSG ON (S.ID = SSG.STATUS_ID)"),
				"PERM_PAYMENT" => array("FIELD" => "SSG.PERM_PAYMENT", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_status2group SSG ON (S.ID = SSG.STATUS_ID)"),
				"PERM_STATUS" => array("FIELD" => "SSG.PERM_STATUS", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_status2group SSG ON (S.ID = SSG.STATUS_ID)"),
				"PERM_STATUS_FROM" => array("FIELD" => "SSG.PERM_STATUS_FROM", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_status2group SSG ON (S.ID = SSG.STATUS_ID)"),
				"PERM_UPDATE" => array("FIELD" => "SSG.PERM_UPDATE", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_status2group SSG ON (S.ID = SSG.STATUS_ID)"),
				"PERM_DELETE" => array("FIELD" => "SSG.PERM_DELETE", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_status2group SSG ON (S.ID = SSG.STATUS_ID)"),
				"LID" => array("FIELD" => "SL.LID", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_status_lang SL ON (S.ID = SL.STATUS_ID)"),
				"NAME" => array("FIELD" => "SL.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_status_lang SL ON (S.ID = SL.STATUS_ID)"),
				"DESCRIPTION" => array("FIELD" => "SL.DESCRIPTION", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_status_lang SL ON (S.ID = SL.STATUS_ID)")
			);
		// <-- FIELDS

		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_sale_status S ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!1!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql = 
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_sale_status S ".
			"	".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_sale_status S ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!2.1!=".htmlspecialcharsbx($strSql_tmp)."<br>";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (strlen($arSqls["GROUPBY"]) <= 0)
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				// FOR MYSQL!!! ANOTHER CODE FOR ORACLE
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			//echo "!2.2!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".IntVal($arNavStartParams["nTopCount"]);

			//echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}

	function GetByID($ID, $strLang = LANGUAGE_ID)
	{
		global $DB;

		$ID = $DB->ForSql($ID, 1);
		$strLang = $DB->ForSql($strLang, 2);
		if (isset($GLOBALS["SALE_STATUS"]["SALE_STATUS_CACHE_".$ID."_".$strLang]) && is_array($GLOBALS["SALE_STATUS"]["SALE_STATUS_CACHE_".$ID."_".$strLang]) && is_set($GLOBALS["SALE_STATUS"]["SALE_ORDER_CACHE_".$ID."_".$strLang], "ID"))
		{
			return $GLOBALS["SALE_STATUS"]["SALE_STATUS_CACHE_".$ID."_".$strLang];
		}
		else
		{

			$strSql =
				"SELECT S.ID, S.SORT, SL.LID, SL.NAME, SL.DESCRIPTION ".
				"FROM b_sale_status S ".
				"	LEFT JOIN b_sale_status_lang SL ON (S.ID = SL.STATUS_ID AND SL.LID = '".$strLang."') ".
				"WHERE ID = '".$ID."' ";
			$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			if ($res = $db_res->Fetch())
			{
				$GLOBALS["SALE_STATUS"]["SALE_STATUS_CACHE_".$ID."_".$strLang] = $res;
				return $res;
			}
		}
		return False;
	}


	function GetPermissionsList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "S.ID", "TYPE" => "int"),
				"GROUP_ID" => array("FIELD" => "S.GROUP_ID", "TYPE" => "int"),
				"STATUS_ID" => array("FIELD" => "S.STATUS_ID", "TYPE" => "char"),
				"PERM_VIEW" => array("FIELD" => "S.PERM_VIEW", "TYPE" => "char"),
				"PERM_CANCEL" => array("FIELD" => "S.PERM_CANCEL", "TYPE" => "char"),
				"PERM_MARK" => array("FIELD" => "S.PERM_MARK", "TYPE" => "char"),
				"PERM_DELIVERY" => array("FIELD" => "S.PERM_DELIVERY", "TYPE" => "char"),
				"PERM_DEDUCTION" => array("FIELD" => "S.PERM_DEDUCTION", "TYPE" => "char"),
				"PERM_PAYMENT" => array("FIELD" => "S.PERM_PAYMENT", "TYPE" => "char"),
				"PERM_STATUS" => array("FIELD" => "S.PERM_STATUS", "TYPE" => "char"),
				"PERM_STATUS_FROM" => array("FIELD" => "S.PERM_STATUS_FROM", "TYPE" => "char"),
				"PERM_UPDATE" => array("FIELD" => "S.PERM_UPDATE", "TYPE" => "char"),
				"PERM_DELETE" => array("FIELD" => "S.PERM_DELETE", "TYPE" => "char"),
			);
		// <-- FIELDS

		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_sale_status2group S ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!1!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql = 
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_sale_status2group S ".
			"	".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_sale_status2group S ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!2.1!=".htmlspecialcharsbx($strSql_tmp)."<br>";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (strlen($arSqls["GROUPBY"]) <= 0)
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				// FOR MYSQL!!! ANOTHER CODE FOR ORACLE
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			//echo "!2.2!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".IntVal($arNavStartParams["nTopCount"]);

			//echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}
}
?>