<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialnetwork/classes/general/group_features_perms.php");

class CSocNetFeaturesPerms extends CAllSocNetFeaturesPerms
{
	/***************************************/
	/********  DATA MODIFICATION  **********/
	/***************************************/
	function Add($arFields)
	{
		global $DB;

		$arFields1 = array();
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1) == "=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}

		if (!CSocNetFeaturesPerms::CheckFields("ADD", $arFields))
			return false;

		$db_events = GetModuleEvents("socialnetwork", "OnBeforeSocNetFeaturesPermsAdd");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($arFields))===false)
				return false;

		$arInsert = $DB->PrepareInsert("b_sonet_features2perms", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($arInsert[0]) > 0)
				$arInsert[0] .= ", ";
			$arInsert[0] .= $key;
			if (strlen($arInsert[1]) > 0)
				$arInsert[1] .= ", ";
			$arInsert[1] .= $value;
		}

		$ID = false;
		if (strlen($arInsert[0]) > 0)
		{
			$strSql =
				"INSERT INTO b_sonet_features2perms(".$arInsert[0].") ".
				"VALUES(".$arInsert[1].")";
			$DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);

			$ID = IntVal($DB->LastID());

			$events = GetModuleEvents("socialnetwork", "OnSocNetFeaturesPermsAdd");
			while ($arEvent = $events->Fetch())
				ExecuteModuleEventEx($arEvent, array($ID, $arFields));
		}

		return $ID;
	}

	
	/***************************************/
	/**********  DATA SELECTION  ***********/
	/***************************************/
	function GetList($arOrder = Array("ID" => "DESC"), $arFilter = Array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		if (count($arSelectFields) <= 0)
			$arSelectFields = array("ID", "FEATURE_ID", "OPERATION_ID", "ROLE");

		static $arFields = array(
			"ID" => Array("FIELD" => "GFP.ID", "TYPE" => "int"),
			"FEATURE_ID" => Array("FIELD" => "GFP.FEATURE_ID", "TYPE" => "int"),
			"OPERATION_ID" => Array("FIELD" => "GFP.OPERATION_ID", "TYPE" => "string"),
			"ROLE" => Array("FIELD" => "GFP.ROLE", "TYPE" => "string"),
			"FEATURE_ENTITY_TYPE" => Array("FIELD" => "GF.ENTITY_TYPE", "TYPE" => "string", "FROM" => "INNER JOIN b_sonet_features GF ON (GFP.FEATURE_ID = GF.ID)"),
			"FEATURE_ENTITY_ID" => Array("FIELD" => "GF.ENTITY_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sonet_features GF ON (GFP.FEATURE_ID = GF.ID)"),
			"FEATURE_FEATURE" => Array("FIELD" => "GF.FEATURE", "TYPE" => "string", "FROM" => "INNER JOIN b_sonet_features GF ON (GFP.FEATURE_ID = GF.ID)"),
			"FEATURE_FEATURE_NAME" => Array("FIELD" => "GF.FEATURE_NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_sonet_features GF ON (GFP.FEATURE_ID = GF.ID)"),
			"FEATURE_ACTIVE" => Array("FIELD" => "GF.ACTIVE", "TYPE" => "string", "FROM" => "INNER JOIN b_sonet_features GF ON (GFP.FEATURE_ID = GF.ID)"),
		);

		$arSqls = CSocNetGroup::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_sonet_features2perms GFP ".
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
			"FROM b_sonet_features2perms GFP ".
			"	".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"]) <= 0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_sonet_features2perms GFP ".
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
				// ������ ��� MYSQL!!! ��� ORACLE ������ ���
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			//echo "!2.2!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"]) > 0)
				$strSql .= "LIMIT ".IntVal($arNavStartParams["nTopCount"]);

			//echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}
	
	function GetAvaibleEntity($entityType, $feature, $role, $operation, $active, $visible, $siteID)
	{
		global $DB;
		
		if(Strlen($entityType) <= 0 || Strlen($role) <= 0 || Strlen($operation) <= 0)
			return false;
		if(Strlen($entityType) <= 0)
			$entityType = "G";
		if(Strlen($active) <= 0)
			$active = "Y";
		if(Strlen($visible) <= 0)
			$visible = "Y";
		if(Strlen($siteID) <= 0)
			$siteID = SITE_ID;		
			
		$strSql = "select b.ID as ID,
					b.ENTITY_TYPE as ENTITY_TYPE,
					b.ENTITY_ID as ENTITY_ID,
					b.FEATURE as FEATURE,
					b.ACTIVE as FEATURE_ACTIVE,
					p.OPERATION_ID as OPERATION_ID,
					p.ROLE as ROLE ";
		if($entityType == "G")
			$strSql .= ", g.SITE_ID as GROUP_SITE_ID,
					g.NAME as GROUP_NAME,
					g.VISIBLE as GROUP_VISIBLE,
					g.OWNER_ID as GROUP_OWNER_ID ";
		$strSql .= " from b_sonet_features b ".
					"LEFT JOIN b_sonet_features2perms p ON (b.ID = p.FEATURE_ID AND ". 
					"p.ROLE = '".$DB->ForSQL($role)."' AND p.OPERATION_ID = '".$DB->ForSQL($operation)."') ";
		if($entityType == "G")
			$strSql .= "INNER JOIN b_sonet_group g ON (g.ID = b.ENTITY_ID) ";
		$strSql .= "WHERE ".
					"b.FEATURE='".$DB->ForSQL($feature)."' AND ".
					"b.ACTIVE = '".$DB->ForSQL($active)."' AND ".
					"b.ENTITY_TYPE = '".$DB->ForSQL($entityType)."' ";

		if($entityType == "G")
			$strSql .= " AND g.ACTIVE = 'Y' AND ".
						"g.VISIBLE= 'Y' AND ". 
						"g.SITE_ID= '".$DB->ForSQL($siteID)."'";

		$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $dbRes;
	}
}
?>