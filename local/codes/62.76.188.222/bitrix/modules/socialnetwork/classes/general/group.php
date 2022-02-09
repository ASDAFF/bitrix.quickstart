<?
IncludeModuleLangFile(__FILE__);

$GLOBALS["SONET_GROUP_CACHE"] = array();

class CAllSocNetGroup
{
	/***************************************/
	/********  DATA MODIFICATION  **********/
	/***************************************/
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		global $DB, $arSocNetAllowedInitiatePerms, $arSocNetAllowedSpamPerms;

		if ($ACTION != "ADD" && IntVal($ID) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("System error 870164", "ERROR");
			return false;
		}

		if(
			($ID === 0 && !is_set($arFields, "SITE_ID")) 
			||
			(
				is_set($arFields, "SITE_ID")
				&& (
					(is_array($arFields["SITE_ID"]) && count($arFields["SITE_ID"]) <= 0)
					||
					(!is_array($arFields["SITE_ID"]) && strlen($arFields["SITE_ID"]) <= 0)
				)
			)
		)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GG_EMPTY_SITE_ID"), "EMPTY_SITE_ID");
			return false;
		}
		elseif(is_set($arFields, "SITE_ID"))
		{
			if(!is_array($arFields["SITE_ID"]))
				$arFields["SITE_ID"] = array($arFields["SITE_ID"]);

			foreach($arFields["SITE_ID"] as $v)
			{
				$r = CSite::GetByID($v);
				if(!$r->Fetch())
				{
					$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $v, GetMessage("SONET_GG_ERROR_NO_SITE")), "ERROR_NO_SITE");				
					return false;
				}
			}
		}

		if ((is_set($arFields, "NAME") || $ACTION=="ADD") && strlen($arFields["NAME"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GB_EMPTY_NAME"), "EMPTY_NAME");
			return false;
		}

		if (is_set($arFields, "DATE_CREATE") && (!$DB->IsDate($arFields["DATE_CREATE"], false, LANG, "FULL")))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GB_EMPTY_DATE_CREATE"), "EMPTY_DATE_CREATE");
			return false;
		}

		if (is_set($arFields, "DATE_UPDATE") && (!$DB->IsDate($arFields["DATE_UPDATE"], false, LANG, "FULL")))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GB_EMPTY_DATE_UPDATE"), "EMPTY_DATE_UPDATE");
			return false;
		}

		if (is_set($arFields, "DATE_ACTIVITY") && (!$DB->IsDate($arFields["DATE_ACTIVITY"], false, LANG, "FULL")))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GB_EMPTY_DATE_ACTIVITY"), "EMPTY_DATE_ACTIVITY");
			return false;
		}

		if ((is_set($arFields, "OWNER_ID") || $ACTION=="ADD") && IntVal($arFields["OWNER_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GB_EMPTY_OWNER_ID"), "EMPTY_OWNER_ID");
			return false;
		}
		elseif (is_set($arFields, "OWNER_ID"))
		{
			$dbResult = CUser::GetByID($arFields["OWNER_ID"]);
			if (!$dbResult->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GB_ERROR_NO_OWNER_ID"), "ERROR_NO_OWNER_ID");
				return false;
			}
		}

		if ((is_set($arFields, "SUBJECT_ID") || $ACTION=="ADD") && IntVal($arFields["SUBJECT_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GB_EMPTY_SUBJECT_ID"), "EMPTY_SUBJECT_ID");
			return false;
		}
		elseif (is_set($arFields, "SUBJECT_ID"))
		{
			$arResult = CSocNetGroupSubject::GetByID($arFields["SUBJECT_ID"]);
			if ($arResult == false)
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GB_ERROR_NO_SUBJECT_ID"), "ERROR_NO_SUBJECT_ID");
				return false;
			}
		}

		if ((is_set($arFields, "ACTIVE") || $ACTION=="ADD") && $arFields["ACTIVE"] != "Y" && $arFields["ACTIVE"] != "N")
			$arFields["ACTIVE"] = "Y";

		if ((is_set($arFields, "VISIBLE") || $ACTION=="ADD") && $arFields["VISIBLE"] != "Y" && $arFields["VISIBLE"] != "N")
			$arFields["VISIBLE"] = "Y";

		if ((is_set($arFields, "OPENED") || $ACTION=="ADD") && $arFields["OPENED"] != "Y" && $arFields["OPENED"] != "N")
			$arFields["OPENED"] = "N";

		if ((is_set($arFields, "CLOSED") || $ACTION=="ADD") && $arFields["CLOSED"] != "Y" && $arFields["CLOSED"] != "N")
			$arFields["CLOSED"] = "N";

		if ((is_set($arFields, "INITIATE_PERMS") || $ACTION=="ADD") && strlen($arFields["INITIATE_PERMS"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_EMPTY_INITIATE_PERMS"), "EMPTY_INITIATE_PERMS");
			return false;
		}
		elseif (is_set($arFields, "INITIATE_PERMS") && !in_array($arFields["INITIATE_PERMS"], $arSocNetAllowedInitiatePerms))
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["INITIATE_PERMS"], GetMessage("SONET_UG_ERROR_NO_INITIATE_PERMS")), "ERROR_NO_INITIATE_PERMS");
			return false;
		}

		if ((is_set($arFields, "SPAM_PERMS") || $ACTION=="ADD") && strlen($arFields["SPAM_PERMS"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_EMPTY_SPAM_PERMS"), "EMPTY_SPAM_PERMS");
			return false;
		}
		elseif (is_set($arFields, "SPAM_PERMS") && !in_array($arFields["SPAM_PERMS"], $arSocNetAllowedSpamPerms))
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["SPAM_PERMS"], GetMessage("SONET_UG_ERROR_NO_SPAM_PERMS")), "ERROR_NO_SPAM_PERMS");
			return false;
		}

		if (is_set($arFields, "IMAGE_ID") && strlen($arFields["IMAGE_ID"]["name"])<=0 && (strlen($arFields["IMAGE_ID"]["del"])<=0 || $arFields["IMAGE_ID"]["del"] != "Y"))
			unset($arFields["IMAGE_ID"]);

		if (is_set($arFields, "IMAGE_ID"))
		{
			$arResult = CFile::CheckImageFile($arFields["IMAGE_ID"], 0, 0, 0);
			if (strlen($arResult) > 0)
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GP_ERROR_IMAGE_ID").": ".$arResult, "ERROR_IMAGE_ID");
				return false;
			}
		}

		if (!$GLOBALS["USER_FIELD_MANAGER"]->CheckFields("SONET_GROUP", $ID, $arFields))
			return false;

		return True;
	}

	function Delete($ID)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($ID))
			return false;

		$ID = IntVal($ID);
		$bSuccess = True;

		$db_events = GetModuleEvents("socialnetwork", "OnBeforeSocNetGroupDelete");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
				return false;

		$arGroup = CSocNetGroup::GetByID($ID);
		if (!$arGroup)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_NO_GROUP"), "ERROR_NO_GROUP");
			return false;
		}

		$DB->StartTransaction();

		$events = GetModuleEvents("socialnetwork", "OnSocNetGroupDelete");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID));

		if ($bSuccess)
			$bSuccess = $DB->Query("DELETE FROM b_sonet_user2group WHERE GROUP_ID = ".$ID."", true);

		if ($bSuccess)
		{
			$bSuccessTmp = true;
			$dbResult = CSocNetFeatures::GetList(
				array(),
				array("ENTITY_ID" => $ID, "ENTITY_TYPE" => SONET_ENTITY_GROUP)
			);
			while ($arResult = $dbResult->Fetch())
			{
				$bSuccessTmp = $DB->Query("DELETE FROM b_sonet_features2perms WHERE FEATURE_ID = ".$arResult["ID"]."", true);
				if (!$bSuccessTmp)
					break;
			}
			if (!$bSuccessTmp)
				$bSuccess = false;
		}
		if ($bSuccess)
			$bSuccess = $DB->Query("DELETE FROM b_sonet_features WHERE ENTITY_ID = ".$ID." AND ENTITY_TYPE = '".$DB->ForSql(SONET_ENTITY_GROUP, 1)."'", true);
		if ($bSuccess)
		{
			$dbResult = CSocNetLog::GetList(
				array(),
				array("ENTITY_ID" => $ID, "ENTITY_TYPE" => SONET_ENTITY_GROUP),
				false,
				false,
				array("ID")
			);
			while ($arResult = $dbResult->Fetch())
			{
				$bSuccessTmp = $DB->Query("DELETE FROM b_sonet_log_site WHERE LOG_ID = ".$arResult["ID"]."", true);
				if (!$bSuccessTmp)
					break;

				$bSuccessTmp = $DB->Query("DELETE FROM b_sonet_log_right WHERE LOG_ID = ".$arResult["ID"]."", true);
				if (!$bSuccessTmp)
					break;
			}
			if (!$bSuccessTmp)
				$bSuccess = false;
		}
		if ($bSuccess)		
			$bSuccess = $DB->Query("DELETE FROM b_sonet_log WHERE ENTITY_TYPE = '".SONET_ENTITY_GROUP."' AND ENTITY_ID = ".$ID."", true);
		if ($bSuccess)
			$bSuccess = CSocNetLog::DeleteSystemEventsByGroupID($ID);
		if ($bSuccess)
			$bSuccess = $DB->Query("DELETE FROM b_sonet_log_events WHERE ENTITY_TYPE = 'G' AND ENTITY_ID = ".$ID."", true);
		if ($bSuccess)
			$bSuccess = $DB->Query("DELETE FROM b_sonet_group_site WHERE GROUP_ID = ".$ID."", true);

		if ($bSuccess)
			$bSuccess = $DB->Query("DELETE FROM b_sonet_log_right WHERE GROUP_CODE LIKE 'SG".$ID."\_%' OR GROUP_CODE = 'SG".$ID."'", true);

		if ($bSuccess)
		{
			CFile::Delete($arGroup["IMAGE_ID"]);
			$bSuccess = $DB->Query("DELETE FROM b_sonet_group WHERE ID = ".$ID."", true);
		}

		if ($bSuccess)
		{
			CUserOptions::DeleteOption("socialnetwork", "~menu_".SONET_ENTITY_GROUP."_".$ID, false, 0);
			unset($GLOBALS["SONET_GROUP_CACHE"][$ID]);
		}
		
		if ($bSuccess)
			$DB->Commit();
		else
			$DB->Rollback();

		if ($bSuccess)
		{
			unset($GLOBALS["SONET_GROUP_CACHE"][$ID]);
			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("sonet_user2group_G".$ID);
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("sonet_group_".$ID);
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("sonet_group");
			}
		}

		if ($bSuccess && CModule::IncludeModule("search"))
			CSearch::DeleteIndex("socialnetwork", "G".$ID);

		if ($bSuccess)
			$DB->Query("DELETE FROM b_sonet_event_user_view WHERE ENTITY_TYPE = '".SONET_ENTITY_GROUP."' AND ENTITY_ID = ".$ID, true);

		if ($bSuccess)
			$GLOBALS["USER_FIELD_MANAGER"]->Delete("SONET_GROUP", $ID);
		
