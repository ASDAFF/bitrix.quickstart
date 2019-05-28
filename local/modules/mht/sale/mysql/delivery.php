<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/delivery.php");

use Bitrix\Sale\Location\Admin\LocationHelper as Helper;

class CSaleDelivery extends CAllSaleDelivery
{
	function PrepareCurrency4Where($val, $key, $operation, $negative, $field, &$arField, &$arFilter)
	{
		$val = DoubleVal($val);

		$baseSiteCurrency = "";
		if (isset($arFilter["LID"]) && strlen($arFilter["LID"]) > 0)
			$baseSiteCurrency = CSaleLang::GetLangCurrency($arFilter["LID"]);
		elseif (isset($arFilter["CURRENCY"]) && strlen($arFilter["CURRENCY"]) > 0)
			$baseSiteCurrency = $arFilter["CURRENCY"];

		if (strlen($baseSiteCurrency) <= 0)
			return False;

		$strSqlSearch = "";

		$dbCurrency = CCurrency::GetList(($by = "sort"), ($order = "asc"));
		while ($arCurrency = $dbCurrency->Fetch())
		{
			$val1 = roundEx(CCurrencyRates::ConvertCurrency($val, $baseSiteCurrency, $arCurrency["CURRENCY"]), SALE_VALUE_PRECISION);
			if (strlen($strSqlSearch) > 0)
				$strSqlSearch .= " OR ";

			$strSqlSearch .= "(D.ORDER_CURRENCY = '".$arCurrency["CURRENCY"]."' AND ";
			if ($negative == "Y")
				$strSqlSearch .= "NOT";
			$strSqlSearch .= "(".$field." ".$operation." ".$val1." OR ".$field." IS NULL OR ".$field." = 0)";
			$strSqlSearch .= ")";
		}

		return "(".$strSqlSearch.")";
	}

	function PrepareLocation4Where($val, $key, $operation, $negative, $field, &$arField, &$arFilter)
	{
		return "(D2L.LOCATION_CODE = ".IntVal($val)." AND D2L.LOCATION_TYPE = 'L' ".
			" OR L2LG.LOCATION_ID = ".IntVal($val)." AND D2L.LOCATION_TYPE = 'G') ";
	}

