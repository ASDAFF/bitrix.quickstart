<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/general/catalog.php");

class CCatalog extends CAllCatalog
{
	function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		// For old-style execution
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

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "I.ID", "TYPE" => "int"),
				"IBLOCK_ID" => array("FIELD" => "CI.IBLOCK_ID", "TYPE" => "int"),
				"YANDEX_EXPORT" => array("FIELD" => "CI.YANDEX_EXPORT", "TYPE" => "char"),
				"SUBSCRIPTION" => array("FIELD" => "CI.SUBSCRIPTION", "TYPE" => "char"),
				"PRODUCT_IBLOCK_ID" => array("FIELD" => "CI.PRODUCT_IBLOCK_ID", "TYPE" => "int"),
				"SKU_PROPERTY_ID" => array("FIELD" => "CI.SKU_PROPERTY_ID", "TYPE" => "int"),
				"OFFERS_PROPERTY_ID" => array("FIELD" => "OFFERS.SKU_PROPERTY_ID", "TYPE" => "int"),
				"OFFERS_IBLOCK_ID" => array("FIELD" => "OFFERS.IBLOCK_ID", "TYPE" => "int"),
				"IBLOCK_TYPE_ID" => array("FIELD" => "I.IBLOCK_TYPE_ID", "TYPE" => "string"),
				"IBLOCK_ACTIVE" => array("FIELD" => "I.ACTIVE", "TYPE" => "char"),
				"LID" => array("FIELD" => "I.LID", "TYPE" => "string"),
				"NAME" => array("FIELD" => "I.NAME", "TYPE" => "string")
			);
		// <-- FIELDS

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_catalog_iblock CI
				INNER JOIN b_iblock I ON CI.IBLOCK_ID = I.ID
				LEFT JOIN b_catalog_iblock OFFERS ON CI.IBLOCK_ID = OFFERS.PRODUCT_IBLOCK_ID".
				" ".$arSqls["FROM"]." ".
				"WHERE 1=1 ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "AND ".$arSqls["WHERE"]." ";
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
			"FROM b_catalog_iblock CI
			INNER JOIN b_iblock I ON CI.IBLOCK_ID = I.ID
			LEFT JOIN b_catalog_iblock OFFERS ON CI.IBLOCK_ID = OFFERS.PRODUCT_IBLOCK_ID".
			" ".$arSqls["FROM"]." ".
			"WHERE 1=1 ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "AND ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_catalog_iblock CI
				INNER JOIN b_iblock I ON CI.IBLOCK_ID = I.ID
				LEFT JOIN b_catalog_iblock OFFERS ON CI.IBLOCK_ID = OFFERS.PRODUCT_IBLOCK_ID".
				" ".$arSqls["FROM"]." ".
				"WHERE 1=1 ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "AND ".$arSqls["WHERE"]." ";
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