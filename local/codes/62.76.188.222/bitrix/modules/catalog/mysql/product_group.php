<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/general/product_group.php");

/***********************************************************************/
/***********  CCatalogProductGroups  ***********************************/
/***********************************************************************/
class CCatalogProductGroups extends CAllCatalogProductGroups
{
	function Add($arFields)
	{
		global $DB;

		if (!CCatalogProductGroups::CheckFields("ADD", $arFields, 0))
			return False;

		$arInsert = $DB->PrepareInsert("b_catalog_product2group", $arFields);

		$strSql =
			"INSERT INTO b_catalog_product2group(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$ID = intval($DB->LastID());

		return $ID;
	}

	function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		$arFields = array(
				"ID" => array("FIELD" => "CPG.ID", "TYPE" => "int"),
				"PRODUCT_ID" => array("FIELD" => "CPG.PRODUCT_ID", "TYPE" => "int"),
				"GROUP_ID" => array("FIELD" => "CPG.GROUP_ID", "TYPE" => "int"),
				"ACCESS_LENGTH" => array("FIELD" => "CPG.ACCESS_LENGTH", "TYPE" => "int"),
				"ACCESS_LENGTH_TYPE" => array("FIELD" => "CPG.ACCESS_LENGTH_TYPE", "TYPE" => "char"),
				"GROUP_ACTIVE" => array("FIELD" => "G.ACTIVE", "TYPE" => "char", "FROM" => "INNER JOIN b_group G ON (CPG.GROUP_ID = G.ID)"),
				"GROUP_NAME" => array("FIELD" => "G.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_group G ON (CPG.GROUP_ID = G.ID)")
			);

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && empty($arGroupBy))
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_catalog_product2group CPG ".
				"	".$arSqls["FROM"]." ";
			if (!empty($arSqls["WHERE"]))
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (!empty($arSqls["GROUPBY"]))
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_catalog_product2group CPG ".
			"	".$arSqls["FROM"]." ";
		if (!empty($arSqls["WHERE"]))
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (!empty($arSqls["GROUPBY"]))
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (!empty($arSqls["ORDERBY"]))
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && intval($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_catalog_product2group CPG ".
				"	".$arSqls["FROM"]." ";
			if (!empty($arSqls["WHERE"]))
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (!empty($arSqls["GROUPBY"]))
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (empty($arSqls["GROUPBY"]))
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
?>