	// If the money is given by the filter, then the filter is mandatory LID!
	function GetList($arOrder = array("SORT" => "ASC", "NAME" => "ASC"), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		if (isset($arFilter["WEIGHT"]) && DoubleVal($arFilter["WEIGHT"]) > 0)
		{
			// changed by Sigurd, 2007-08-16
			if (!isset($arFilter["WEIGHT_FROM"]) || DoubleVal($arFilter["WEIGHT"]) > DoubleVal($arFilter["WEIGHT_FROM"]))
				$arFilter["+<=WEIGHT_FROM"] = $arFilter["WEIGHT"];
			if (!isset($arFilter["WEIGHT_TO"]) || DoubleVal($arFilter["WEIGHT"]) < DoubleVal($arFilter["WEIGHT_TO"]))
				$arFilter["+>=WEIGHT_TO"] = $arFilter["WEIGHT"];
		}

		if (isset($arFilter["ORDER_PRICE"]) && IntVal($arFilter["ORDER_PRICE"]) > 0)
		{
			if (!isset($arFilter["ORDER_PRICE_FROM"]) || IntVal($arFilter["ORDER_PRICE"]) > IntVal($arFilter["ORDER_PRICE_FROM"]))
				$arFilter["+<=ORDER_PRICE_FROM"] = $arFilter["ORDER_PRICE"];
			if (!isset($arFilter["ORDER_PRICE_TO"]) || IntVal($arFilter["ORDER_PRICE"]) < IntVal($arFilter["ORDER_PRICE_TO"]))
				$arFilter["+>=ORDER_PRICE_TO"] = $arFilter["ORDER_PRICE"];
		}

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "NAME", "LID", "PERIOD_FROM", "PERIOD_TO", "PERIOD_TYPE", "WEIGHT_FROM", "WEIGHT_TO", "ORDER_PRICE_FROM", "ORDER_PRICE_TO", "ORDER_CURRENCY", "ACTIVE", "PRICE", "CURRENCY", "SORT", "DESCRIPTION", "LOGOTIP", "STORE");

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "D.ID", "TYPE" => "int"),
				"NAME" => array("FIELD" => "D.NAME", "TYPE" => "string"),
				"LID" => array("FIELD" => "D.LID", "TYPE" => "string"),
				"PERIOD_FROM" => array("FIELD" => "D.PERIOD_FROM", "TYPE" => "int"),
				"PERIOD_TO" => array("FIELD" => "D.PERIOD_TO", "TYPE" => "int"),
				"PERIOD_TYPE" => array("FIELD" => "D.PERIOD_TYPE", "TYPE" => "char"),
				"WEIGHT_FROM" => array("FIELD" => "D.WEIGHT_FROM", "TYPE" => "double"),
				"WEIGHT_TO" => array("FIELD" => "D.WEIGHT_TO", "TYPE" => "double"),
				"ORDER_PRICE_FROM" => array("FIELD" => "D.ORDER_PRICE_FROM", "TYPE" => "double", "WHERE" => array("CSaleDelivery", "PrepareCurrency4Where")),
				"ORDER_PRICE_TO" => array("FIELD" => "D.ORDER_PRICE_TO", "TYPE" => "double", "WHERE" => array("CSaleDelivery", "PrepareCurrency4Where")),
				"ORDER_CURRENCY" => array("FIELD" => "D.ORDER_CURRENCY", "TYPE" => "string"),
				"ACTIVE" => array("FIELD" => "D.ACTIVE", "TYPE" => "char"),
				"PRICE" => array("FIELD" => "D.PRICE", "TYPE" => "double"),
				"CURRENCY" => array("FIELD" => "D.CURRENCY", "TYPE" => "string"),
				"SORT" => array("FIELD" => "D.SORT", "TYPE" => "int"),
				"DESCRIPTION" => array("FIELD" => "D.DESCRIPTION", "TYPE" => "string"),
				"LOGOTIP" => array("FIELD" => "D.LOGOTIP", "TYPE" => "int"),
				"STORE" => array("FIELD" => "D.STORE", "TYPE" => "string"),
			);

		if(CSaleLocation::isLocationProMigrated())
			$arFields['LOCATION'] = array("FIELD" => "D.ID", "TYPE" => "int", "WHERE" => array("CSaleDelivery", "PrepareLocation24Where"));
		else
			$arFields['LOCATION'] = array("FIELD" => "D.DESCRIPTION", "WHERE_ONLY" => "Y", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_delivery2location D2L ON (D.ID = D2L.DELIVERY_ID) LEFT JOIN b_sale_location2location_group L2LG ON (D2L.LOCATION_TYPE = 'G' AND D2L.LOCATION_CODE = L2LG.LOCATION_GROUP_ID)", "WHERE" => array("CSaleDelivery", "PrepareLocation4Where"));

		// <-- FIELDS

		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "DISTINCT", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_sale_delivery D ".
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
			"FROM b_sale_delivery D ".
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
				"FROM b_sale_delivery D ".
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

	function Add($arFields, $arOptions = array())
	{
		global $DB;

		if (!CSaleDelivery::CheckFields("ADD", $arFields))
			return false;

		if (array_key_exists("LOGOTIP", $arFields) && is_array($arFields["LOGOTIP"]))
			$arFields["LOGOTIP"]["MODULE_ID"] = "sale";

		CFile::SaveForDB($arFields, "LOGOTIP", "sale/delivery/logotip");

		$arInsert = $DB->PrepareInsert("b_sale_delivery", $arFields);

		$strSql =
			"INSERT INTO b_sale_delivery(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$ID = IntVal($DB->LastID());

		if(CSaleLocation::isLocationProMigrated())
		{
			Helper::resetLocationsForEntity($ID, $arFields['LOCATIONS'], self::CONN_ENTITY_NAME, !!$arOptions['EXPECT_LOCATION_CODES']);
		}
		else
		{
			foreach($arFields["LOCATIONS"] as $location)
			{
				// change location id to location code
				$location['LOCATION_CODE'] = $location['LOCATION_ID'];
				unset($location['LOCATION_ID']);

				$arInsert = $DB->PrepareInsert("b_sale_delivery2location", $location);

				$strSql =
					"INSERT INTO b_sale_delivery2location(DELIVERY_ID, ".$arInsert[0].") ".
					"VALUES(".$ID.", ".$arInsert[1].")";
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
		}

		if (is_set($arFields, "PAY_SYSTEM"))
		{
			CSaleDelivery::UpdateDeliveryPay($ID, $arFields["PAY_SYSTEM"]);
		}
		
		return $ID;
	}
}
?>