<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/general/store_product.php");

class CCatalogStoreProduct
	extends CCatalogStoreProductAll
{
	public static function Add($arFields)
	{
		global $DB;
		if (!self::CheckFields('ADD',$arFields))
			return false;

		$arInsert = $DB->PrepareInsert("b_catalog_store_product", $arFields);
		$strSql =
			"INSERT INTO b_catalog_store_product (".$arInsert[0].") ".
				"VALUES(".$arInsert[1].")";

		$res=$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());
		return $lastId;
	}

	static function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		$arFields = array(
			"ID" => array("FIELD" => "CP.ID", "TYPE" => "int"),
			"PRODUCT_ID" => array("FIELD" => "CP.PRODUCT_ID", "TYPE" => "int"),
			"STORE_ID" => array("FIELD" => "CP.STORE_ID", "TYPE" => "int"),
			"AMOUNT" => array("FIELD" => "CP.AMOUNT", "TYPE" => "double"),
			"STORE_NAME" => array("FIELD" => "CS.TITLE", "TYPE" => "string", "FROM" => "RIGHT JOIN b_catalog_store CS ON (CS.ID = CP.STORE_ID)"),
			"STORE_ADDR" => array("FIELD" => "CS.ADDRESS", "TYPE" => "string", "FROM" => "RIGHT JOIN b_catalog_store CS ON (CS.ID = CP.STORE_ID)"),
			"STORE_DESCR" => array("FIELD" => "CS.DESCRIPTION", "TYPE" => "string", "FROM" => "RIGHT JOIN b_catalog_store CS ON (CS.ID = CP.STORE_ID)"),
			"STORE_GPS_N" => array("FIELD" => "CS.GPS_N", "TYPE" => "string", "FROM" => "RIGHT JOIN b_catalog_store CS ON (CS.ID = CP.STORE_ID)"),
			"STORE_GPS_S" => array("FIELD" => "CS.GPS_S", "TYPE" => "string", "FROM" => "RIGHT JOIN b_catalog_store CS ON (CS.ID = CP.STORE_ID)"),
			"STORE_IMAGE" => array("FIELD" => "CS.IMAGE_ID", "TYPE" => "int", "FROM" => "RIGHT JOIN b_catalog_store CS ON (CS.ID = CP.STORE_ID)"),
			"STORE_LOCATION" => array("FIELD" => "CS.LOCATION_ID", "TYPE" => "int", "FROM" => "RIGHT JOIN b_catalog_store CS ON (CS.ID = CP.STORE_ID)")

		);
		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
					"FROM b_catalog_store_product CP ".
					"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return false;
		}
		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_catalog_store_product CP ".
				"	".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";


		if (is_array($arNavStartParams) && intval($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
					"FROM b_catalog_store_product CP ".
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
			if (is_array($arNavStartParams) && intval($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".intval($arNavStartParams["nTopCount"]);

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		return $dbRes;
	}
}