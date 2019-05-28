<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/pay_system_action.php");

class CSalePaySystemAction extends CAllSalePaySystemAction
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
		}

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "PAY_SYSTEM_ID", "PERSON_TYPE_ID", "NAME", "ACTION_FILE", "RESULT_FILE", "NEW_WINDOW", "PARAMS", "TARIF", "ENCODING", "LOGOTIP");

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "PSA.ID", "TYPE" => "int"),
				"PAY_SYSTEM_ID" => array("FIELD" => "PSA.PAY_SYSTEM_ID", "TYPE" => "int"),
				"PERSON_TYPE_ID" => array("FIELD" => "PSA.PERSON_TYPE_ID", "TYPE" => "int"),
				"NAME" => array("FIELD" => "PSA.NAME", "TYPE" => "string"),
				"ACTION_FILE" => array("FIELD" => "PSA.ACTION_FILE", "TYPE" => "string"),
				"RESULT_FILE" => array("FIELD" => "PSA.RESULT_FILE", "TYPE" => "string"),
				"NEW_WINDOW" => array("FIELD" => "PSA.NEW_WINDOW", "TYPE" => "char"),
				"PARAMS" => array("FIELD" => "PSA.PARAMS", "TYPE" => "string"),
				"TARIF" => array("FIELD" => "PSA.TARIF", "TYPE" => "string"),
				"HAVE_PAYMENT" => array("FIELD" => "PSA.HAVE_PAYMENT", "TYPE" => "char"),
				"HAVE_ACTION" => array("FIELD" => "PSA.HAVE_ACTION", "TYPE" => "char"),
				"HAVE_RESULT" => array("FIELD" => "PSA.HAVE_RESULT", "TYPE" => "char"),
				"HAVE_PREPAY" => array("FIELD" => "PSA.HAVE_PREPAY", "TYPE" => "char"),
				"HAVE_RESULT_RECEIVE" => array("FIELD" => "PSA.HAVE_RESULT_RECEIVE", "TYPE" => "char"),
				"ENCODING" => array("FIELD" => "PSA.ENCODING", "TYPE" => "string"),
				"LOGOTIP" => array("FIELD" => "PSA.LOGOTIP", "TYPE" => "int"),
				"PS_LID" => array("FIELD" => "PS.LID", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_pay_system PS ON (PSA.PAY_SYSTEM_ID = PS.ID)"),
				"PS_CURRENCY" => array("FIELD" => "PS.CURRENCY", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_pay_system PS ON (PSA.PAY_SYSTEM_ID = PS.ID)"),
				"PS_NAME" => array("FIELD" => "PS.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_pay_system PS ON (PSA.PAY_SYSTEM_ID = PS.ID)"),
				"PS_ACTIVE" => array("FIELD" => "PS.ACTIVE", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_pay_system PS ON (PSA.PAY_SYSTEM_ID = PS.ID)"),
				"PS_SORT" => array("FIELD" => "PS.SORT", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_pay_system PS ON (PSA.PAY_SYSTEM_ID = PS.ID)"),
				"PS_DESCRIPTION" => array("FIELD" => "PS.DESCRIPTION", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_pay_system PS ON (PSA.PAY_SYSTEM_ID = PS.ID)"),
				"PT_LID" => array("FIELD" => "PT.LID", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_person_type PT ON (PSA.PERSON_TYPE_ID = PT.ID)"),
				"PT_NAME" => array("FIELD" => "PT.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_person_type PT ON (PSA.PERSON_TYPE_ID = PT.ID)"),
				"PT_SORT" => array("FIELD" => "PT.SORT", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_person_type PT ON (PSA.PERSON_TYPE_ID = PT.ID)"),
				"PT_ACTIVE" => array("FIELD" => "PT.ACTIVE", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_person_type PT ON (PSA.PERSON_TYPE_ID = PT.ID)"),
			);
		// <-- FIELDS

		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "DISTINCT", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_sale_pay_system_action PSA ".
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
			"FROM b_sale_pay_system_action PSA ".
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
				"FROM b_sale_pay_system_action PSA ".
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

		if (!CSalePaySystemAction::CheckFields("ADD", $arFields))
			return false;

		if (array_key_exists("LOGOTIP", $arFields) && is_array($arFields["LOGOTIP"]))
			$arFields["LOGOTIP"]["MODULE_ID"] = "sale";

		CFile::SaveForDB($arFields, "LOGOTIP", "sale/paysystem/logotip");

		$arInsert = $DB->PrepareInsert("b_sale_pay_system_action", $arFields);

		$strSql =
			"INSERT INTO b_sale_pay_system_action(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$ID = IntVal($DB->LastID());

		return $ID;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		$ID = IntVal($ID);
		if (!CSalePaySystemAction::CheckFields("UPDATE", $arFields))
			return false;

		if (array_key_exists("LOGOTIP", $arFields) && is_array($arFields["LOGOTIP"]))
			$arFields["LOGOTIP"]["MODULE_ID"] = "sale";

		CFile::SaveForDB($arFields, "LOGOTIP", "sale/paysystem/logotip");

		$strUpdate = $DB->PrepareUpdate("b_sale_pay_system_action", $arFields);
		$strSql = "UPDATE b_sale_pay_system_action SET ".$strUpdate." WHERE ID = ".$ID."";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $ID;
	}
}
?>