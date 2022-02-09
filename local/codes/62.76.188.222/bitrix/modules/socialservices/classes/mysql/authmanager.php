<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialservices/classes/general/authmanager.php");

class CSocServAuthDB
	extends CSocServAuth
{

	static function Add($arFields)
	{
		global $DB;
		if (!self::CheckFields('ADD',$arFields))
			return false;

		$arInsert = $DB->PrepareInsert("b_socialservices_user", $arFields);
		$strSql =
			"INSERT INTO b_socialservices_user (".$arInsert[0].") ".
				"VALUES(".$arInsert[1].")";

		$res=$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
		{
			$_SESSION["LAST_ERROR"] = GetMessage("SC_ADD_ERROR");
			return false;
		}
		$lastId = intval($DB->LastID());
		return $lastId;
	}

	function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "LOGIN", "NAME", "LAST_NAME", "EMAIL", "PERSONAL_PHOTO", "EXTERNAL_AUTH_ID", "USER_ID", "XML_ID", "CAN_DELETE", "PERSONAL_WWW");

		$arFields = array(
			"ID" => array("FIELD" => "SU.ID", "TYPE" => "int"),
			"LOGIN" => array("FIELD" => "SU.LOGIN", "TYPE" => "string"),
			"NAME" => array("FIELD" => "SU.NAME", "TYPE" => "string"),
			"LAST_NAME" => array("FIELD" => "SU.LAST_NAME", "TYPE" => "string"),
			"EMAIL" => array("FIELD" => "SU.EMAIL", "TYPE" => "string"),
			"PERSONAL_PHOTO" => array("FIELD" => "SU.PERSONAL_PHOTO", "TYPE" => "int"),
			"EXTERNAL_AUTH_ID" => array("FIELD" => "SU.EXTERNAL_AUTH_ID", "TYPE" => "string"),
			"USER_ID" => array("FIELD" => "SU.USER_ID", "TYPE" => "int"),
			"XML_ID" => array("FIELD" => "SU.XML_ID", "TYPE" => "string"),
			"CAN_DELETE" => array("FIELD" => "SU.CAN_DELETE", "TYPE" => "char"),
			"PERSONAL_WWW" => array("FIELD" => "SU.PERSONAL_WWW", "TYPE" => "string"),
		);
		$arSqls = CGroup::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
					"FROM b_socialservices_user SU ".
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
				"FROM b_socialservices_user SU ".
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
					"FROM b_socialservices_user SU ".
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