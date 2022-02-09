<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/general/price.php");

/***********************************************************************/
/***********  CPrice  **************************************************/
/***********************************************************************/
class CPrice extends CAllPrice
{
	function Add($arFields,$boolRecalc = false)
	{
		global $DB;

		if (!CPrice::CheckFields("ADD", $arFields, 0))
			return false;

		$boolBase = false;
		$arFields['RECALC'] = ($boolRecalc === true ? true : false);

		$events = GetModuleEvents("catalog", "OnBeforePriceAdd");
		while ($arEvent = $events->Fetch())
		{
			ExecuteModuleEventEx($arEvent, array(&$arFields));
		}

		if (!empty($arFields['RECALC']) && $arFields['RECALC'] === true)
		{
			CPrice::ReCountFromBase($arFields,$boolBase);
		}

		$arInsert = $DB->PrepareInsert("b_catalog_price", $arFields);

		$strSql =
			"INSERT INTO b_catalog_price(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$ID = IntVal($DB->LastID());

		if ($ID > 0 && $boolBase == true)
		{
			CPrice::ReCountForBase($arFields);
		}

		$events = GetModuleEvents("catalog", "OnPriceAdd");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		// strange copy-paste bug
		$events = GetModuleEvents("sale", "OnPriceAdd");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		return $ID;
	}

	function GetByID($ID)
	{
		global $DB, $USER;
		if (!$USER->IsAdmin())
		{
			$strSql =
				"SELECT CP.ID, CP.PRODUCT_ID, CP.EXTRA_ID, CP.CATALOG_GROUP_ID, CP.PRICE, ".
				"	CP.CURRENCY, CP.QUANTITY_FROM, CP.QUANTITY_TO, IF(CGG.ID IS NULL, 'N', 'Y') as CAN_ACCESS, CP.TMP_ID, ".
				"	CGL.NAME as CATALOG_GROUP_NAME, IF(CGG1.ID IS NULL, 'N', 'Y') as CAN_BUY, ".
				"	".$DB->DateToCharFunction("CP.TIMESTAMP_X", "FULL")." as TIMESTAMP_X ".
				"FROM b_catalog_price CP, b_catalog_group CG ".
				"	LEFT JOIN b_catalog_group2group CGG ON (CG.ID = CGG.CATALOG_GROUP_ID AND CGG.GROUP_ID IN (".$USER->GetGroups().") AND CGG.BUY <> 'Y') ".
				"	LEFT JOIN b_catalog_group2group CGG1 ON (CG.ID = CGG1.CATALOG_GROUP_ID AND CGG1.GROUP_ID IN (".$USER->GetGroups().") AND CGG1.BUY = 'Y') ".
				"	LEFT JOIN b_catalog_group_lang CGL ON (CG.ID = CGL.CATALOG_GROUP_ID AND CGL.LID = '".LANGUAGE_ID."') ".
				"WHERE CP.ID = ".IntVal($ID)." ".
				"	AND CP.CATALOG_GROUP_ID = CG.ID ".
				"GROUP BY CP.ID, CP.PRODUCT_ID, CP.EXTRA_ID, CP.CATALOG_GROUP_ID, CP.PRICE, CP.CURRENCY, CP.QUANTITY_FROM, CP.QUANTITY_TO, CP.TIMESTAMP_X ";
		}
		else
		{
			$strSql =
				"SELECT CP.ID, CP.PRODUCT_ID, CP.EXTRA_ID, CP.CATALOG_GROUP_ID, CP.PRICE, ".
				"	CP.CURRENCY, CP.QUANTITY_FROM, CP.QUANTITY_TO, 'Y' as CAN_ACCESS, CP.TMP_ID, CGL.NAME as CATALOG_GROUP_NAME, ".
				"	'Y' as CAN_BUY, ".
				"	".$DB->DateToCharFunction("CP.TIMESTAMP_X", "FULL")." as TIMESTAMP_X ".
				"FROM b_catalog_price CP ".
				"	LEFT JOIN b_catalog_group_lang CGL ON (CP.CATALOG_GROUP_ID = CGL.CATALOG_GROUP_ID AND CGL.LID = '".LANGUAGE_ID."') ".
				"WHERE CP.ID = ".IntVal($ID)." ";
		}
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if ($res = $db_res->Fetch())
			return $res;

		return false;
	}

	function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB, $USER;

