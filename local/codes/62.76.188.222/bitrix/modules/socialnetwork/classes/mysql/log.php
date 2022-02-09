<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialnetwork/classes/general/log.php");

class CSocNetLog extends CAllSocNetLog
{
	/***************************************/
	/********  DATA MODIFICATION  **********/
	/***************************************/
	function Add($arFields, $bSendEvent = true)
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

		if (!CSocNetLog::CheckFields("ADD", $arFields))
			return false;
		else
		{
			$arSiteID = array();
			if(array_key_exists("SITE_ID", $arFields))
			{
				if(is_array($arFields["SITE_ID"]))
					foreach($arFields["SITE_ID"] as $site_id)
						$arSiteID[$site_id] = $DB->ForSQL($site_id);
				else
					$arSiteID[$arFields["SITE_ID"]] = $DB->ForSQL($arFields["SITE_ID"]);
			}
		}

		if(empty($arSiteID))
			unset($arFields["SITE_ID"]);
		else
			$arFields["SITE_ID"] = end($arSiteID);

		unset($arFields["LOG_UPDATE"]);
		$arFields["~LOG_UPDATE"] = $DB->CurrentTimeFunction();

		$arInsert = $DB->PrepareInsert("b_sonet_log", $arFields);

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
				"INSERT INTO b_sonet_log(".$arInsert[0].") ".
				"VALUES(".$arInsert[1].")";
			$DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);

			$ID = IntVal($DB->LastID());

			if ($ID > 0 && $bSendEvent)
				CSocNetLog::SendEvent($ID, "SONET_NEW_EVENT");

			if ($ID > 0 && !empty($arSiteID))
			{
					$DB->Query("
						DELETE FROM b_sonet_log_site WHERE LOG_ID = ".$ID."
					", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

					$DB->Query("
						INSERT INTO b_sonet_log_site(LOG_ID, SITE_ID)
						SELECT ".$ID.", LID
						FROM b_lang
						WHERE LID IN ('".implode("', '", $arSiteID)."')
					", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			}
		}

		CSocNetLogTools::SetCacheLastLogID("log", $ID);

		return $ID;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_L_WRONG_PARAMETER_ID"), "ERROR_NO_ID");
			return false;
		}

		$arFields1 = array();
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1) == "=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}

		if (!CSocNetLog::CheckFields("UPDATE", $arFields, $ID))
			return false;
		else
		{
			$arSiteID = Array();
			if(is_set($arFields, "SITE_ID"))
			{
				if(is_array($arFields["SITE_ID"]))
					$arSiteID = $arFields["SITE_ID"];
				else
					$arSiteID[] = $arFields["SITE_ID"];

				$arFields["SITE_ID"] = false;
				$str_SiteID = "''";
				foreach($arSiteID as $v)
				{
					$arFields["SITE_ID"] = $v;
					$str_SiteID .= ", '".$DB->ForSql($v)."'";
				}
			}
		}

		$strUpdate = $DB->PrepareUpdate("b_sonet_log", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($strUpdate) > 0)
				$strUpdate .= ", ";
			$strUpdate .= $key."=".$value." ";
		}

		if (strlen($strUpdate) > 0)
		{
			$strSql =
				"UPDATE b_sonet_log SET ".
				"	".$strUpdate." ".
				"WHERE ID = ".$ID." ";
			$DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);

			if(count($arSiteID)>0)
			{
				$strSql = "DELETE FROM b_sonet_log_site WHERE LOG_ID=".$ID;
				$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

				$strSql =
					"INSERT INTO b_sonet_log_site(LOG_ID, SITE_ID) ".
					"SELECT ".$ID.", LID ".
					"FROM b_lang ".
					"WHERE LID IN (".$str_SiteID.") ";
				$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			}
		}
		else
			$ID = False;

		return $ID;
	}

	function ClearOld($days = 90)
	{
		global $DB;

		$days = IntVal($days);
		if ($days <= 0)
			return true;

		$DB->Query("DELETE LC FROM b_sonet_log_comment LC INNER JOIN (SELECT L.TMP_ID FROM b_sonet_log L LEFT JOIN b_sonet_log_favorites LF ON L.ID = LF.LOG_ID WHERE LF.USER_ID IS NULL AND L.LOG_UPDATE < DATE_SUB(NOW(), INTERVAL ".$days." DAY)) L1 ON LC.LOG_ID = L1.TMP_ID", true);
		$DB->Query("DELETE LS FROM b_sonet_log_site LS INNER JOIN (SELECT L.ID FROM b_sonet_log L LEFT JOIN b_sonet_log_favorites LF ON L.ID = LF.LOG_ID WHERE LF.USER_ID IS NULL AND L.LOG_UPDATE < DATE_SUB(NOW(), INTERVAL ".$days." DAY)) L1 ON LS.LOG_ID = L1.ID", true);
		$DB->Query("DELETE LR FROM b_sonet_log_right LR INNER JOIN (SELECT L.ID FROM b_sonet_log L LEFT JOIN b_sonet_log_favorites LF ON L.ID = LF.LOG_ID WHERE LF.USER_ID IS NULL AND L.LOG_UPDATE < DATE_SUB(NOW(), INTERVAL ".$days." DAY)) L1 ON LR.LOG_ID = L1.ID", true);

		return $DB->Query("DELETE FROM b_sonet_log L LEFT JOIN b_sonet_log_favorites LF ON L.ID = LF.LOG_ID WHERE LF.USER_ID IS NULL AND L.LOG_UPDATE < DATE_SUB(NOW(), INTERVAL ".$days." DAY)", true);
	}

	/***************************************/
	/**********  DATA SELECTION  ***********/
	/***************************************/
	function GetList($arOrder = Array("ID" => "DESC"), $arFilter = Array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arParams = array())
	{
		global $DB, $arSocNetAllowedEntityTypes, $USER;

		if (count($arSelectFields) <= 0)
			$arSelectFields = array(
				"ID", "TMP_ID", "ENTITY_TYPE", "ENTITY_ID", "USER_ID", "EVENT_ID", "LOG_DATE", "LOG_UPDATE", "TITLE_TEMPLATE", "TITLE", "MESSAGE", "TEXT_MESSAGE", "URL", "MODULE_ID", "CALLBACK_FUNC", "EXTERNAL_ID", "SITE_ID", "PARAMS",
				"COMMENTS_COUNT", "ENABLE_COMMENTS", "SOURCE_ID",
				"GROUP_NAME", "GROUP_OWNER_ID", "GROUP_INITIATE_PERMS", "GROUP_VISIBLE", "GROUP_OPENED", "GROUP_IMAGE_ID",
				"USER_NAME", "USER_LAST_NAME", "USER_SECOND_NAME", "USER_LOGIN", "USER_PERSONAL_PHOTO", "USER_PERSONAL_GENDER",
				"CREATED_BY_NAME", "CREATED_BY_LAST_NAME", "CREATED_BY_SECOND_NAME", "CREATED_BY_LOGIN", "CREATED_BY_PERSONAL_PHOTO", "CREATED_BY_PERSONAL_GENDER",
				"RATING_TYPE_ID", "RATING_ENTITY_ID", "RATING_TOTAL_VALUE", "RATING_TOTAL_VOTES", "RATING_TOTAL_POSITIVE_VOTES", "RATING_TOTAL_NEGATIVE_VOTES", "RATING_USER_VOTE_VALUE",
				"FAVORITES_USER_ID"
			);

		static $arFields1 = array(
			"ID" => Array("FIELD" => "L.ID", "TYPE" => "int"),
			"TMP_ID" => Array("FIELD" => "L.TMP_ID", "TYPE" => "int"),
			"SOURCE_ID" => Array("FIELD" => "L.SOURCE_ID", "TYPE" => "int"),
			"ENTITY_TYPE" => Array("FIELD" => "L.ENTITY_TYPE", "TYPE" => "string"),
			"ENTITY_ID" => Array("FIELD" => "L.ENTITY_ID", "TYPE" => "int"),
			"USER_ID" => Array("FIELD" => "L.USER_ID", "TYPE" => "int"),
			"EVENT_ID" => Array("FIELD" => "L.EVENT_ID", "TYPE" => "string"),
			"LOG_DATE" => Array("FIELD" => "L.LOG_DATE", "TYPE" => "datetime"),
			"LOG_UPDATE" => Array("FIELD" => "L.LOG_UPDATE", "TYPE" => "datetime"),
			"TITLE_TEMPLATE" => Array("FIELD" => "L.TITLE_TEMPLATE", "TYPE" => "string"),
			"TITLE" => Array("FIELD" => "L.TITLE", "TYPE" => "string"),
			"MESSAGE" => Array("FIELD" => "L.MESSAGE", "TYPE" => "string"),
			"TEXT_MESSAGE" => Array("FIELD" => "L.TEXT_MESSAGE", "TYPE" => "string"),
			"URL" => Array("FIELD" => "L.URL", "TYPE" => "string"),
			"MODULE_ID" => Array("FIELD" => "L.MODULE_ID", "TYPE" => "string"),
			"CALLBACK_FUNC" => Array("FIELD" => "L.CALLBACK_FUNC", "TYPE" => "string"),
			"EXTERNAL_ID" => Array("FIELD" => "L.EXTERNAL_ID", "TYPE" => "string"),
			"PARAMS" => Array("FIELD" => "L.PARAMS", "TYPE" => "string"),
			"COMMENTS_COUNT" => Array("FIELD" => "L.COMMENTS_COUNT", "TYPE" => "int"),
			"ENABLE_COMMENTS" => Array("FIELD" => "L.ENABLE_COMMENTS", "TYPE" => "string"),
			"GROUP_NAME" => Array("FIELD" => "G.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_sonet_group G ON (L.ENTITY_TYPE = 'G' AND L.ENTITY_ID = G.ID)"),
			"GROUP_OWNER_ID" => Array("FIELD" => "G.OWNER_ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_sonet_group G ON (L.ENTITY_TYPE = 'G' AND L.ENTITY_ID = G.ID)"),
			"GROUP_INITIATE_PERMS" => Array("FIELD" => "G.INITIATE_PERMS", "TYPE" => "string", "FROM" => "LEFT JOIN b_sonet_group G ON (L.ENTITY_TYPE = 'G' AND L.ENTITY_ID = G.ID)"),
			"GROUP_VISIBLE" => Array("FIELD" => "G.VISIBLE", "TYPE" => "string", "FROM" => "LEFT JOIN b_sonet_group G ON (L.ENTITY_TYPE = 'G' AND L.ENTITY_ID = G.ID)"),
			"GROUP_OPENED" => Array("FIELD" => "G.OPENED", "TYPE" => "string", "FROM" => "LEFT JOIN b_sonet_group G ON (L.ENTITY_TYPE = 'G' AND L.ENTITY_ID = G.ID)"),
			"GROUP_IMAGE_ID" => Array("FIELD" => "G.IMAGE_ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_sonet_group G ON (L.ENTITY_TYPE = 'G' AND L.ENTITY_ID = G.ID)"),
			"USER_NAME" => Array("FIELD" => "U.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (L.ENTITY_TYPE = 'U' AND L.ENTITY_ID = U.ID)"),
			"USER_LAST_NAME" => Array("FIELD" => "U.LAST_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (L.ENTITY_TYPE = 'U' AND L.ENTITY_ID = U.ID)"),
			"USER_SECOND_NAME" => Array("FIELD" => "U.SECOND_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (L.ENTITY_TYPE = 'U' AND L.ENTITY_ID = U.ID)"),
			"USER_LOGIN" => Array("FIELD" => "U.LOGIN", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (L.ENTITY_TYPE = 'U' AND L.ENTITY_ID = U.ID)"),
			"USER_PERSONAL_PHOTO" => Array("FIELD" => "U.PERSONAL_PHOTO", "TYPE" => "int", "FROM" => "LEFT JOIN b_user U ON (L.ENTITY_TYPE = 'U' AND L.ENTITY_ID = U.ID)"),
			"USER_PERSONAL_GENDER" => Array("FIELD" => "U.PERSONAL_GENDER", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (L.ENTITY_TYPE = 'U' AND L.ENTITY_ID = U.ID)"),
			"CREATED_BY_NAME" => Array("FIELD" => "U1.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U1 ON L.USER_ID = U1.ID"),
			"CREATED_BY_LAST_NAME" => Array("FIELD" => "U1.LAST_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U1 ON L.USER_ID = U1.ID"),
			"CREATED_BY_SECOND_NAME" => Array("FIELD" => "U1.SECOND_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U1 ON L.USER_ID = U1.ID"),
			"CREATED_BY_LOGIN" => Array("FIELD" => "U1.LOGIN", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U1 ON L.USER_ID = U1.ID"),
			"CREATED_BY_PERSONAL_PHOTO" => Array("FIELD" => "U1.PERSONAL_PHOTO", "TYPE" => "int", "FROM" => "LEFT JOIN b_user U1 ON L.USER_ID = U1.ID"),
			"CREATED_BY_PERSONAL_GENDER" => Array("FIELD" => "U1.PERSONAL_GENDER", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U1 ON L.USER_ID = U1.ID"),
		);

		$arFields = array(
			"RATING_TYPE_ID" => Array("FIELD" => "L.RATING_TYPE_ID", "TYPE" => "string"),
			"RATING_ENTITY_ID" => Array("FIELD" => "L.RATING_ENTITY_ID", "TYPE" => "int"),
			"RATING_TOTAL_VALUE" => Array("FIELD" => $DB->IsNull('RG.TOTAL_VALUE', '0'), "TYPE" => "double", "FROM" => "LEFT JOIN b_rating_voting RG ON L.RATING_TYPE_ID = RG.ENTITY_TYPE_ID AND L.RATING_ENTITY_ID = RG.ENTITY_ID"),
			"RATING_TOTAL_VOTES" => Array("FIELD" => $DB->IsNull('RG.TOTAL_VOTES', '0'), "TYPE" => "double", "FROM" => "LEFT JOIN b_rating_voting RG ON L.RATING_TYPE_ID = RG.ENTITY_TYPE_ID AND L.RATING_ENTITY_ID = RG.ENTITY_ID"),
			"RATING_TOTAL_POSITIVE_VOTES" => Array("FIELD" => $DB->IsNull('RG.TOTAL_POSITIVE_VOTES', '0'), "TYPE" => "int", "FROM" => "LEFT JOIN b_rating_voting RG ON L.RATING_TYPE_ID = RG.ENTITY_TYPE_ID AND L.RATING_ENTITY_ID = RG.ENTITY_ID"),
			"RATING_TOTAL_NEGATIVE_VOTES" => Array("FIELD" => $DB->IsNull('RG.TOTAL_NEGATIVE_VOTES', '0'), "TYPE" => "int", "FROM" => "LEFT JOIN b_rating_voting RG ON L.RATING_TYPE_ID = RG.ENTITY_TYPE_ID AND L.RATING_ENTITY_ID = RG.ENTITY_ID"),
		);
		if (isset($USER) && is_object($USER))
		{
			$arFields["RATING_USER_VOTE_VALUE"] = Array("FIELD" => $DB->IsNull('RV.VALUE', '0'), "TYPE" => "double", "FROM" => "LEFT JOIN b_rating_vote RV ON L.RATING_TYPE_ID = RV.ENTITY_TYPE_ID AND L.RATING_ENTITY_ID = RV.ENTITY_ID AND RV.USER_ID = ".intval($USER->GetId()));
			$arFields["FAVORITES_USER_ID"] = Array("FIELD" => $DB->IsNull('SLF.USER_ID', '0'), "TYPE" => "double", "FROM" => "LEFT JOIN b_sonet_log_favorites SLF ON L.ID = SLF.LOG_ID AND SLF.USER_ID = ".intval($USER->GetID()));
		}

		if (array_key_exists("SITE_ID", $arFilter))
		{
			$arFields["SITE_ID"] = Array("FIELD" => "SLS.SITE_ID", "TYPE" => "string", "FROM" => "LEFT JOIN b_sonet_log_site SLS ON L.ID = SLS.LOG_ID");
			$strDistinct = " DISTINCT ";
			foreach ($arSelectFields as $i => $strFieldTmp)
				if ($strFieldTmp == "SITE_ID")
					unset($arSelectFields[$i]);

			foreach ($arOrder as $by => $order)
				if (!in_array($by, $arSelectFields))
					$arSelectFields[] = $by;
		}
		else
		{
			$arFields["SITE_ID"] = Array("FIELD" => "L.SITE_ID", "TYPE" => "string");
			$strDistinct = " ";
		}

		if (
			array_key_exists("USER_ID", $arFilter)
			&& !array_key_exists("ENTITY_TYPE", $arFilter)
		)
		{
			$arCBFilterEntityType = array();
			foreach($GLOBALS["arSocNetAllowedSubscribeEntityTypesDesc"] as $entity_type_tmp => $arEntityTypeTmp)
				if (
					array_key_exists("USE_CB_FILTER", $arEntityTypeTmp)
					&& $arEntityTypeTmp["USE_CB_FILTER"] == "Y"
				)
					$arCBFilterEntityType[] = $entity_type_tmp;

			if (is_array($arCBFilterEntityType) && count($arCBFilterEntityType) > 0)
				$arFilter["ENTITY_TYPE"] = $arCBFilterEntityType;
		}

		if (array_key_exists("LOG_RIGHTS", $arFilter))
		{
			$Rights = array();
			if(is_array($arFilter["LOG_RIGHTS"]))
			{
				foreach($arFilter["LOG_RIGHTS"] as $str)
					if(trim($str))
						$Rights[] = trim($str);
			}
			elseif(trim($arFilter["LOG_RIGHTS"]))
				$Rights = trim($arFilter["LOG_RIGHTS"]);

			unset($arFilter["LOG_RIGHTS"]);
			if((is_array($Rights) && !empty($Rights)) || !is_array($Rights))
			{
				$arFilter["LOG_RIGHTS"] = $Rights;
				$arFields["LOG_RIGHTS"] = Array("FIELD" => "SLR0.GROUP_CODE", "TYPE" => "string", "FROM" => "INNER JOIN b_sonet_log_right SLR0 ON L.ID = SLR0.LOG_ID");
			}

			if(is_array($Rights) && count($Rights) > 1)
				$strDistinct = " DISTINCT ";
		}

		$arFields = array_merge($arFields1, $arFields);

		$arSqls = CSocNetGroup::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["RIGHTS"] = "";

		if (
			!empty($arParams)
			&& array_key_exists("CHECK_RIGHTS", $arParams)
			&& !array_key_exists("USER_ID", $arParams)
		)
			$arParams["USER_ID"] = $GLOBALS["USER"]->GetID();

		if (
			!empty($arParams)
			&& array_key_exists("USER_ID", $arParams)
		)
			$arParams["CHECK_RIGHTS"] = "Y";

		if (
			!empty($arParams)
			&& array_key_exists("USE_SUBSCRIBE", $arParams)
			&& $arParams["USE_SUBSCRIBE"] == "Y"
		)
		{
			if (!array_key_exists("SUBSCRIBE_USER_ID", $arParams))
			{
				if (
					array_key_exists("USER_ID", $arParams)
					&& intval($arParams["USER_ID"]) > 0
				)
					$arParams["SUBSCRIBE_USER_ID"] = $arParams["USER_ID"];
				else
					$arParams["SUBSCRIBE_USER_ID"] = $GLOBALS["USER"]->GetID();
			}

			if (!array_key_exists("MY_ENTITIES", $arParams))
			{
				foreach($GLOBALS["arSocNetAllowedSubscribeEntityTypesDesc"] as $entity_type_tmp => $arEntityTypeTmp)
					if (
						array_key_exists("HAS_MY", $arEntityTypeTmp)
						&& $arEntityTypeTmp["HAS_MY"] == "Y"
						&& array_key_exists("CLASS_MY", $arEntityTypeTmp)
						&& array_key_exists("METHOD_MY", $arEntityTypeTmp)
						&& strlen($arEntityTypeTmp["CLASS_MY"]) > 0
						&& strlen($arEntityTypeTmp["METHOD_MY"]) > 0
						&& method_exists($arEntityTypeTmp["CLASS_MY"], $arEntityTypeTmp["METHOD_MY"])
					)
						$arMyEntities[$entity_type_tmp] = call_user_func(array($arEntityTypeTmp["CLASS_MY"], $arEntityTypeTmp["METHOD_MY"]));

				$arParams["MY_ENTITIES"] = $arMyEntities;
			}
		}

		if (
			!empty($arParams)
			&& array_key_exists("CHECK_RIGHTS", $arParams)
			&& $arParams["CHECK_RIGHTS"] == "Y"
			&& array_key_exists("USER_ID", $arParams)
		)
		{
			$acc = new CAccess;
			$acc->UpdateCodes();

			$arSqls["RIGHTS"] = "EXISTS ( SELECT SLR.ID FROM b_sonet_log_right SLR
				LEFT JOIN b_user_access UA ON (UA.ACCESS_CODE = SLR.GROUP_CODE AND UA.USER_ID = ".(is_object($USER)? intval($USER->GetID()): 0).")
				WHERE L.ID = SLR.LOG_ID AND (0=1 ".
				(is_object($USER) && CSocNetUser::IsCurrentUserModuleAdmin() ? " OR SLR.GROUP_CODE = 'SA'" : "").
				(is_object($USER) && $USER->IsAuthorized() ? " OR (SLR.GROUP_CODE = 'AU')" : "").
				" OR (SLR.GROUP_CODE = 'G2')".
				(is_object($USER) && $USER->IsAuthorized() ? " OR (UA.ACCESS_CODE = SLR.GROUP_CODE AND UA.USER_ID = ".$USER->GetID().")" : "")."))";
		}

		if (
			$arParams["USE_SUBSCRIBE"] == "Y"
			&& intval($arParams["SUBSCRIBE_USER_ID"]) > 0
		)
		{
			$arSqls["SUBSCRIBE"] = CSocNetLogEvents::GetSQL(
				$arParams["SUBSCRIBE_USER_ID"],
				(is_array($arParams["MY_ENTITIES"]) ? $arParams["MY_ENTITIES"] : array()),
				$arParams["TRANSPORT"],
				$arParams["VISIBLE"]
			);
			$arParams["MIN_ID_JOIN"] = true;
		}

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", $strDistinct, $arSqls["SELECT"]);
		$strMinIDJoin = "";

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_sonet_log L ".
				$strMinIDJoin.
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["RIGHTS"]) > 0)
			{
				if (strlen($arSqls["WHERE"]) > 0)
					$strSql .= " AND ";
				else
					$strSql .= " WHERE ";
				$strSql .= $arSqls["RIGHTS"]." ";
			}
			if (strlen($arSqls["SUBSCRIBE"]) > 0)
			{
				if (strlen($arSqls["WHERE"]) > 0)
					$strSql .= " AND ";
				else
					$strSql .= " WHERE ";
				$strSql .= "(".$arSqls["SUBSCRIBE"].") ";
			}
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
			"FROM b_sonet_log L ".
			$strMinIDJoin.
			"       ".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["RIGHTS"]) > 0)
		{
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= " AND ";
			else
				$strSql .= " WHERE ";
			$strSql .= $arSqls["RIGHTS"]." ";
		}
		if (strlen($arSqls["SUBSCRIBE"]) > 0)
		{
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= " AND ";
			else
				$strSql .= " WHERE ";
			$strSql .= "(".$arSqls["SUBSCRIBE"].") ";
		}
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"]) <= 0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_sonet_log L ".
				$strMinIDJoin.
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["RIGHTS"]) > 0)
			{
				if (strlen($arSqls["WHERE"]) > 0)
					$strSql_tmp .= " AND ";
				else
					$strSql_tmp .= " WHERE ";
				$strSql_tmp .= $arSqls["RIGHTS"]." ";
			}
			if (strlen($arSqls["SUBSCRIBE"]) > 0)
			{
				if (strlen($arSqls["WHERE"]) > 0)
					$strSql_tmp .= " AND ";
				else
					$strSql_tmp .= " WHERE ";
				$strSql_tmp .= "(".$arSqls["SUBSCRIBE"].") ";
			}
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

			// for empty 2nd page show
			if ($arNavStartParams["bSkipPageReset"] && $arNavStartParams["nPageSize"] >= $cnt)
				$cnt = $arNavStartParams["nPageSize"] + $cnt;

			$dbRes = new CDBResult();

			//echo "!2.2!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"]) > 0)
				$strSql .= "LIMIT ".intval($arNavStartParams["nTopCount"]);

			//echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}

	function DeleteSystemEventsByGroupID($group_id = false)
	{
		global $DB;

		if (intval($group_id) <= 0)
			return false;

		$DB->Query("DELETE LC FROM b_sonet_log_comment LC INNER JOIN (SELECT L.TMP_ID FROM b_sonet_log L WHERE L.ENTITY_TYPE = '".SONET_ENTITY_USER."' AND EVENT_ID = 'system_groups' AND MESSAGE = '".$ID."') L1 ON LC.LOG_ID = L1.TMP_ID", true);
		$DB->Query("DELETE LS FROM b_sonet_log_site LS INNER JOIN (SELECT L.ID FROM b_sonet_log L WHERE L.ENTITY_TYPE = '".SONET_ENTITY_USER."' AND EVENT_ID = 'system_groups' AND MESSAGE = '".$ID."') L1 ON LS.LOG_ID = L1.ID", true);
		$DB->Query("DELETE LR FROM b_sonet_log_right LR INNER JOIN (SELECT L.ID FROM b_sonet_log L WHERE L.ENTITY_TYPE = '".SONET_ENTITY_USER."' AND EVENT_ID = 'system_groups' AND MESSAGE = '".$ID."') L1 ON LR.LOG_ID = L1.ID", true);
		$DB->Query("DELETE LF FROM b_sonet_log_favorites LF INNER JOIN (SELECT L.ID FROM b_sonet_log L WHERE L.ENTITY_TYPE = '".SONET_ENTITY_USER."' AND EVENT_ID = 'system_groups' AND MESSAGE = '".$ID."') L1 ON LF.LOG_ID = L1.ID", true);

		return $DB->Query("DELETE FROM b_sonet_log WHERE ENTITY_TYPE = '".SONET_ENTITY_USER."' AND EVENT_ID = 'system_groups' AND MESSAGE = '".$ID."'", true);
	}

	function Delete($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GL_WRONG_PARAMETER_ID"), "ERROR_NO_ID");
			return false;
		}

		$DB->Query("DELETE LC FROM b_sonet_log_comment LC INNER JOIN (SELECT L.TMP_ID FROM b_sonet_log L WHERE L.ID = ".$ID.") L1 ON LC.LOG_ID = L1.TMP_ID", true);
		$DB->Query("DELETE FROM b_sonet_log_right WHERE LOG_ID = ".$ID, true);
		$DB->Query("DELETE FROM b_sonet_log_site WHERE LOG_ID = ".$ID, true);
		$DB->Query("DELETE FROM b_sonet_log_favorites WHERE LOG_ID = ".$ID, true);

		$bSuccess = $DB->Query("DELETE FROM b_sonet_log WHERE ID = ".$ID, true);

		return $bSuccess;
	}

	function DeleteNoDemand($userID)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID <= 0)
			return false;

		$DB->Query("DELETE LC FROM b_sonet_log_comment LC INNER JOIN (SELECT L.TMP_ID FROM b_sonet_log L WHERE L.ENTITY_TYPE = '".SONET_ENTITY_USER."' AND L.ENTITY_ID = ".$userID.") L1 ON LC.LOG_ID = L1.TMP_ID", true);
		$DB->Query("DELETE LS FROM b_sonet_log_site LS INNER JOIN (SELECT L.ID FROM b_sonet_log L WHERE L.ENTITY_TYPE = '".SONET_ENTITY_USER."' AND L.ENTITY_ID = ".$userID.") L1 ON LS.LOG_ID = L1.ID", true);
		$DB->Query("DELETE LR FROM b_sonet_log_right LR INNER JOIN (SELECT L.ID FROM b_sonet_log L WHERE L.ENTITY_TYPE = '".SONET_ENTITY_USER."' AND L.ENTITY_ID = ".$userID.") L1 ON LR.LOG_ID = L1.ID", true);
		$DB->Query("DELETE LF FROM b_sonet_log_favorites LF INNER JOIN (SELECT L.ID FROM b_sonet_log L WHERE L.ENTITY_TYPE = '".SONET_ENTITY_USER."' AND L.ENTITY_ID = ".$userID.") L1 ON LF.LOG_ID = L1.ID", true);
		$DB->Query("DELETE FROM b_sonet_log_favorites WHERE USER_ID = ".$userID, true);

		$DB->Query("DELETE FROM b_sonet_log WHERE ENTITY_TYPE = '".SONET_ENTITY_USER."' AND ENTITY_ID = ".$userID, true);

		return true;
	}
}

?>