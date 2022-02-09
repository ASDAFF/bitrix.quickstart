<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/order_props.php");

class CSaleOrderProps extends CAllSaleOrderProps
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
			if (is_array($arGroupBy))
				$arFilter = $arGroupBy;
			else
				$arFilter = array();
			$arGroupBy = false;

			$arSelectFields = array("ID", "PERSON_TYPE_ID", "NAME", "TYPE", "REQUIED", "DEFAULT_VALUE", "SORT", "USER_PROPS", "IS_LOCATION", "PROPS_GROUP_ID", "SIZE1", "SIZE2", "DESCRIPTION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "IS_ZIP", "CODE", "IS_FILTERED", "ACTIVE", "UTIL", "INPUT_FIELD_LOCATION");
		}

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "PERSON_TYPE_ID", "NAME", "TYPE", "REQUIED", "DEFAULT_VALUE", "SORT", "USER_PROPS", "IS_LOCATION", "PROPS_GROUP_ID", "SIZE1", "SIZE2", "DESCRIPTION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "IS_ZIP",	"CODE", "IS_FILTERED", "ACTIVE", "UTIL", "INPUT_FIELD_LOCATION");

		// FIELDS -->
		$arFields = array(
			"ID" => array("FIELD" => "P.ID", "TYPE" => "int"),
			"PERSON_TYPE_ID" => array("FIELD" => "P.PERSON_TYPE_ID", "TYPE" => "int"),
			"NAME" => array("FIELD" => "P.NAME", "TYPE" => "string"),
			"TYPE" => array("FIELD" => "P.TYPE", "TYPE" => "string"),
			"REQUIED" => array("FIELD" => "P.REQUIED", "TYPE" => "char"),
			"REQUIRED" => array("FIELD" => "P.REQUIED", "TYPE" => "char"),
			"DEFAULT_VALUE" => array("FIELD" => "P.DEFAULT_VALUE", "TYPE" => "string"),
			"SORT" => array("FIELD" => "P.SORT", "TYPE" => "int"),
			"USER_PROPS" => array("FIELD" => "P.USER_PROPS", "TYPE" => "char"),
			"IS_LOCATION" => array("FIELD" => "P.IS_LOCATION", "TYPE" => "char"),
			"PROPS_GROUP_ID" => array("FIELD" => "P.PROPS_GROUP_ID", "TYPE" => "int"),
			"SIZE1" => array("FIELD" => "P.SIZE1", "TYPE" => "int"),
			"SIZE2" => array("FIELD" => "P.SIZE2", "TYPE" => "int"),
			"DESCRIPTION" => array("FIELD" => "P.DESCRIPTION", "TYPE" => "string"),
			"IS_EMAIL" => array("FIELD" => "P.IS_EMAIL", "TYPE" => "char"),
			"IS_PROFILE_NAME" => array("FIELD" => "P.IS_PROFILE_NAME", "TYPE" => "char"),
			"IS_PAYER" => array("FIELD" => "P.IS_PAYER", "TYPE" => "char"),
			"IS_LOCATION4TAX" => array("FIELD" => "P.IS_LOCATION4TAX", "TYPE" => "char"),
			"IS_FILTERED" => array("FIELD" => "P.IS_FILTERED", "TYPE" => "char"),
			"IS_ZIP" => array("FIELD" => "P.IS_ZIP", "TYPE" => "char"),
			"CODE" => array("FIELD" => "P.CODE", "TYPE" => "string"),
			"ACTIVE" => array("FIELD" => "P.ACTIVE", "TYPE" => "char"),
			"UTIL" => array("FIELD" => "P.UTIL", "TYPE" => "char"),
			"INPUT_FIELD_LOCATION" => array("FIELD" => "P.INPUT_FIELD_LOCATION", "TYPE" => "int"),

			"GROUP_ID" => array("FIELD" => "PG.ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_sale_order_props_group PG ON (P.PROPS_GROUP_ID = PG.ID)"),
			"GROUP_PERSON_TYPE_ID" => array("FIELD" => "PG.PERSON_TYPE_ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_sale_order_props_group PG ON (P.PROPS_GROUP_ID = PG.ID)"),
			"GROUP_NAME" => array("FIELD" => "PG.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_order_props_group PG ON (P.PROPS_GROUP_ID = PG.ID)"),
			"GROUP_SORT" => array("FIELD" => "PG.SORT", "TYPE" => "int", "FROM" => "LEFT JOIN b_sale_order_props_group PG ON (P.PROPS_GROUP_ID = PG.ID)"),

			"PERSON_TYPE_LID" => array("FIELD" => "SPT.LID", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_person_type SPT ON (P.PERSON_TYPE_ID = SPT.ID)"),
			"PERSON_TYPE_NAME" => array("FIELD" => "SPT.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_person_type SPT ON (P.PERSON_TYPE_ID = SPT.ID)"),
			"PERSON_TYPE_SORT" => array("FIELD" => "SPT.SORT", "TYPE" => "int", "FROM" => "LEFT JOIN b_sale_person_type SPT ON (P.PERSON_TYPE_ID = SPT.ID)"),
			"PERSON_TYPE_ACTIVE" => array("FIELD" => "SPT.ACTIVE", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_person_type SPT ON (P.PERSON_TYPE_ID = SPT.ID)"),
		);
		// <-- FIELDS

		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "DISTINCT", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_sale_order_props P ".
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
			"FROM b_sale_order_props P ".
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
				"FROM b_sale_order_props P ".
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

	function Add($arFields)
	{
		global $DB;

		if (!CSaleOrderProps::CheckFields("ADD", $arFields))
			return false;

		$arInsert = $DB->PrepareInsert("b_sale_order_props", $arFields);

		$strSql =
			"INSERT INTO b_sale_order_props(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$ID = IntVal($DB->LastID());

		return $ID;
	}
}
?>