		// for old execution style
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
		}

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "PRODUCT_ID", "EXTRA_ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "TIMESTAMP_X", "QUANTITY_FROM", "QUANTITY_TO", "BASE", "SORT", "CATALOG_GROUP_NAME", "CAN_ACCESS", "CAN_BUY");

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "P.ID", "TYPE" => "int"),
				"PRODUCT_ID" => array("FIELD" => "P.PRODUCT_ID", "TYPE" => "int"),
				"EXTRA_ID" => array("FIELD" => "P.EXTRA_ID", "TYPE" => "int"),
				"CATALOG_GROUP_ID" => array("FIELD" => "P.CATALOG_GROUP_ID", "TYPE" => "int"),
				"PRICE" => array("FIELD" => "P.PRICE", "TYPE" => "double"),
				"CURRENCY" => array("FIELD" => "P.CURRENCY", "TYPE" => "string"),
				"TIMESTAMP_X" => array("FIELD" => "P.TIMESTAMP_X", "TYPE" => "datetime"),
				"QUANTITY_FROM" => array("FIELD" => "P.QUANTITY_FROM", "TYPE" => "int"),
				"QUANTITY_TO" => array("FIELD" => "P.QUANTITY_TO", "TYPE" => "int"),
				"TMP_ID" => array("FIELD" => "P.TMP_ID", "TYPE" => "string"),

				"BASE" => array("FIELD" => "CG.BASE", "TYPE" => "char", "FROM" => "INNER JOIN b_catalog_group CG ON (P.CATALOG_GROUP_ID = CG.ID)"),
				"SORT" => array("FIELD" => "CG.SORT", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_group CG ON (P.CATALOG_GROUP_ID = CG.ID)"),

				"PRODUCT_QUANTITY" => array("FIELD" => "CP.QUANTITY", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_product CP ON (P.PRODUCT_ID = CP.ID)"),
				"PRODUCT_QUANTITY_TRACE" => array("FIELD" => "IF (CP.QUANTITY_TRACE = 'D', '".$DB->ForSql(COption::GetOptionString('catalog','default_quantity_trace','N'))."', CP.QUANTITY_TRACE)", "TYPE" => "char", "FROM" => "INNER JOIN b_catalog_product CP ON (P.PRODUCT_ID = CP.ID)"),
				"PRODUCT_CAN_BUY_ZERO" => array("FIELD" => "IF (CP.CAN_BUY_ZERO = 'D', '".$DB->ForSql(COption::GetOptionString('catalog','default_can_buy_zero','N'))."', CP.CAN_BUY_ZERO)", "TYPE" => "char", "FROM" => "INNER JOIN b_catalog_product CP ON (P.PRODUCT_ID = CP.ID)"),
				"PRODUCT_NEGATIVE_AMOUNT_TRACE" => array("FIELD" => "IF (CP.NEGATIVE_AMOUNT_TRACE = 'D', '".$DB->ForSql(COption::GetOptionString('catalog','allow_negative_amount','N'))."', CP.NEGATIVE_AMOUNT_TRACE)", "TYPE" => "char", "FROM" => "INNER JOIN b_catalog_product CP ON (P.PRODUCT_ID = CP.ID)"),
				"PRODUCT_WEIGHT" => array("FIELD" => "CP.WEIGHT", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_product CP ON (P.PRODUCT_ID = CP.ID)"),

				"ELEMENT_IBLOCK_ID" => array("FIELD" => "IE.IBLOCK_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_iblock_element IE ON (P.PRODUCT_ID = IE.ID)"),

				"CATALOG_GROUP_NAME" => array("FIELD" => "CGL.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_catalog_group_lang CGL ON (CG.ID = CGL.CATALOG_GROUP_ID AND CGL.LID = '".LANGUAGE_ID."')"),
			);
		if (!$USER->IsAdmin())
		{
			$arFields["CAN_ACCESS"] = array("FIELD" => "IF(CGG.ID IS NULL, 'N', 'Y')", "TYPE" => "char", "FROM" => "LEFT JOIN b_catalog_group2group CGG ON (CG.ID = CGG.CATALOG_GROUP_ID AND CGG.GROUP_ID IN (".$USER->GetGroups().") AND CGG.BUY <> 'Y')");
			$arFields["CAN_BUY"] = array("FIELD" => "IF(CGG1.ID IS NULL, 'N', 'Y')", "TYPE" => "char", "FROM" => "LEFT JOIN b_catalog_group2group CGG1 ON (CG.ID = CGG1.CATALOG_GROUP_ID AND CGG1.GROUP_ID IN (".$USER->GetGroups().") AND CGG1.BUY = 'Y')");
		}
		else
		{
			$arFields["CAN_ACCESS"] = array("FIELD" => "'Y'", "TYPE" => "char");
			$arFields["CAN_BUY"] = array("FIELD" => "'Y'", "TYPE" => "char");
		}
		// <-- FIELDS

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		if ((array_key_exists("CAN_ACCESS", $arFields) || array_key_exists("CAN_BUY", $arFields)) && !$USER->IsAdmin())
			$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "DISTINCT", $arSqls["SELECT"]);
		else
			$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_catalog_price P ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_catalog_price P ".
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
				"FROM b_catalog_price P ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

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
		global $DB;

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "PRODUCT_ID", "EXTRA_ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "TIMESTAMP_X", "QUANTITY_FROM", "QUANTITY_TO", "TMP_ID");

		// FIELDS -->
		$arFields = array(
			"ID" => array("FIELD" => "P.ID", "TYPE" => "int"),
			"PRODUCT_ID" => array("FIELD" => "P.PRODUCT_ID", "TYPE" => "int"),
			"EXTRA_ID" => array("FIELD" => "P.EXTRA_ID", "TYPE" => "int"),
			"CATALOG_GROUP_ID" => array("FIELD" => "P.CATALOG_GROUP_ID", "TYPE" => "int"),
			"PRICE" => array("FIELD" => "P.PRICE", "TYPE" => "double"),
			"CURRENCY" => array("FIELD" => "P.CURRENCY", "TYPE" => "string"),
			"TIMESTAMP_X" => array("FIELD" => "P.TIMESTAMP_X", "TYPE" => "datetime"),
			"QUANTITY_FROM" => array("FIELD" => "P.QUANTITY_FROM", "TYPE" => "int"),
			"QUANTITY_TO" => array("FIELD" => "P.QUANTITY_TO", "TYPE" => "int"),
			"TMP_ID" => array("FIELD" => "P.TMP_ID", "TYPE" => "string"),

			"PRODUCT_QUANTITY" => array("FIELD" => "CP.QUANTITY", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_product CP ON (P.PRODUCT_ID = CP.ID)"),
			"PRODUCT_QUANTITY_TRACE" => array("FIELD" => "IF (CP.QUANTITY_TRACE = 'D', '".$DB->ForSql(COption::GetOptionString('catalog','default_quantity_trace','N'))."', CP.QUANTITY_TRACE)", "TYPE" => "char", "FROM" => "INNER JOIN b_catalog_product CP ON (P.PRODUCT_ID = CP.ID)"),
			"PRODUCT_CAN_BUY_ZERO" => array("FIELD" => "IF (CP.CAN_BUY_ZERO = 'D', '".$DB->ForSql(COption::GetOptionString('catalog','default_can_buy_zero','N'))."', CP.CAN_BUY_ZERO)", "TYPE" => "char", "FROM" => "INNER JOIN b_catalog_product CP ON (P.PRODUCT_ID = CP.ID)"),
			"PRODUCT_NEGATIVE_AMOUNT_TRACE" => array("FIELD" => "IF (CP.NEGATIVE_AMOUNT_TRACE = 'D', '".$DB->ForSql(COption::GetOptionString('catalog','allow_negative_amount','N'))."', CP.NEGATIVE_AMOUNT_TRACE)", "TYPE" => "char", "FROM" => "INNER JOIN b_catalog_product CP ON (P.PRODUCT_ID = CP.ID)"),
			"PRODUCT_WEIGHT" => array("FIELD" => "CP.WEIGHT", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_product CP ON (P.PRODUCT_ID = CP.ID)"),

			"ELEMENT_IBLOCK_ID" => array("FIELD" => "IE.IBLOCK_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_iblock_element IE ON (P.PRODUCT_ID = IE.ID)"),
			"ELEMENT_NAME" => array("FIELD" => "IE.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_iblock_element IE ON (P.PRODUCT_ID = IE.ID)"),

			"CATALOG_GROUP_BASE" => array("FIELD" => "CG.BASE", "TYPE" => "char", "FROM" => "INNER JOIN b_catalog_group CG ON (P.CATALOG_GROUP_ID = CG.ID)"),
			"CATALOG_GROUP_SORT" => array("FIELD" => "CG.SORT", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_group CG ON (P.CATALOG_GROUP_ID = CG.ID)"),

			"CATALOG_GROUP_NAME" => array("FIELD" => "CGL.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_catalog_group_lang CGL ON (P.CATALOG_GROUP_ID = CGL.CATALOG_GROUP_ID AND CGL.LID = '".LANGUAGE_ID."')"),

			"GROUP_GROUP_ID" => array("FIELD" => "CGG.GROUP_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_group2group CGG ON (P.CATALOG_GROUP_ID = CGG.CATALOG_GROUP_ID)"),
			"GROUP_BUY" => array("FIELD" => "CGG.BUY", "TYPE" => "char", "FROM" => "INNER JOIN b_catalog_group2group CGG ON (P.CATALOG_GROUP_ID = CGG.CATALOG_GROUP_ID)")
		);
		// <-- FIELDS

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_catalog_price P ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_catalog_price P ".
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
				"FROM b_catalog_price P ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

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