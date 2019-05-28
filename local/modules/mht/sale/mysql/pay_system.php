<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/pay_system.php");

class CSalePaySystem extends CAllSalePaySystem
{
	function GetList($arOrder = array("SORT"=>"ASC", "NAME"=>"ASC"), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		if (isset($arFilter["PERSON_TYPE_ID"]))
		{
			$arFilter["PSA_PERSON_TYPE_ID"] = $arFilter["PERSON_TYPE_ID"];
			unset($arFilter["PERSON_TYPE_ID"]);
			if (count($arSelectFields) <= 0)
				$arSelectFields = array("*");
		}

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "LID", "CURRENCY", "NAME", "ACTIVE", "SORT", "DESCRIPTION");

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "P.ID", "TYPE" => "int"),
				"LID" => array("FIELD" => "P.LID", "TYPE" => "string"),
				"CURRENCY" => array("FIELD" => "P.CURRENCY", "TYPE" => "string"),
				"NAME" => array("FIELD" => "P.NAME", "TYPE" => "string"),
				"ACTIVE" => array("FIELD" => "P.ACTIVE", "TYPE" => "char"),
				"SORT" => array("FIELD" => "P.SORT", "TYPE" => "int"),
				"DESCRIPTION" => array("FIELD" => "P.DESCRIPTION", "TYPE" => "string"),
				"PSA_ID" => array("FIELD" => "PA.ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_NAME" => array("FIELD" => "PA.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_ACTION_FILE" => array("FIELD" => "PA.ACTION_FILE", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_RESULT_FILE" => array("FIELD" => "PA.RESULT_FILE", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_NEW_WINDOW" => array("FIELD" => "PA.NEW_WINDOW", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_PERSON_TYPE_ID" => array("FIELD" => "PA.PERSON_TYPE_ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_PARAMS" => array("FIELD" => "PA.PARAMS", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_TARIF" => array("FIELD" => "PA.TARIF", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_HAVE_PAYMENT" => array("FIELD" => "PA.HAVE_PAYMENT", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_HAVE_ACTION" => array("FIELD" => "PA.HAVE_ACTION", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_HAVE_RESULT" => array("FIELD" => "PA.HAVE_RESULT", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_HAVE_PREPAY" => array("FIELD" => "PA.HAVE_PREPAY", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_HAVE_RESULT_RECEIVE" => array("FIELD" => "PA.HAVE_RESULT_RECEIVE", "TYPE" => "char", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_ENCODING" => array("FIELD" => "PA.ENCODING", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
				"PSA_LOGOTIP" => array("FIELD" => "PA.LOGOTIP", "TYPE" => "int", "FROM" => "LEFT JOIN b_sale_pay_system_action PA ON (P.ID = PA.PAY_SYSTEM_ID)"),
			);
		// <-- FIELDS

		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "DISTINCT", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_sale_pay_system P ".
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
			"FROM b_sale_pay_system P ".
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
				"FROM b_sale_pay_system P ".
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
				// for MYSQL only!!! another code for ORACLE
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

		if (!CSalePaySystem::CheckFields("ADD", $arFields))
			return false;

		$arInsert = $DB->PrepareInsert("b_sale_pay_system", $arFields);

		$strSql =
			"INSERT INTO b_sale_pay_system(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$ID = IntVal($DB->LastID());

		return $ID;
	}
}
?>