//		if ($bSuccess)
//			$GLOBALS["USER_FIELD_MANAGER"]->Delete("BLOG_BLOG", $ID);

		return $bSuccess;
	}

	function DeleteNoDemand($userID)
	{
		if (!CSocNetGroup::__ValidateID($userID))
			return false;

		$userID = IntVal($userID);

		$err = "";
		$dbResult = CSocNetGroup::GetList(array(), array("OWNER_ID" => $userID), false, false, array("ID", "NAME"));
		while ($arResult = $dbResult->GetNext())
			$err .= $arResult["NAME"]."<br>";
		
		if (strlen($err) <= 0)
			return true;
		else
		{
			$err = GetMessage("SONET_GG_ERROR_CANNOT_DELETE_USER_1").$err;
			$err .= GetMessage("SONET_GG_ERROR_CANNOT_DELETE_USER_2");
			$GLOBALS["APPLICATION"]->ThrowException($err);
			return false;
		}
	}

	function SetStat($ID)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($ID))
			return false;
		
		$ID = IntVal($ID);

		$num = CSocNetUserToGroup::GetList(
			array(),
			array(
				"GROUP_ID" => $ID,
				"USER_ACTIVE" => "Y",
				"<=ROLE" => SONET_ROLES_USER
			),
			array()
		);

		$num_mods = CSocNetUserToGroup::GetList(
			array(),
			array(
				"GROUP_ID" => $ID,
				"USER_ACTIVE" => "Y",
				"<=ROLE" => SONET_ROLES_MODERATOR
			),
			array()
		);

		CSocNetGroup::Update(
			$ID, 
			array(
				"NUMBER_OF_MEMBERS" => $num,
				"NUMBER_OF_MODERATORS" => $num_mods
			)
		);
	}

	function SetLastActivity($ID, $date = false)
	{
		if (!CSocNetGroup::__ValidateID($ID))
			return false;

		$ID = IntVal($ID);

		if ($date == false)
			CSocNetGroup::Update($ID, array("=DATE_ACTIVITY" => $GLOBALS["DB"]->CurrentTimeFunction()));
		else
			CSocNetGroup::Update($ID, array("DATE_ACTIVITY" => $date));
	}

	/***************************************/
	/**********  DATA SELECTION  ***********/
	/***************************************/
	function GetByID($ID)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($ID))
			return false;

		$ID = IntVal($ID);

		if (
			array_key_exists("SONET_GROUP_CACHE", $GLOBALS) 
			&& is_array($GLOBALS["SONET_GROUP_CACHE"])
			&& array_key_exists($ID, $GLOBALS["SONET_GROUP_CACHE"])
			&& is_array($GLOBALS["SONET_GROUP_CACHE"][$ID])
		)
			return $GLOBALS["SONET_GROUP_CACHE"][$ID];

		$dbResult = CSocNetGroup::GetList(Array(), Array("ID" => $ID), false, false, array("ID", "SITE_ID", "NAME", "DESCRIPTION", "DATE_CREATE", "DATE_UPDATE", "ACTIVE", "VISIBLE", "OPENED", "CLOSED", "SUBJECT_ID", "OWNER_ID", "KEYWORDS", "IMAGE_ID", "NUMBER_OF_MEMBERS", "NUMBER_OF_MODERATORS", "INITIATE_PERMS", "SPAM_PERMS", "DATE_ACTIVITY", "SUBJECT_NAME", "UF_*"));
		if ($arResult = $dbResult->GetNext())
		{
			$arResult["NAME_FORMATTED"] = $arResult["NAME"];

			if (!array_key_exists("SONET_GROUP_CACHE", $GLOBALS) || !is_array($GLOBALS["SONET_GROUP_CACHE"]))
				$GLOBALS["SONET_GROUP_CACHE"] = array();

			$GLOBALS["SONET_GROUP_CACHE"][$ID] = $arResult;

			return $arResult;
		}

		return False;
	}

	/***************************************/
	/**********  COMMON METHODS  ***********/
	/***************************************/
	function CanUserInitiate($userID, $groupID)
	{
		$userID = IntVal($userID);
		$groupID = IntVal($groupID);
		if ($groupID <= 0)
			return false;

		$userRoleInGroup = CSocNetUserToGroup::GetUserRole($userID, $groupID);
		if ($userRoleInGroup == false)
			return false;

		$arGroup = CSocNetGroup::GetById($groupID);
		if ($arGroup == false)
			return false;

		if ($arGroup["INITIATE_PERMS"] == SONET_ROLES_MODERATOR)
		{
			if ($userRoleInGroup == SONET_ROLES_MODERATOR || $userRoleInGroup == SONET_ROLES_OWNER)
				return true;
			else
				return false;
		}
		elseif ($arGroup["INITIATE_PERMS"] == SONET_ROLES_USER)
		{
			if ($userRoleInGroup == SONET_ROLES_MODERATOR || $userRoleInGroup == SONET_ROLES_OWNER || $userRoleInGroup == SONET_ROLES_USER)
				return true;
			else
				return false;
		}
		elseif ($arGroup["INITIATE_PERMS"] == SONET_ROLES_OWNER)
		{
			if ($userRoleInGroup == SONET_ROLES_OWNER)
				return true;
			else
				return false;
		}

		return false;
	}

	function CanUserViewGroup($userID, $groupID)
	{
		$userID = IntVal($userID);
		$groupID = IntVal($groupID);
		if ($groupID <= 0)
			return false;

		$arGroup = CSocNetGroup::GetById($groupID);
		if ($arGroup == false)
			return false;

		if ($arGroup["VISIBLE"] == "Y")
			return true;

		$userRoleInGroup = CSocNetUserToGroup::GetUserRole($userID, $groupID);
		if ($userRoleInGroup == false)
			return false;

		if ($userRoleInGroup == SONET_ROLES_MODERATOR || $userRoleInGroup == SONET_ROLES_OWNER || $userRoleInGroup == SONET_ROLES_USER)
			return true;
		else
			return false;

		return false;
	}

	function CanUserReadGroup($userID, $groupID)
	{
		$userID = IntVal($userID);
		$groupID = IntVal($groupID);
		if ($groupID <= 0)
			return false;

		$arGroup = CSocNetGroup::GetById($groupID);
		if ($arGroup == false)
			return false;

		if ($arGroup["OPENED"] == "Y")
			return true;

		$userRoleInGroup = CSocNetUserToGroup::GetUserRole($userID, $groupID);
		if ($userRoleInGroup == false)
			return false;

		if ($userRoleInGroup == SONET_ROLES_MODERATOR || $userRoleInGroup == SONET_ROLES_OWNER || $userRoleInGroup == SONET_ROLES_USER)
			return true;
		else
			return false;

		return false;
	}

	/***************************************/
	/************  ACTIONS  ****************/
	/***************************************/
	function CreateGroup($ownerID, $arFields, $bAutoSubscribe = true)
	{
		global $APPLICATION, $DB;

		$ownerID = IntVal($ownerID);
		if ($ownerID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_OWNERID").". ", "ERROR_OWNERID");
			return false;
		}

		if (!isset($arFields) || !is_array($arFields))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_FIELDS").". ", "ERROR_FIELDS");
			return false;
		}

		$DB->StartTransaction();

		$arFields["=DATE_CREATE"] = $GLOBALS["DB"]->CurrentTimeFunction();
		$arFields["=DATE_UPDATE"] = $GLOBALS["DB"]->CurrentTimeFunction();
		$arFields["=DATE_ACTIVITY"] = $GLOBALS["DB"]->CurrentTimeFunction();
		$arFields["ACTIVE"] = "Y";
		$arFields["OWNER_ID"] = $ownerID;

		$groupID = CSocNetGroup::Add($arFields);

		if (!$groupID || IntVal($groupID) <= 0)
		{
			$errorMessage = "";
			if ($e = $APPLICATION->GetException())
			{
				$errorMessage = $e->GetString();
				$errorID = $e->GetID();

				if (StrLen($errorID) <= 0 && isset($e->messages) && is_array($e->messages) && is_array($e->messages[0]) && array_key_exists("id", $e->messages[0]))
					$errorID = $e->messages[0]["id"];
			}

			if (StrLen($errorMessage) <= 0)
				$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_GROUP").". ";

			if (StrLen($errorID) <= 0)
				$errorID = "ERROR_CREATE_GROUP";

			$GLOBALS["APPLICATION"]->ThrowException($errorMessage, $errorID);
			$DB->Rollback();
			return false;
		}

		$arFields1 = array(
			"USER_ID" => $ownerID,
			"GROUP_ID" => $groupID,
			"ROLE" => SONET_ROLES_OWNER,
			"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			"=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			"INITIATED_BY_TYPE" => SONET_INITIATED_BY_USER,
			"INITIATED_BY_USER_ID" => $ownerID,
			"MESSAGE" => false
		);

		if (!CSocNetUserToGroup::Add($arFields1))
		{
			$errorMessage = "";
			if ($e = $APPLICATION->GetException())
				$errorMessage = $e->GetString();
			if (StrLen($errorMessage) <= 0)
				$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_U_GROUP").". ";

			$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_CREATE_GROUP");
			$DB->Rollback();
			return false;
		}

		if ($bAutoSubscribe)
			CSocNetLogEvents::AutoSubscribe($ownerID, SONET_ENTITY_GROUP, $groupID);

		$DB->Commit();

		return $groupID;
	}

	/***************************************/
	/*************  UTILITIES  *************/
	/***************************************/
	function __ValidateID($ID)
	{
		if (IntVal($ID)."|" == $ID."|")
			return true;

		$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_WRONG_PARAMETER_ID"), "ERROR_NO_ID");
		return false;
	}

	function GetFilterOperation($key)
	{
		$strNegative = "N";
		if (substr($key, 0, 1)=="!")
		{
			$key = substr($key, 1);
			$strNegative = "Y";
		}

		$strOrNull = "N";
		if (substr($key, 0, 1)=="+")
		{
			$key = substr($key, 1);
			$strOrNull = "Y";
		}

		if (substr($key, 0, 2)==">=")
		{
			$key = substr($key, 2);
			$strOperation = ">=";
		}
		elseif (substr($key, 0, 1)==">")
		{
			$key = substr($key, 1);
			$strOperation = ">";
		}
		elseif (substr($key, 0, 2)=="<=")
		{
			$key = substr($key, 2);
			$strOperation = "<=";
		}
		elseif (substr($key, 0, 1)=="<")
		{
			$key = substr($key, 1);
			$strOperation = "<";
		}
		elseif (substr($key, 0, 1)=="@")
		{
			$key = substr($key, 1);
			$strOperation = "IN";
		}
		elseif (substr($key, 0, 1)=="~")
		{
			$key = substr($key, 1);
			$strOperation = "LIKE";
		}
		elseif (substr($key, 0, 1)=="%")
		{
			$key = substr($key, 1);
			$strOperation = "QUERY";
		}
		else
		{
			$strOperation = "=";
		}

		return array("FIELD" => $key, "NEGATIVE" => $strNegative, "OPERATION" => $strOperation, "OR_NULL" => $strOrNull);
	}

	function PrepareSql(&$arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields, $arUF = array())
	{
		global $DB;

		$obUserFieldsSql = false;
		
		if (is_array($arUF) && array_key_exists("ENTITY_ID", $arUF))
		{
			$obUserFieldsSql = new CUserTypeSQL;
			$obUserFieldsSql->SetEntity($arUF["ENTITY_ID"], $arFields["ID"]["FIELD"]);
			$obUserFieldsSql->SetSelect($arSelectFields);
			$obUserFieldsSql->SetFilter($arFilter);
			$obUserFieldsSql->SetOrder($arOrder);
		}

		$strSqlSelect = "";
		$strSqlFrom = "";
		$strSqlWhere = "";
		$strSqlGroupBy = "";
		$strSqlOrderBy = "";

		$arGroupByFunct = array("COUNT", "AVG", "MIN", "MAX", "SUM");

		$arAlreadyJoined = array();

		// GROUP BY -->
		if (is_array($arGroupBy) && count($arGroupBy)>0)
		{
			$arSelectFields = $arGroupBy;
			foreach ($arGroupBy as $key => $val)
			{
				$val = strtoupper($val);
				$key = strtoupper($key);
				if (array_key_exists($val, $arFields) && !in_array($key, $arGroupByFunct))
				{
					if (strlen($strSqlGroupBy) > 0)
						$strSqlGroupBy .= ", ";
					$strSqlGroupBy .= $arFields[$val]["FIELD"];

					if (isset($arFields[$val]["FROM"])
						&& strlen($arFields[$val]["FROM"]) > 0
						&& !in_array($arFields[$val]["FROM"], $arAlreadyJoined))
					{
						if (strlen($strSqlFrom) > 0)
							$strSqlFrom .= " ";
						$strSqlFrom .= $arFields[$val]["FROM"];
						$arAlreadyJoined[] = $arFields[$val]["FROM"];
					}
				}
			}
		}
		// <-- GROUP BY

		// WHERE -->
		$arSqlSearch = Array();

		if (!is_array($arFilter))
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		for ($i = 0; $i < count($filter_keys); $i++)
		{
			$vals = $arFilter[$filter_keys[$i]];
			if (!is_array($vals))
				$vals = array($vals);

			$key = $filter_keys[$i];
			$key_res = CSocNetGroup::GetFilterOperation($key);
			$key = $key_res["FIELD"];
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];
			$strOrNull = $key_res["OR_NULL"];

			if (array_key_exists($key, $arFields))
			{
				$arSqlSearch_tmp = array();
				foreach ($vals as $val)
				{
					if (isset($arFields[$key]["WHERE"]))
					{
						$arSqlSearch_tmp1 = call_user_func_array(
								$arFields[$key]["WHERE"],
								array($val, $key, $strOperation, $strNegative, $arFields[$key]["FIELD"], &$arFields, &$arFilter)
							);
						if ($arSqlSearch_tmp1 !== false)
							$arSqlSearch_tmp[] = $arSqlSearch_tmp1;
					}
					else
					{
						if ($arFields[$key]["TYPE"] == "int")
						{
							if ((IntVal($val) == 0) && (strpos($strOperation, "=") !== False))
								$arSqlSearch_tmp[] = "(".$arFields[$key]["FIELD"]." IS ".(($strNegative == "Y") ? "NOT " : "")."NULL) ".(($strNegative == "Y") ? "AND" : "OR")." ".(($strNegative == "Y") ? "NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." 0)";
							else
								$arSqlSearch_tmp[] = (($strNegative == "Y") ? " ".$arFields[$key]["FIELD"]." IS NULL OR NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".IntVal($val)." )";
						}
						elseif ($arFields[$key]["TYPE"] == "double")
						{
							$val = str_replace(",", ".", $val);

							if ((DoubleVal($val) == 0) && (strpos($strOperation, "=") !== False))
								$arSqlSearch_tmp[] = "(".$arFields[$key]["FIELD"]." IS ".(($strNegative == "Y") ? "NOT " : "")."NULL) ".(($strNegative == "Y") ? "AND" : "OR")." ".(($strNegative == "Y") ? "NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." 0)";
							else
								$arSqlSearch_tmp[] = (($strNegative == "Y") ? " ".$arFields[$key]["FIELD"]." IS NULL OR NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".DoubleVal($val)." )";
						}
						elseif ($arFields[$key]["TYPE"] == "string" || $arFields[$key]["TYPE"] == "char")
						{
							if ($strOperation == "QUERY")
								$arSqlSearch_tmp[] = GetFilterQuery($arFields[$key]["FIELD"], $val, (strpos($val, "%") === false ? "Y" : "N"));
							else
							{
								if ((strlen($val) == 0) && (strpos($strOperation, "=") !== False))
									$arSqlSearch_tmp[] = "(".$arFields[$key]["FIELD"]." IS ".(($strNegative == "Y") ? "NOT " : "")."NULL) ".(($strNegative == "Y") ? "AND NOT" : "OR")." (".$DB->Length($arFields[$key]["FIELD"])." <= 0) ".(($strNegative == "Y") ? "AND NOT" : "OR")." (".$arFields[$key]["FIELD"]." ".$strOperation." '".$DB->ForSql($val)."' )";
								else
									$arSqlSearch_tmp[] = (($strNegative == "Y") ? " ".$arFields[$key]["FIELD"]." IS NULL OR NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." '".$DB->ForSql($val)."' )";
							}
						}
						elseif ($arFields[$key]["TYPE"] == "string_or_null" || $arFields[$key]["TYPE"] == "char_or_null")
						{
							if ($strOperation == "QUERY")
								$arSqlSearch_tmp[] = GetFilterQuery($arFields[$key]["FIELD"], $val, (strpos($val, "%") === false ? "Y" : "N"));
							else
							{
								if ((strlen($val) == 0) && (strpos($strOperation, "=") !== False))
								{
// future functionality
								}
								else
									$arSqlSearch_tmp[] = "(".$arFields[$key]["FIELD"]." ".$strOperation." '".$DB->ForSql($val)."' OR ".$arFields[$key]["FIELD"]." IS NULL)";
							}
						}
						elseif ($arFields[$key]["TYPE"] == "datetime")
						{
							if (strlen($val) <= 0)
								$arSqlSearch_tmp[] = ($strNegative=="Y"?"NOT":"")."(".$arFields[$key]["FIELD"]." IS NULL)";
							else
								$arSqlSearch_tmp[] = ($strNegative=="Y"?" ".$arFields[$key]["FIELD"]." IS NULL OR NOT ":"")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "FULL").")";
						}
						elseif ($arFields[$key]["TYPE"] == "date")
						{
							if (strlen($val) <= 0)
								$arSqlSearch_tmp[] = ($strNegative=="Y"?"NOT":"")."(".$arFields[$key]["FIELD"]." IS NULL)";
							else
								$arSqlSearch_tmp[] = ($strNegative=="Y"?" ".$arFields[$key]["FIELD"]." IS NULL OR NOT ":"")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "SHORT").")";
						}
					}
				}

				if (isset($arFields[$key]["FROM"])
					&& strlen($arFields[$key]["FROM"]) > 0
					&& !in_array($arFields[$key]["FROM"], $arAlreadyJoined))
				{
					if (strlen($strSqlFrom) > 0)
						$strSqlFrom .= " ";
					$strSqlFrom .= $arFields[$key]["FROM"];
					$arAlreadyJoined[] = $arFields[$key]["FROM"];
				}

				$strSqlSearch_tmp = "";
				for ($j = 0; $j < count($arSqlSearch_tmp); $j++)
				{
					if ($j > 0)
						$strSqlSearch_tmp .= ($strNegative=="Y" ? " AND " : " OR ");
					$strSqlSearch_tmp .= "(".$arSqlSearch_tmp[$j].")";
				}
				if ($strOrNull == "Y")
				{
					if (strlen($strSqlSearch_tmp) > 0)
						$strSqlSearch_tmp .= ($strNegative=="Y" ? " AND " : " OR ");
					$strSqlSearch_tmp .= "(".$arFields[$key]["FIELD"]." IS ".($strNegative=="Y" ? "NOT " : "")."NULL)";

					if (strlen($strSqlSearch_tmp) > 0)
						$strSqlSearch_tmp .= ($strNegative=="Y" ? " AND " : " OR ");
					if ($arFields[$key]["TYPE"] == "int" || $arFields[$key]["TYPE"] == "double")
						$strSqlSearch_tmp .= "(".$arFields[$key]["FIELD"]." ".($strNegative=="Y" ? "<>" : "=")." 0)";
					elseif ($arFields[$key]["TYPE"] == "string" || $arFields[$key]["TYPE"] == "char")
						$strSqlSearch_tmp .= "(".$arFields[$key]["FIELD"]." ".($strNegative=="Y" ? "<>" : "=")." '')";
				}

				if ($strSqlSearch_tmp != "")
					$arSqlSearch[] = "(".$strSqlSearch_tmp.")";
			}
		}

		for ($i = 0; $i < count($arSqlSearch); $i++)
		{
			if (strlen($strSqlWhere) > 0)
				$strSqlWhere .= " AND ";
			$strSqlWhere .= "(".$arSqlSearch[$i].")";
		}
		
		if ($obUserFieldsSql)
		{
			$r = $obUserFieldsSql->GetFilter();
			if(strlen($r) > 0)
				$strSqlWhere .= (strlen($strSqlWhere) > 0 ? " AND" : "")." (".$r.") ";
		}
		// <-- WHERE

		// ORDER BY -->
		$arSqlOrder = Array();
		foreach ($arOrder as $by => $order)
		{
			$by = strtoupper($by);
			$order = strtoupper($order);

			if ($order != "ASC")
				$order = "DESC";
			else
				$order = "ASC";

			if (array_key_exists($by, $arFields))
			{
				if ($arFields[$by]["TYPE"] == "datetime" || $arFields[$by]["TYPE"] == "date")
				{
					$arSqlOrder[] = " ".$by."_X1 ".$order." ";
					if (!is_array($arSelectFields) || !in_array($by, $arSelectFields))
						$arSelectFields[] = $by;
				}
				else
					$arSqlOrder[] = " ".$arFields[$by]["FIELD"]." ".$order." ";

				if (isset($arFields[$by]["FROM"])
					&& strlen($arFields[$by]["FROM"]) > 0
					&& !in_array($arFields[$by]["FROM"], $arAlreadyJoined))
				{
					if (strlen($strSqlFrom) > 0)
						$strSqlFrom .= " ";
					$strSqlFrom .= $arFields[$by]["FROM"];
					$arAlreadyJoined[] = $arFields[$by]["FROM"];
				}
			}
			elseif($obUserFieldsSql && $s = $obUserFieldsSql->GetOrder($by))
				$arSqlOrder[$by] = " ".$s." ".$order." ";
		}
		
		$strSqlOrderBy = "";
		DelDuplicateSort($arSqlOrder); 
		for ($i=0; $i<count($arSqlOrder); $i++)
		{
			if (strlen($strSqlOrderBy) > 0)
				$strSqlOrderBy .= ", ";

			if(strtoupper($DB->type)=="ORACLE")
			{
				if(substr($arSqlOrder[$i], -3)=="ASC")
					$strSqlOrderBy .= $arSqlOrder[$i]." NULLS FIRST";
				else
					$strSqlOrderBy .= $arSqlOrder[$i]." NULLS LAST";
			}
			else
				$strSqlOrderBy .= $arSqlOrder[$i];
		}
		// <-- ORDER BY

		// SELECT -->
		$arFieldsKeys = array_keys($arFields);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSqlSelect = "COUNT(%%_DISTINCT_%% ".$arFields[$arFieldsKeys[0]]["FIELD"].") as CNT ";
		}
		else
		{
			if (isset($arSelectFields) && !is_array($arSelectFields) && is_string($arSelectFields) && strlen($arSelectFields)>0 && array_key_exists($arSelectFields, $arFields))
				$arSelectFields = array($arSelectFields);

			if (!isset($arSelectFields)
				|| !is_array($arSelectFields)
				|| count($arSelectFields)<=0
				|| in_array("*", $arSelectFields))
			{
				for ($i = 0; $i < count($arFieldsKeys); $i++)
				{
					if (isset($arFields[$arFieldsKeys[$i]]["WHERE_ONLY"])
						&& $arFields[$arFieldsKeys[$i]]["WHERE_ONLY"] == "Y")
						continue;

					if (strlen($strSqlSelect) > 0)
						$strSqlSelect .= ", ";

					if ($arFields[$arFieldsKeys[$i]]["TYPE"] == "datetime")
					{
						if (array_key_exists($arFieldsKeys[$i], $arOrder))
							$strSqlSelect .= $arFields[$arFieldsKeys[$i]]["FIELD"]." as ".$arFieldsKeys[$i]."_X1, ";

						$strSqlSelect .= $DB->DateToCharFunction($arFields[$arFieldsKeys[$i]]["FIELD"], "FULL")." as ".$arFieldsKeys[$i];
					}
					elseif ($arFields[$arFieldsKeys[$i]]["TYPE"] == "date")
					{
						if (array_key_exists($arFieldsKeys[$i], $arOrder))
							$strSqlSelect .= $arFields[$arFieldsKeys[$i]]["FIELD"]." as ".$arFieldsKeys[$i]."_X1, ";

						$strSqlSelect .= $DB->DateToCharFunction($arFields[$arFieldsKeys[$i]]["FIELD"], "SHORT")." as ".$arFieldsKeys[$i];
					}
					else
						$strSqlSelect .= $arFields[$arFieldsKeys[$i]]["FIELD"]." as ".$arFieldsKeys[$i];

					if (isset($arFields[$arFieldsKeys[$i]]["FROM"])
						&& strlen($arFields[$arFieldsKeys[$i]]["FROM"]) > 0
						&& !in_array($arFields[$arFieldsKeys[$i]]["FROM"], $arAlreadyJoined))
					{
						if (strlen($strSqlFrom) > 0)
							$strSqlFrom .= " ";
						$strSqlFrom .= $arFields[$arFieldsKeys[$i]]["FROM"];
						$arAlreadyJoined[] = $arFields[$arFieldsKeys[$i]]["FROM"];
					}
				}
			}
			else
			{
				foreach ($arSelectFields as $key => $val)
				{
					$val = strtoupper($val);
					$key = strtoupper($key);
					if (array_key_exists($val, $arFields))
					{
						if (strlen($strSqlSelect) > 0)
							$strSqlSelect .= ", ";

						if (in_array($key, $arGroupByFunct))
							$strSqlSelect .= $key."(".$arFields[$val]["FIELD"].") as ".$val;
						else
						{
							if ($arFields[$val]["TYPE"] == "datetime")
							{
								if (array_key_exists($val, $arOrder))
									$strSqlSelect .= $arFields[$val]["FIELD"]." as ".$val."_X1, ";

								$strSqlSelect .= $DB->DateToCharFunction($arFields[$val]["FIELD"], "FULL")." as ".$val;
							}
							elseif ($arFields[$val]["TYPE"] == "date")
							{
								if (array_key_exists($val, $arOrder))
									$strSqlSelect .= $arFields[$val]["FIELD"]." as ".$val."_X1, ";

								$strSqlSelect .= $DB->DateToCharFunction($arFields[$val]["FIELD"], "SHORT")." as ".$val;
							}
							else
								$strSqlSelect .= $arFields[$val]["FIELD"]." as ".$val;
						}

						if (isset($arFields[$val]["FROM"])
							&& strlen($arFields[$val]["FROM"]) > 0
							&& !in_array($arFields[$val]["FROM"], $arAlreadyJoined))
						{
							if (strlen($strSqlFrom) > 0)
								$strSqlFrom .= " ";
							$strSqlFrom .= $arFields[$val]["FROM"];
							$arAlreadyJoined[] = $arFields[$val]["FROM"];
						}
					}
				}
			}

			if ($obUserFieldsSql)
				$strSqlSelect .= (strlen($strSqlSelect) <= 0 ? $arFields["ID"]["FIELD"] : "").$obUserFieldsSql->GetSelect();
			
			if (strlen($strSqlGroupBy) > 0)
			{
				if (strlen($strSqlSelect) > 0)
					$strSqlSelect .= ", ";
				$strSqlSelect .= "COUNT(%%_DISTINCT_%% ".$arFields[$arFieldsKeys[0]]["FIELD"].") as CNT";
			}
			else
				$strSqlSelect = "%%_DISTINCT_%% ".$strSqlSelect;
		}
		// <-- SELECT

		if ($obUserFieldsSql)
			$strSqlFrom .= " ".$obUserFieldsSql->GetJoin($arFields["ID"]["FIELD"]);
		
		return array(
			"SELECT" => $strSqlSelect,
			"FROM" => $strSqlFrom,
			"WHERE" => $strSqlWhere,
			"GROUPBY" => $strSqlGroupBy,
			"ORDERBY" => $strSqlOrderBy
		);
	}

	/***************************************/
	/*************    *************/
	/***************************************/

	function GetSite($group_id)
	{
		global $DB;
		$strSql = "SELECT L.*, SGS.* FROM b_sonet_group_site SGS, b_lang L WHERE L.LID=SGS.SITE_ID AND SGS.GROUP_ID=".IntVal($group_id);
		return $DB->Query($strSql);
	}

	function OnBeforeLangDelete($lang)
	{
		global $APPLICATION, $DB;
		$r = $DB->Query("
			SELECT GROUP_ID
			FROM b_sonet_group_site
			WHERE SITE_ID='".$DB->ForSQL($lang, 2)."'
			ORDER BY GROUP_ID
		");
		$arSocNetGroups = array();
		while($a = $r->Fetch())
			$arSocNetGroups[] = $a["GROUP_ID"];
		if(count($arSocNetGroups) > 0)
		{
			$APPLICATION->ThrowException(GetMessage("SONET_GROUP_SITE_LINKS_EXISTS", array("#ID_LIST#" => implode(", ", $arSocNetGroups))));
			return false;
		}
		else
			return true;
	}
}
?>