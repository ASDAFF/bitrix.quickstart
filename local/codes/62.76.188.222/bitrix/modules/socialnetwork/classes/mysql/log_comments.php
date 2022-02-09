<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialnetwork/classes/general/log_comments.php");

class CSocNetLogComments extends CAllSocNetLogComments
{
	/***************************************/
	/********  DATA MODIFICATION  **********/
	/***************************************/
	function Add($arFields, $bSetSource = false, $bSendEvent = true, $bSetLogUpDate = true)
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

		if (!CSocNetLogComments::CheckFields("ADD", $arFields))
			return false;

		$arCommentEvent = CSocNetLogTools::FindLogCommentEventByID($arFields["EVENT_ID"]);
		if (
			!$arCommentEvent
			|| !array_key_exists("ADD_CALLBACK", $arCommentEvent)
			|| !is_callable($arCommentEvent["ADD_CALLBACK"])
		)
			$bSetSource = false;

		$db_events = GetModuleEvents("socialnetwork", "OnBeforeSocNetLogCommentAdd");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array(&$arFields))===false)
				return false;

		if ($bSetSource)
		{
			$arSource = CSocNetLogComments::SetSource($arFields);
			if (intval($arSource["SOURCE_ID"]) > 0)
			{
				$arFields["SOURCE_ID"] = $arSource["SOURCE_ID"];
				if (
					array_key_exists("RATING_ENTITY_ID", $arSource)
					&& array_key_exists("RATING_TYPE_ID", $arSource)
					&& intval($arSource["RATING_ENTITY_ID"]) > 0
					&& strlen($arSource["RATING_TYPE_ID"]) > 0
				)
				{
					$arFields["RATING_TYPE_ID"] = $arSource["RATING_TYPE_ID"];
					$arFields["RATING_ENTITY_ID"] = $arSource["RATING_ENTITY_ID"];
				}
			}
			else
				$strMessage =
				(
					array_key_exists("ERROR", $arSource) && strlen($arSource["ERROR"]) > 0
						? $arSource["ERROR"] :
						(
							array_key_exists("NOTES", $arSource)  && strlen($arSource["NOTES"]) > 0
								? $arSource["NOTES"]
								: ""
						)
				);
		}

		if (!$bSetSource || (is_array($arSource) && array_key_exists("SOURCE_ID", $arFields) && intval($arFields["SOURCE_ID"]) > 0))
		{
			$arInsert = $DB->PrepareInsert("b_sonet_log_comment", $arFields);

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
					"INSERT INTO b_sonet_log_comment(".$arInsert[0].") ".
					"VALUES(".$arInsert[1].")";
				$DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);

				$ID = IntVal($DB->LastID());

				if ($ID > 0)
				{
					if ($bSendEvent)
						CSocNetLogComments::SendEvent($ID, "SONET_NEW_EVENT");
					CSocNetLogComments::UpdateLogData($arFields["LOG_ID"], $bSetLogUpDate);

					$db_events = GetModuleEvents("socialnetwork", "OnAfterSocNetLogCommentAdd");
					while ($arEvent = $db_events->Fetch())
					{
						ExecuteModuleEventEx($arEvent, array($ID, $arFields));
					}
				}
			}

			CSocNetLogTools::SetCacheLastLogID("comment", $ID);
			return $ID;
		}
		elseif ($bSetSource && strlen($strMessage) > 0)
			return array(
					"ID"		=> false,
					"MESSAGE"	=> $strMessage
				);
		else
			return false;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_LC_WRONG_PARAMETER_ID"), "ERROR_NO_ID");
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

		if (!CSocNetLogComments::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$strUpdate = $DB->PrepareUpdate("b_sonet_log_comment", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($strUpdate) > 0)
				$strUpdate .= ", ";
			$strUpdate .= $key."=".$value." ";
		}

		if (strlen($strUpdate) > 0)
		{
			$strSql =
				"UPDATE b_sonet_log_comment SET ".
				"	".$strUpdate." ".
				"WHERE ID = ".$ID." ";
			$DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		else
			$ID = False;

		return $ID;
	}

	/***************************************/
	/**********  DATA SELECTION  ***********/
	/***************************************/
	function GetList($arOrder = Array("ID" => "DESC"), $arFilter = Array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arParams = array())
	{
		global $DB, $arSocNetAllowedEntityTypes, $USER;

		if (count($arSelectFields) <= 0)
			$arSelectFields = array(
				"ID", "LOG_ID", "SOURCE_ID", "ENTITY_TYPE", "ENTITY_ID", "USER_ID", "EVENT_ID", "LOG_DATE", "MESSAGE", "TEXT_MESSAGE", "URL", "MODULE_ID",
				"GROUP_NAME", "GROUP_OWNER_ID", "GROUP_VISIBLE", "GROUP_OPENED", "GROUP_IMAGE_ID",
				"USER_NAME", "USER_LAST_NAME", "USER_SECOND_NAME", "USER_LOGIN", "USER_PERSONAL_PHOTO", "USER_PERSONAL_GENDER",
				"CREATED_BY_NAME", "CREATED_BY_LAST_NAME", "CREATED_BY_SECOND_NAME", "CREATED_BY_LOGIN", "CREATED_BY_PERSONAL_PHOTO", "CREATED_BY_PERSONAL_GENDER",
				"LOG_SITE_ID", "LOG_SOURCE_ID",
				"RATING_TYPE_ID", "RATING_ENTITY_ID", "RATING_TOTAL_VALUE", "RATING_TOTAL_VOTES", "RATING_TOTAL_POSITIVE_VOTES", "RATING_TOTAL_NEGATIVE_VOTES", "RATING_USER_VOTE_VALUE"
			);

		static $arFields1 = array(
			"ID" => Array("FIELD" => "LC.ID", "TYPE" => "int"),
			"LOG_ID" => Array("FIELD" => "LC.LOG_ID", "TYPE" => "int"),
			"SOURCE_ID" => Array("FIELD" => "LC.SOURCE_ID", "TYPE" => "int"),
			"ENTITY_TYPE" => Array("FIELD" => "LC.ENTITY_TYPE", "TYPE" => "string"),
			"ENTITY_ID" => Array("FIELD" => "LC.ENTITY_ID", "TYPE" => "int"),
			"USER_ID" => Array("FIELD" => "LC.USER_ID", "TYPE" => "int"),
			"EVENT_ID" => Array("FIELD" => "LC.EVENT_ID", "TYPE" => "string"),
			"LOG_DATE" => Array("FIELD" => "LC.LOG_DATE", "TYPE" => "datetime"),
			"TITLE" => Array("FIELD" => "LC.TITLE", "TYPE" => "string"),
			"MESSAGE" => Array("FIELD" => "LC.MESSAGE", "TYPE" => "string"),
			"TEXT_MESSAGE" => Array("FIELD" => "LC.TEXT_MESSAGE", "TYPE" => "string"),
			"URL" => Array("FIELD" => "LC.URL", "TYPE" => "string"),
			"MODULE_ID" => Array("FIELD" => "LC.MODULE_ID", "TYPE" => "string"),
			"LOG_SOURCE_ID" => Array("FIELD" => "L.SOURCE_ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_sonet_log L ON (LC.LOG_ID = L.ID)"),
			"LOG_TITLE" => Array("FIELD" => "L1.TITLE", "TYPE" => "string", "FROM" => "INNER JOIN b_sonet_log L1 ON (LC.LOG_ID = L1.ID)"),
			"LOG_URL" => Array("FIELD" => "L1.URL", "TYPE" => "string", "FROM" => "INNER JOIN b_sonet_log L1 ON (LC.LOG_ID = L1.ID)"),
			"LOG_PARAMS" => Array("FIELD" => "L1.PARAMS", "TYPE" => "string", "FROM" => "INNER JOIN b_sonet_log L1 ON (LC.LOG_ID = L1.ID)"),
			"GROUP_NAME" => Array("FIELD" => "G.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_sonet_group G ON (LC.ENTITY_TYPE = 'G' AND LC.ENTITY_ID = G.ID)"),
			"GROUP_OWNER_ID" => Array("FIELD" => "G.OWNER_ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_sonet_group G ON (LC.ENTITY_TYPE = 'G' AND LC.ENTITY_ID = G.ID)"),
			"GROUP_VISIBLE" => Array("FIELD" => "G.VISIBLE", "TYPE" => "string", "FROM" => "LEFT JOIN b_sonet_group G ON (LC.ENTITY_TYPE = 'G' AND LC.ENTITY_ID = G.ID)"),
			"GROUP_OPENED" => Array("FIELD" => "G.OPENED", "TYPE" => "string", "FROM" => "LEFT JOIN b_sonet_group G ON (LC.ENTITY_TYPE = 'G' AND LC.ENTITY_ID = G.ID)"),
			"GROUP_IMAGE_ID" => Array("FIELD" => "G.IMAGE_ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_sonet_group G ON (LC.ENTITY_TYPE = 'G' AND LC.ENTITY_ID = G.ID)"),
			"USER_NAME" => Array("FIELD" => "U.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (LC.ENTITY_TYPE = 'U' AND LC.ENTITY_ID = U.ID)"),
			"USER_LAST_NAME" => Array("FIELD" => "U.LAST_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (LC.ENTITY_TYPE = 'U' AND LC.ENTITY_ID = U.ID)"),
			"USER_SECOND_NAME" => Array("FIELD" => "U.SECOND_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (LC.ENTITY_TYPE = 'U' AND LC.ENTITY_ID = U.ID)"),
			"USER_LOGIN" => Array("FIELD" => "U.LOGIN", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (LC.ENTITY_TYPE = 'U' AND LC.ENTITY_ID = U.ID)"),
			"USER_PERSONAL_PHOTO" => Array("FIELD" => "U.PERSONAL_PHOTO", "TYPE" => "int", "FROM" => "LEFT JOIN b_user U ON (LC.ENTITY_TYPE = 'U' AND LC.ENTITY_ID = U.ID)"),
			"USER_PERSONAL_GENDER" => Array("FIELD" => "U.PERSONAL_GENDER", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (LC.ENTITY_TYPE = 'U' AND LC.ENTITY_ID = U.ID)"),
			"CREATED_BY_NAME" => Array("FIELD" => "U1.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U1 ON LC.USER_ID = U1.ID"),
			"CREATED_BY_LAST_NAME" => Array("FIELD" => "U1.LAST_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U1 ON LC.USER_ID = U1.ID"),
			"CREATED_BY_SECOND_NAME" => Array("FIELD" => "U1.SECOND_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U1 ON LC.USER_ID = U1.ID"),
			"CREATED_BY_LOGIN" => Array("FIELD" => "U1.LOGIN", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U1 ON LC.USER_ID = U1.ID"),
			"CREATED_BY_PERSONAL_PHOTO" => Array("FIELD" => "U1.PERSONAL_PHOTO", "TYPE" => "int", "FROM" => "LEFT JOIN b_user U1 ON LC.USER_ID = U1.ID"),
			"CREATED_BY_PERSONAL_GENDER" => Array("FIELD" => "U1.PERSONAL_GENDER", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U1 ON LC.USER_ID = U1.ID"),
		);

		if (array_key_exists("LOG_SITE_ID", $arFilter))
		{
			$arFields["LOG_SITE_ID"] = Array("FIELD" => "SLS.SITE_ID", "TYPE" => "string", "FROM" => "LEFT JOIN b_sonet_log_site SLS ON LC.LOG_ID = SLS.LOG_ID");
			$strDistinct = " DISTINCT ";
			foreach ($arSelectFields as $i => $strFieldTmp)
				if ($strFieldTmp == "LOG_SITE_ID")
					unset($arSelectFields[$i]);

			foreach ($arOrder as $by => $order)
				if (!in_array($by, $arSelectFields))
					$arSelectFields[] = $by;
		}
		else
		{
			$arFields["LOG_SITE_ID"] = Array("FIELD" => "L.SITE_ID", "TYPE" => "string", "FROM" => "LEFT JOIN b_sonet_log L ON (LC.LOG_ID = L.ID)");
			$strDistinct = " ";
		}

		$arFields["RATING_TYPE_ID"] = Array("FIELD" => "LC.RATING_TYPE_ID", "TYPE" => "string");
		$arFields["RATING_ENTITY_ID"] = Array("FIELD" => "LC.RATING_ENTITY_ID", "TYPE" => "int");
		$arFields["RATING_TOTAL_VALUE"] = Array("FIELD" => $DB->IsNull('RG.TOTAL_VALUE', '0'), "TYPE" => "double", "FROM" => "LEFT JOIN b_rating_voting RG ON LC.RATING_TYPE_ID = RG.ENTITY_TYPE_ID AND LC.RATING_ENTITY_ID = RG.ENTITY_ID");
		$arFields["RATING_TOTAL_VOTES"] = Array("FIELD" => $DB->IsNull('RG.TOTAL_VOTES', '0'), "TYPE" => "double", "FROM" => "LEFT JOIN b_rating_voting RG ON LC.RATING_TYPE_ID = RG.ENTITY_TYPE_ID AND LC.RATING_ENTITY_ID = RG.ENTITY_ID");
		$arFields["RATING_TOTAL_POSITIVE_VOTES"] = Array("FIELD" => $DB->IsNull('RG.TOTAL_POSITIVE_VOTES', '0'), "TYPE" => "int", "FROM" => "LEFT JOIN b_rating_voting RG ON LC.RATING_TYPE_ID = RG.ENTITY_TYPE_ID AND LC.RATING_ENTITY_ID = RG.ENTITY_ID");
		$arFields["RATING_TOTAL_NEGATIVE_VOTES"] = Array("FIELD" => $DB->IsNull('RG.TOTAL_NEGATIVE_VOTES', '0'), "TYPE" => "int", "FROM" => "LEFT JOIN b_rating_voting RG ON LC.RATING_TYPE_ID = RG.ENTITY_TYPE_ID AND LC.RATING_ENTITY_ID = RG.ENTITY_ID");
		if (isset($USER) && is_object($USER))
			$arFields["RATING_USER_VOTE_VALUE"] = Array("FIELD" => $DB->IsNull('RV.VALUE', '0'), "TYPE" => "double", "FROM" => "LEFT JOIN b_rating_vote RV ON LC.RATING_TYPE_ID = RV.ENTITY_TYPE_ID AND LC.RATING_ENTITY_ID = RV.ENTITY_ID AND RV.USER_ID = ".intval($USER->GetId()));

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
				$arFields["LOG_RIGHTS"] = Array("FIELD" => "SLR0.GROUP_CODE", "TYPE" => "string", "FROM" => "INNER JOIN b_sonet_log_right SLR0 ON LC.LOG_ID = SLR0.LOG_ID");
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
				WHERE LC.LOG_ID = SLR.LOG_ID AND (0=1 ".
				(is_object($USER) && CSocNetUser::IsCurrentUserModuleAdmin() ? " OR SLR.GROUP_CODE = 'SA'" : "").
				(is_object($USER) && $USER->IsAuthorized() ? " OR (SLR.GROUP_CODE = 'AU')" : "").
				" OR (SLR.GROUP_CODE = 'G2')".
				(is_object($USER) && $USER->IsAuthorized() ? " OR (UA.ACCESS_CODE = SLR.GROUP_CODE AND UA.USER_ID = ".$USER->GetID().")" : "")."))";
		}

		if (
			$arParams["USE_SUBSCRIBE"] == "Y"
			&& intval($arParams["SUBSCRIBE_USER_ID"]) > 0
		)
			$arSqls["SUBSCRIBE"] = CSocNetLogEvents::GetSQL(
				$arParams["SUBSCRIBE_USER_ID"],
				(is_array($arParams["MY_ENTITIES"]) ? $arParams["MY_ENTITIES"] : array()),
				$arParams["TRANSPORT"],
				$arParams["VISIBLE"],
				"LC"
			);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", $strDistinct, $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_sonet_log_comment LC ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ".(strlen($arSqls["SUBSCRIBE"]) > 0 ? "AND (".$arSqls["SUBSCRIBE"].") " : "");
			else
				$strSql .= (strlen($arSqls["SUBSCRIBE"]) > 0 ? "WHERE (".$arSqls["SUBSCRIBE"].") " : "");
			if (strlen($arSqls["RIGHTS"]) > 0)
			{
				if (strlen($arSqls["WHERE"]) > 0 || strlen($arSqls["SUBSCRIBE"]) > 0)
					$strSql .= " AND ";
				else
					$strSql .= " WHERE ";
				$strSql .= $arSqls["RIGHTS"]." ";
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
			"FROM b_sonet_log_comment LC ".
			"	".$arSqls["FROM"]." ";

		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ".(strlen($arSqls["SUBSCRIBE"]) > 0 ? "AND (".$arSqls["SUBSCRIBE"].") " : "");
		else
			$strSql .= (strlen($arSqls["SUBSCRIBE"]) > 0 ? "WHERE (".$arSqls["SUBSCRIBE"].") " : "");
		if (strlen($arSqls["RIGHTS"]) > 0)
		{
			if (strlen($arSqls["WHERE"]) > 0 || strlen($arSqls["SUBSCRIBE"]) > 0)
				$strSql .= " AND ";
			else
				$strSql .= " WHERE ";
			$strSql .= $arSqls["RIGHTS"]." ";
		}
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"]) <= 0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_sonet_log_comment LC ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ".(strlen($arSqls["SUBSCRIBE"]) > 0 ? "AND (".$arSqls["SUBSCRIBE"].") " : "");
			else
				$strSql_tmp .= (strlen($arSqls["SUBSCRIBE"]) > 0 ? "WHERE (".$arSqls["SUBSCRIBE"].") " : "");
			if (strlen($arSqls["RIGHTS"]) > 0)
			{
				if (strlen($arSqls["WHERE"]) > 0 || strlen($arSqls["SUBSCRIBE"]) > 0)
					$strSql_tmp .= " AND ";
				else
					$strSql_tmp .= " WHERE ";
				$strSql_tmp .= $arSqls["RIGHTS"]." ";
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
}
?>