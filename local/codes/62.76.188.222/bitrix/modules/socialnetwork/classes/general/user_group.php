<?
IncludeModuleLangFile(__FILE__);

global $arSocNetUserInRoleCache;
$arSocNetUserInRoleCache = array();

class CAllSocNetUserToGroup
{
	/***************************************/
	/********  DATA MODIFICATION  **********/
	/***************************************/
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		global $DB, $arSocNetAllowedRolesForUserInGroup, $arSocNetAllowedInitiatedByType;

		if ($ACTION != "ADD" && IntVal($ID) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("System error 870164", "ERROR");
			return false;
		}

		if ((is_set($arFields, "USER_ID") || $ACTION=="ADD") && IntVal($arFields["USER_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_EMPTY_USER_ID"), "EMPTY_USER_ID");
			return false;
		}
		elseif (is_set($arFields, "USER_ID"))
		{
			$dbResult = CUser::GetByID($arFields["USER_ID"]);
			if (!$dbResult->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_USER_ID"), "ERROR_NO_USER_ID");
				return false;
			}
		}

		if ((is_set($arFields, "GROUP_ID") || $ACTION=="ADD") && IntVal($arFields["GROUP_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_EMPTY_GROUP_ID"), "EMPTY_GROUP_ID");
			return false;
		}
		elseif (is_set($arFields, "GROUP_ID"))
		{
			$arResult = CSocNetGroup::GetByID($arFields["GROUP_ID"]);
			if ($arResult == false)
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_GROUP_ID"), "ERROR_NO_GROUP_ID");
				return false;
			}
		}

		if ((is_set($arFields, "ROLE") || $ACTION=="ADD") && strlen($arFields["ROLE"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_EMPTY_ROLE"), "EMPTY_ROLE");
			return false;
		}
		elseif (is_set($arFields, "ROLE") && !in_array($arFields["ROLE"], $arSocNetAllowedRolesForUserInGroup))
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["ROLE"], GetMessage("SONET_UG_ERROR_NO_ROLE")), "ERROR_NO_ROLE");
			return false;
		}

		if ((is_set($arFields, "INITIATED_BY_TYPE") || $ACTION=="ADD") && strlen($arFields["INITIATED_BY_TYPE"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_EMPTY_INITIATED_BY_TYPE"), "EMPTY_INITIATED_BY_TYPE");
			return false;
		}
		elseif (is_set($arFields, "INITIATED_BY_TYPE") && !in_array($arFields["INITIATED_BY_TYPE"], $arSocNetAllowedInitiatedByType))
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["INITIATED_BY_TYPE"], GetMessage("SONET_UG_ERROR_NO_INITIATED_BY_TYPE")), "ERROR_NO_INITIATED_BY_TYPE");
			return false;
		}

		if ((is_set($arFields, "INITIATED_BY_USER_ID") || $ACTION=="ADD") && IntVal($arFields["INITIATED_BY_USER_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_EMPTY_INITIATED_BY_USER_ID"), "EMPTY_INITIATED_BY_USER_ID");
			return false;
		}
		elseif (is_set($arFields, "INITIATED_BY_USER_ID"))
		{
			$dbResult = CUser::GetByID($arFields["INITIATED_BY_USER_ID"]);
			if (!$dbResult->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_INITIATED_BY_USER_ID"), "ERROR_NO_INITIATED_BY_USER_ID");
				return false;
			}
		}

		if (is_set($arFields, "DATE_CREATE") && (!$DB->IsDate($arFields["DATE_CREATE"], false, LANG, "FULL")))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_EMPTY_DATE_CREATE"), "EMPTY_DATE_CREATE");
			return false;
		}

		if (is_set($arFields, "DATE_UPDATE") && (!$DB->IsDate($arFields["DATE_UPDATE"], false, LANG, "FULL")))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_EMPTY_DATE_UPDATE"), "EMPTY_DATE_UPDATE");
			return false;
		}

		return True;
	}

	function Delete($ID, $bSendExclude = false)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($ID))
			return false;

		$ID = IntVal($ID);
		$bSuccess = True;

		$arUser2Group = CSocNetUserToGroup::GetByID($ID);
		if (!$arUser2Group)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_NO_USER2GROUP"), "ERROR_NO_USER2GROUP");
			return false;
		}

		$db_events = GetModuleEvents("socialnetwork", "OnBeforeSocNetUserToGroupDelete");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
				return false;

		$events = GetModuleEvents("socialnetwork", "OnSocNetUserToGroupDelete");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arUser2Group));

		if (CModule::IncludeModule("im"))
			CIMNotify::DeleteByTag("SOCNET|INVITE_GROUP|".intval($arUser2Group['USER_ID'])."|".intval($ID));

		$bSuccess = $DB->Query("DELETE FROM b_sonet_user2group WHERE ID = ".$ID."", true);

		CSocNetGroup::SetStat($arUser2Group["GROUP_ID"]);
		CSocNetSearch::OnUserRelationsChange($arUser2Group["USER_ID"]);

		global $arSocNetUserInRoleCache;
		if (!isset($arSocNetUserInRoleCache) || !is_array($arSocNetUserInRoleCache))
			$arSocNetUserInRoleCache = array();
		if (array_key_exists($arUser2Group["USER_ID"]."_".$arUser2Group["GROUP_ID"], $arSocNetUserInRoleCache))
			unset($arSocNetUserInRoleCache[$arUser2Group["USER_ID"]."_".$arUser2Group["GROUP_ID"]]);

		if($bSuccess && defined("BX_COMP_MANAGED_CACHE"))
		{
			$GLOBALS["CACHE_MANAGER"]->ClearByTag("sonet_user2group_G".$arUser2Group["GROUP_ID"]);
			$GLOBALS["CACHE_MANAGER"]->ClearByTag("sonet_user2group_U".$arUser2Group["USER_ID"]);
		}

		if ($bSuccess && $bSendExclude && in_array($arUser2Group["ROLE"], array(SONET_ROLES_MODERATOR, SONET_ROLES_USER)))
		{
			if ($arUser2Group["GROUP_VISIBLE"] == "Y")
			{
				$arGroupSiteID = array();
				$rsGroupSite = CSocNetGroup::GetSite($arUser2Group["GROUP_ID"]);
				while($arGroupSite = $rsGroupSite->Fetch())
					$arGroupSiteID[] = $arGroupSite["LID"];
			}

			$logID = CSocNetLog::Add(
				array(
					"ENTITY_TYPE" => SONET_ENTITY_GROUP,
					"ENTITY_ID" => $arUser2Group["GROUP_ID"],
					"EVENT_ID" => "system",
					"=LOG_DATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"TITLE_TEMPLATE" => false,
					"TITLE" => "exclude_group",
					"MESSAGE" => $arUser2Group["USER_ID"],
					"URL" => false,
					"MODULE_ID" => false,
					"CALLBACK_FUNC" => false,
					"USER_ID" => $arUser2Group["USER_ID"],
					"SITE_ID" => $arGroupSiteID					
				),
				false
			);

			if (intval($logID) > 0)
			{
				$tmpID = $logID;
				CSocNetLog::Update($logID, array("TMP_ID" => $tmpID));
				CSocNetLogRights::Add($logID, array("SA", "U".$arUser2Group["USER_ID"], "S".SONET_ENTITY_GROUP.$arUser2Group["GROUP_ID"], "S".SONET_ENTITY_GROUP.$arUser2Group["GROUP_ID"]."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$arUser2Group["GROUP_ID"]."_".SONET_ROLES_MODERATOR, "S".SONET_ENTITY_GROUP.$arUser2Group["GROUP_ID"]."_".SONET_ROLES_USER));
				CSocNetLog::SendEvent($logID, "SONET_NEW_EVENT", $tmpID);
			}
			$arMessageFields = array(
				"FROM_USER_ID" => $GLOBALS["USER"]->GetID(),
				"TO_USER_ID" => $arUser2Group["USER_ID"],
				"MESSAGE" => str_replace("#NAME#", $arUser2Group["GROUP_NAME"], GetMessage("SONET_UG_EXCLUDE_MESSAGE")),
				"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
				"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM
			);
			CSocNetMessages::Add($arMessageFields);
		}

		return $bSuccess;
	}

	function DeleteNoDemand($userID)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($userID))
			return false;

		$userID = IntVal($userID);
		$bSuccess = True;

		$arGroups = array();

		$dbResult = CSocNetUserToGroup::GetList(array(), array("USER_ID" => $userID), false, false, array("GROUP_ID"));
		while ($arResult = $dbResult->Fetch())
			$arGroups[] = $arResult["GROUP_ID"];

		$DB->Query("DELETE FROM b_sonet_user2group WHERE USER_ID = ".$userID."", true);

		for ($i = 0; $i < count($arGroups); $i++)
			CSocNetGroup::SetStat($arGroups[$i]);

		global $arSocNetUserInRoleCache;
		$arSocNetUserInRoleCache = array();

		CSocNetUserToGroup::__SpeedFileDelete($userID);
		CSocNetSearch::OnUserRelationsChange($userID);

		return $bSuccess;
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

		$dbResult = CSocNetUserToGroup::GetList(
						Array(),
						Array("ID" => $ID),
						false,
						false,
						Array("ID", "USER_ID", "GROUP_ID", "GROUP_VISIBLE", "GROUP_NAME", "ROLE", "DATE_CREATE", "DATE_UPDATE", "INITIATED_BY_TYPE", "INITIATED_BY_USER_ID", "MESSAGE")
					);
		if ($arResult = $dbResult->GetNext())
		{
			return $arResult;
		}

		return False;
	}

	/***************************************/
	/**********  COMMON METHODS  ***********/
	/***************************************/
	function GetUserRole($userID, $groupID)
	{
		global $DB;

		$userID = IntVal($userID);
		if ($userID <= 0)
			return false;

		global $arSocNetUserInRoleCache;

		if (!isset($arSocNetUserInRoleCache) || !is_array($arSocNetUserInRoleCache) || array_key_exists("arSocNetUserInRoleCache", $_REQUEST))
			$arSocNetUserInRoleCache = array();

		if (is_array($groupID))
		{
			$arGroupToGet = array();
			foreach($groupID as $TmpGroupID)
				if (!array_key_exists($userID."_".$TmpGroupID, $arSocNetUserInRoleCache))
					$arGroupToGet[] = $TmpGroupID;

			if (count($arGroupToGet) > 0)
			{
				$dbResult = CSocNetUserToGroup::GetList(
					array(),
					array("USER_ID" => $userID, "GROUP_ID" => $arGroupToGet),
					false,
					false,
					array("GROUP_ID", "ROLE")
				);
				$arRolesFromDB = array();
				while ($arResult = $dbResult->Fetch())
					$arRolesFromDB[$arResult["GROUP_ID"]] = $arResult["ROLE"];

				foreach($arGroupToGet as $TmpGroupID)
				{
					if (array_key_exists($TmpGroupID, $arRolesFromDB))
						$arSocNetUserInRoleCache[$userID."_".$TmpGroupID] = $arRolesFromDB[$TmpGroupID];
					else
						$arSocNetUserInRoleCache[$userID."_".$TmpGroupID] = false;
				}
			}

			foreach($groupID as $TmpGroupID)
				$arReturn[$TmpGroupID] = $arSocNetUserInRoleCache[$userID."_".$TmpGroupID];

			return $arReturn;
		}
		else
		{
			$groupID = IntVal($groupID);
			if ($groupID <= 0)
				return false;

			if (!array_key_exists($userID."_".$groupID, $arSocNetUserInRoleCache))
			{
				$dbResult = CSocNetUserToGroup::GetList(
					array(),
					array("USER_ID" => $userID, "GROUP_ID" => $groupID),
					false,
					false,
					array("ROLE")
				);
				if ($arResult = $dbResult->Fetch())
					$arSocNetUserInRoleCache[$userID."_".$groupID] = $arResult["ROLE"];
				else
					$arSocNetUserInRoleCache[$userID."_".$groupID] = false;
			}

			return $arSocNetUserInRoleCache[$userID."_".$groupID];
		}

	}

	/***************************************/
	/**********  SEND EVENTS  **************/
	/***************************************/
	function SendEvent($userGroupID, $mailTemplate = "SONET_INVITE_GROUP")
	{
		$userGroupID = IntVal($userGroupID);
		if ($userGroupID <= 0)
			return false;

		$dbRelation = CSocNetUserToGroup::GetList(
			array(),
			array("ID" => $userGroupID),
			false,
			false,
			array("ID", "USER_ID", "GROUP_ID", "ROLE", "DATE_CREATE", "MESSAGE", "INITIATED_BY_TYPE", "INITIATED_BY_USER_ID", "GROUP_NAME", "USER_NAME", "USER_LAST_NAME", "USER_EMAIL", "USER_LID")
		);
		$arRelation = $dbRelation->Fetch();
		if (!$arRelation)
			return false;

		if (CModule::IncludeModule("extranet"))
			$arUserGroup = CUser::GetUserGroup($arRelation["USER_ID"]);

		$rsGroupSite = CSocNetGroup::GetSite($arRelation["GROUP_ID"]);
		while ($arGroupSite = $rsGroupSite->Fetch())
		{
			if (IsModuleInstalled("extranet"))
			{
				if (
					(
						CExtranet::IsExtranetSite($arGroupSite["LID"])
						&& in_array(CExtranet::GetExtranetUserGroupID(), $arUserGroup)
					)
					||
					(
						!CExtranet::IsExtranetSite($arGroupSite["LID"])
						&& !in_array(CExtranet::GetExtranetUserGroupID(), $arUserGroup)
					)
				)
				{
					$siteID = $arGroupSite["LID"];
					break;
				}
				else
					continue;
			}
			else
			{
				$siteID = $arGroupSite["LID"];
				break;
			}
		}

		if ($siteID == false || StrLen($siteID) <= 0)
			return false;

		$MessagesPageURL = COption::GetOptionString("socialnetwork", "messages_path", "/company/personal/messages/", $siteID);

		$arUserInitiatedForEmail = array("NAME"=>"", "LAST_NAME"=>"");

		if (intval($arRelation["INITIATED_BY_USER_ID"]) > 0):

			$dbUserInitiated = CUser::GetList(
				($by="id"),
				($order="desc"),
				array("ID" => $arRelation["INITIATED_BY_USER_ID"])
			);

			if ($arUserInitiated = $dbUserInitiated->Fetch())
				$arUserInitiatedForEmail = array("NAME"=>$arUserInitiated["NAME"], "LAST_NAME"=>$arUserInitiated["LAST_NAME"]);

		endif;

		$arFields = array(
			"RELATION_ID" => $userGroupID,
			"URL" => (!IsModuleInstalled("im") ? $MessagesPageURL : ""),
			"GROUP_ID" => $arRelation["GROUP_ID"],
			"USER_ID" => $arRelation["USER_ID"],
			"GROUP_NAME" => $arRelation["GROUP_NAME"],
			"USER_NAME" => $arRelation["USER_NAME"],
			"USER_LAST_NAME" => $arRelation["USER_LAST_NAME"],
			"USER_EMAIL" => $arRelation["USER_EMAIL"],
			"INITIATED_USER_NAME" => $arUserInitiatedForEmail["NAME"],
			"INITIATED_USER_LAST_NAME" => $arUserInitiatedForEmail["LAST_NAME"],
			"MESSAGE" => $arRelation["MESSAGE"]
		);

		$event = new CEvent;
		$event->Send($mailTemplate, $siteID, $arFields, "N");

		return true;
	}

	/***************************************/
	/************  ACTIONS  ****************/
	/***************************************/
	function SendRequestToBeMember($userID, $groupID, $message, $RequestConfirmUrl = "", $bAutoSubscribe = true)
	{
		global $APPLICATION;

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_USERID");
			return false;
		}

		$groupID = IntVal($groupID);
		if ($groupID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_GROUPID"), "ERROR_GROUPID");
			return false;
		}

		$arGroup = CSocNetGroup::GetByID($groupID);
		if (!$arGroup || !is_array($arGroup) || $arGroup["ACTIVE"] != "Y"/* || $arGroup["VISIBLE"] != "Y"*/)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_GROUP_ID"), "ERROR_NO_GROUP");
			return false;
		}

		$arFields = array(
			"USER_ID" => $userID,
			"GROUP_ID" => $groupID,
			"ROLE" => SONET_ROLES_REQUEST,
			"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			"=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			"MESSAGE" => $message,
			"INITIATED_BY_TYPE" => SONET_INITIATED_BY_USER,
			"INITIATED_BY_USER_ID" => $userID
		);
		if ($arGroup["OPENED"] == "Y")
			$arFields["ROLE"] = SONET_ROLES_USER;

		if (!CSocNetUserToGroup::Add($arFields))
		{
			$errorMessage = "";
			if ($e = $APPLICATION->GetException())
				$errorMessage = $e->GetString();
			if (StrLen($errorMessage) <= 0)
				$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_USER2GROUP");

			$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_CREATE_USER2GROUP");
			return false;
		}

		if ($arGroup["OPENED"] == "Y")
		{
			if ($bAutoSubscribe)
				CSocNetLogEvents::AutoSubscribe($userID, SONET_ENTITY_GROUP, $groupID);

			$groupUrl = false;

			$arGroupSiteID = array();
			$rsGroupSite = CSocNetGroup::GetSite($groupID);
			while($arGroupSite = $rsGroupSite->Fetch())
			{
				$arGroupSiteID[] = $arGroupSite["LID"];

				//get server name
				$rsSites = CSite::GetByID($arGroupSite["LID"]);
				$arSite = $rsSites->Fetch();
				$serverName = $arSite["SERVER_NAME"];
				if (strlen($serverName) <= 0)
				{
					if (defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME) > 0)
						$serverName = SITE_SERVER_NAME;
					else
						$serverName = COption::GetOptionString("main", "server_name", $GLOBALS["SERVER_NAME"]);
				}

				if (strlen($serverName) > 0)
				{
					$protocol = (CMain::IsHTTPS() ? "https" : "http");
					$serverName = $protocol."://".$serverName;
				}
			}

			$groupUrl = $serverName.str_replace("#group_id#", $arGroup["ID"], COption::GetOptionString("socialnetwork", "group_path_template", "/workgroups/group/#group_id#/", $arGroupSiteID["0"]));

			$logID = CSocNetLog::Add(
				array(
					"ENTITY_TYPE" => SONET_ENTITY_GROUP,
					"ENTITY_ID" => $groupID,
					"EVENT_ID" => "system",
					"=LOG_DATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"TITLE_TEMPLATE" => false,
					"TITLE" => "join",
					"MESSAGE" => $userID,
					"URL" => $groupUrl,
					"MODULE_ID" => false,
					"CALLBACK_FUNC" => false,
					"USER_ID" => $userID,
					"SITE_ID" => $arGroupSiteID
				),
				false
			);

			if (intval($logID) > 0)
			{
				CSocNetLogRights::Add($logID, array("SA", "U".$userID, "S".SONET_ENTITY_GROUP.$groupID, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_MODERATOR, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_USER));
				CSocNetLog::Update($logID, array("TMP_ID" => $logID));
				$tmpID = $logID;
			}

			CSocNetLog::SendEvent($logID, "SONET_NEW_EVENT", $tmpID);
		}
		elseif(strlen(trim($RequestConfirmUrl)) > 0)
		{
			// send sonet system messages to owner and (may be) moderators to accept or refuse request
			if ($arGroup["INITIATE_PERMS"] == SONET_ROLES_OWNER)
				$FilterRole = SONET_ROLES_OWNER;
			else
				$FilterRole = SONET_ROLES_MODERATOR;

			$dbRequests = CSocNetUserToGroup::GetList(
				array("USER_ID" => "ASC"),
				array(
					"GROUP_ID" => $groupID,
					"<=ROLE" => $FilterRole,
					"USER_ACTIVE" => "Y"
				),
				false,
				false,
				array("ID", "USER_ID", "USER_NAME", "USER_LAST_NAME", "USER_EMAIL")
			);
			if ($dbRequests)
			{
				$emailTemplate = 'SONET_REQUEST_GROUP';

				$rsUser = CUser::GetByID($userID);
				$arUser = $rsUser->GetNext();
				$userName = $arUser["NAME"]." ".$arUser["LAST_NAME"];

				$groupName = $arGroup["NAME"];
				$title = GetMessage('SONET_UG_REQUEST_G_TITLE', array('#USER_NAME#' => $userName, '#GROUP_NAME#' => $groupName));
				$mess = GetMessage('SONET_UG_REQUEST_G', array('#USER_NAME#' => $userName, '#GROUP_NAME#' => $groupName));

				if (strlen(trim($message)) > 0)
					$mess .= "\n\n".GetMessage('SONET_UG_REQUEST_G_TEXT', array('#REQUEST_TEXT#' => $message));

				$mess .= "\n\n".GetMessage('SONET_UG_REQUEST_G_LINK', array('#LINK#' => $RequestConfirmUrl));

				while ($arRequests = $dbRequests->GetNext())
				{
					$arMessageFields = array(
						"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
						"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM,
						"FROM_USER_ID" => $userID,
						"TITLE" => $title,
						"TO_USER_ID" => $arRequests["USER_ID"],
						"MESSAGE" => $mess,
						"EMAIL_TEMPLATE" => $emailTemplate
					);

					$res = CSocNetMessages::Add($arMessageFields);
				}
			}

		}

		return true;
	}

	function SendRequestToJoinGroup($senderID, $userID, $groupID, $message)
	{
		global $APPLICATION;

		$senderID = IntVal($senderID);
		if ($senderID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_SENDERID");
			return false;
		}

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_USERID");
			return false;
		}

		$groupID = IntVal($groupID);
		if ($groupID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_GROUPID"), "ERROR_GROUPID");
			return false;
		}

		$arGroup = CSocNetGroup::GetByID($groupID);
		if (!$arGroup || !is_array($arGroup))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_GROUP_ID"), "ERROR_NO_GROUP");
			return false;
		}

		$arGroupSites = array();
		$rsGroupSite = CSocNetGroup::GetSite($groupID);
		while ($arGroupSite = $rsGroupSite->Fetch())
			$arGroupSites[] = $arGroupSite["LID"];

		$userRole = CSocNetUserToGroup::GetUserRole($senderID, $groupID);
		$bUserIsMember = ($userRole && in_array($userRole, array(SONET_ROLES_OWNER, SONET_ROLES_MODERATOR, SONET_ROLES_USER)));
		$bCanInitiate = ($GLOBALS["USER"]->IsAdmin() || CSocNetUser::IsCurrentUserModuleAdmin($arGroupSites) || ($userRole
			&& (($arGroup["INITIATE_PERMS"] == SONET_ROLES_OWNER && $senderID == $arGroup["OWNER_ID"])
				|| ($arGroup["INITIATE_PERMS"] == SONET_ROLES_MODERATOR && in_array($userRole, array(SONET_ROLES_OWNER, SONET_ROLES_MODERATOR)))
				|| ($arGroup["INITIATE_PERMS"] == SONET_ROLES_USER && $bUserIsMember))));

		if (!$bCanInitiate)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_PERMS"), "ERROR_NO_PERMS");
			return false;
		}

		$arFields = array(
			"USER_ID" => $userID,
			"GROUP_ID" => $groupID,
			"ROLE" => SONET_ROLES_REQUEST,
			"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			"=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			"MESSAGE" => str_replace(Array("#TEXT#", "#GROUP_NAME#"), Array($message, $arGroup["NAME"]), (empty($message)?GetMessage("SONET_UG_INVITE_CONFIRM_TEXT_EMPTY"):GetMessage("SONET_UG_INVITE_CONFIRM_TEXT"))),
			"INITIATED_BY_TYPE" => SONET_INITIATED_BY_GROUP,
			"INITIATED_BY_USER_ID" => $senderID
		);
		$ID = CSocNetUserToGroup::Add($arFields);
		if (!$ID)
		{
			$errorMessage = "";
			if ($e = $APPLICATION->GetException())
				$errorMessage = $e->GetString();
			if (StrLen($errorMessage) <= 0)
				$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_USER2GROUP");

			$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_CREATE_USER2GROUP");
			return false;
		}

		if (CModule::IncludeModule("im"))
		{
			$arMessageFields = array(
				"MESSAGE_TYPE" => IM_MESSAGE_SYSTEM,
				"TO_USER_ID" => intval($arFields['USER_ID']),
				"FROM_USER_ID" => intval($arFields['INITIATED_BY_USER_ID']),
				"NOTIFY_TYPE" => IM_NOTIFY_CONFIRM,
				"NOTIFY_MODULE" => "socialnetwork",
				"NOTIFY_EVENT" => "invite_group",
				"NOTIFY_TAG" => "SOCNET|INVITE_GROUP|".intval($arFields['USER_ID'])."|".intval($ID),
				"NOTIFY_MESSAGE" => str_replace(Array("#TEXT#", "#GROUP_NAME#"), Array($message, $arGroup["NAME"]), (empty($message)?GetMessage("SONET_UG_INVITE_CONFIRM_TEXT_EMPTY"):GetMessage("SONET_UG_INVITE_CONFIRM_TEXT"))),
				"NOTIFY_BUTTONS" => Array(
					Array('TITLE' => GetMessage('SONET_UG_INVITE_CONFIRM'), 'VALUE' => 'Y', 'TYPE' => 'accept'),
					Array('TITLE' => GetMessage('SONET_UG_INVITE_REJECT'), 'VALUE' => 'N', 'TYPE' => 'cancel'),
				),
			);
			CIMNotify::Add($arMessageFields);
		}

		$events = GetModuleEvents("socialnetwork", "OnSocNetSendRequestToJoinGroup");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		CSocNetUserToGroup::__SpeedFileCreate($userID);

		return true;
	}

	function ConfirmRequestToBeMember($userID, $groupID, $arRelationID, $bAutoSubscribe = true)
	{
		global $APPLICATION, $DB;

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_USERID");
			return false;
		}

		$groupID = IntVal($groupID);
		if ($groupID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_GROUPID"), "ERROR_GROUPID");
			return false;
		}

		if (!is_array($arRelationID))
			return true;

		$arGroup = CSocNetGroup::GetByID($groupID);
		if (!$arGroup || !is_array($arGroup))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_GROUP_ID"), "ERROR_NO_GROUP");
			return false;
		}

		$arGroupSites = array();
		$rsGroupSite = CSocNetGroup::GetSite($groupID);
		while ($arGroupSite = $rsGroupSite->Fetch())
			$arGroupSites[] = $arGroupSite["LID"];

		$userRole = CSocNetUserToGroup::GetUserRole($userID, $groupID);
		$bUserIsMember = ($userRole && in_array($userRole, array(SONET_ROLES_OWNER, SONET_ROLES_MODERATOR, SONET_ROLES_USER)));
		$bCanInitiate = ($GLOBALS["USER"]->IsAdmin() || CSocNetUser::IsCurrentUserModuleAdmin($arGroupSites) || ($userRole
			&& (($arGroup["INITIATE_PERMS"] == SONET_ROLES_OWNER && $userID == $arGroup["OWNER_ID"])
				|| ($arGroup["INITIATE_PERMS"] == SONET_ROLES_MODERATOR && in_array($userRole, array(SONET_ROLES_OWNER, SONET_ROLES_MODERATOR)))
				|| ($arGroup["INITIATE_PERMS"] == SONET_ROLES_USER && $bUserIsMember))));

		if (!$bCanInitiate)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_PERMS"), "ERROR_NO_PERMS");
			return false;
		}

		$bSuccess = true;
		$arSuccessID = array();
		for ($i = 0; $i < count($arRelationID); $i++)
		{
			$arRelationID[$i] = IntVal($arRelationID[$i]);
			if ($arRelationID[$i] <= 0)
				continue;

			$arRelation = CSocNetUserToGroup::GetByID($arRelationID[$i]);
			if (!$arRelation)
				continue;

			if ($arRelation["GROUP_ID"] != $groupID || $arRelation["INITIATED_BY_TYPE"] != SONET_INITIATED_BY_USER || $arRelation["ROLE"] != SONET_ROLES_REQUEST)
				continue;

			$arFields = array(
				"ROLE" => SONET_ROLES_USER,
				"=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			);
			if (CSocNetUserToGroup::Update($arRelation["ID"], $arFields))
			{
				$arSuccessID[] = $arRelation["USER_ID"];

				if ($bAutoSubscribe)
					CSocNetLogEvents::AutoSubscribe($arRelation["USER_ID"], SONET_ENTITY_GROUP, $groupID);

				$arMessageFields = array(
					"FROM_USER_ID" => $userID,
					"TO_USER_ID" => $arRelation["USER_ID"],
					"MESSAGE" => str_replace("#NAME#", $arGroup["NAME"], GetMessage("SONET_UG_CONFIRM_MEMBER_MESSAGE_G")),
					"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM
				);
				CSocNetMessages::Add($arMessageFields);
			}
			else
			{
				$errorMessage = "";
				if ($e = $APPLICATION->GetException())
					$errorMessage = $e->GetString();
				if (StrLen($errorMessage) <= 0)
					$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_USER2GROUP");

				$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_CONFIRM_MEMBER");
				$bSuccess = false;
			}
		}

		if (count($arSuccessID) > 0)
		{
			$arLogFields = array(
					"ENTITY_TYPE" => SONET_ENTITY_GROUP,
					"ENTITY_ID" => $groupID,
					"EVENT_ID" => "system",
					"=LOG_DATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"TITLE_TEMPLATE" => false,
					"TITLE" => "join",
					"MESSAGE" => implode(",", $arSuccessID),
					"URL" => false,
					"MODULE_ID" => false,
					"CALLBACK_FUNC" => false,
					"SITE_ID" => $arGroupSites
				);

			if (count($arRelationID) == 1)
				$arLogFields["USER_ID"] = $arRelation["USER_ID"];

			$logID = CSocNetLog::Add($arLogFields, false);
			if (intval($logID) > 0)
			{
				$arTmp = array();
				foreach($arSuccessID as $success_id)
					$arTmp[] = "U".$success_id;

				$tmpID = $logID;
				CSocNetLog::Update($logID, array("TMP_ID" => $tmpID));
				CSocNetLogRights::Add($logID, array_merge($arTmp, array("SA", "S".SONET_ENTITY_GROUP.$groupID, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_MODERATOR, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_USER)));
			}

			CSocNetLog::SendEvent($logID, "SONET_NEW_EVENT", $tmpID);
		}

		return $bSuccess;
	}

	function RejectRequestToBeMember($userID, $groupID, $arRelationID)
	{
		global $APPLICATION, $DB;

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_USERID");
			return false;
		}

		$groupID = IntVal($groupID);
		if ($groupID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_GROUPID"), "ERROR_GROUPID");
			return false;
		}

		if (!is_array($arRelationID))
			return true;

		$arGroup = CSocNetGroup::GetByID($groupID);
		if (!$arGroup || !is_array($arGroup))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_GROUP_ID"), "ERROR_NO_GROUP");
			return false;
		}

		$arGroupSites = array();
		$rsGroupSite = CSocNetGroup::GetSite($groupID);
		while ($arGroupSite = $rsGroupSite->Fetch())
			$arGroupSites[] = $arGroupSite["LID"];

		$userRole = CSocNetUserToGroup::GetUserRole($userID, $groupID);
		$bUserIsMember = ($userRole && in_array($userRole, array(SONET_ROLES_OWNER, SONET_ROLES_MODERATOR, SONET_ROLES_USER)));
		$bCanInitiate = ($GLOBALS["USER"]->IsAdmin() || CSocNetUser::IsCurrentUserModuleAdmin($arGroupSites) || ($userRole
			&& (($arGroup["INITIATE_PERMS"] == SONET_ROLES_OWNER && $userID == $arGroup["OWNER_ID"])
				|| ($arGroup["INITIATE_PERMS"] == SONET_ROLES_MODERATOR && in_array($userRole, array(SONET_ROLES_OWNER, SONET_ROLES_MODERATOR)))
				|| ($arGroup["INITIATE_PERMS"] == SONET_ROLES_USER && $bUserIsMember))));

		if (!$bCanInitiate)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_PERMS"), "ERROR_NO_PERMS");
			return false;
		}

		$bSuccess = true;
		for ($i = 0; $i < count($arRelationID); $i++)
		{
			$arRelationID[$i] = IntVal($arRelationID[$i]);
			if ($arRelationID[$i] <= 0)
				continue;

			$arRelation = CSocNetUserToGroup::GetByID($arRelationID[$i]);
			if (!$arRelation)
				continue;

			if ($arRelation["GROUP_ID"] != $groupID || $arRelation["INITIATED_BY_TYPE"] != SONET_INITIATED_BY_USER || $arRelation["ROLE"] != SONET_ROLES_REQUEST)
				continue;

			if (CSocNetUserToGroup::Delete($arRelation["ID"]))
			{
				$arMessageFields = array(
					"FROM_USER_ID" => $userID,
					"TO_USER_ID" => $arRelation["USER_ID"],
					"MESSAGE" => str_replace("#NAME#", $arGroup["NAME"], GetMessage("SONET_UG_REJECT_MEMBER_MESSAGE_G")),
					"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM
				);
				CSocNetMessages::Add($arMessageFields);
			}
			else
			{
				$errorMessage = "";
				if ($e = $APPLICATION->GetException())
					$errorMessage = $e->GetString();
				if (StrLen($errorMessage) <= 0)
					$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_USER2GROUP");

				$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_CONFIRM_MEMBER");
				$bSuccess = false;
			}
		}

		return $bSuccess;
	}

	function UserConfirmRequestToBeMember($targetUserID, $relationID, $bAutoSubscribe = true)
	{
		global $APPLICATION;

		$targetUserID = IntVal($targetUserID);
		if ($targetUserID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_SENDER_USER_ID");
			return false;
		}

		$relationID = IntVal($relationID);
		if ($relationID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_RELATIONID"), "ERROR_RELATION_ID");
			return false;
		}

		if (CModule::IncludeModule("extranet"))
			$extranet_site_id = CExtranet::GetExtranetSiteID();

		$dbResult = CSocNetUserToGroup::GetList(
			array(),
			array(
				"ID" => $relationID,
				"USER_ID" => $targetUserID,
				"ROLE" => SONET_ROLES_REQUEST,
				"INITIATED_BY_TYPE" => SONET_INITIATED_BY_GROUP
			),
			false,
			false,
			array("ID", "USER_ID", "INITIATED_BY_USER_ID", "GROUP_ID", "GROUP_VISIBLE", "GROUP_SITE_ID", "GROUP_NAME")
		);

		if ($arResult = $dbResult->Fetch())
		{
			$rsGroupSite = CSocNetGroup::GetSite($arResult["GROUP_ID"]);
			while ($arGroupSite = $rsGroupSite->Fetch())
			{
				$arGroupSites[] = $arGroupSite["LID"];

				//get server name
				$rsSites = CSite::GetByID($arGroupSite["LID"]);
				$arSite = $rsSites->Fetch();
				$serverName = $arSite["SERVER_NAME"];
				if (strlen($serverName) <= 0)
				{
					if (defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME) > 0)
						$serverName = SITE_SERVER_NAME;
					else
						$serverName = COption::GetOptionString("main", "server_name", $GLOBALS["SERVER_NAME"]);
				}

				if (strlen($serverName) > 0)
				{
					$protocol = (CMain::IsHTTPS() ? "https" : "http");
					$serverName = $protocol."://".$serverName;
				}
			}

			if (!$arGroupSites)
				$arGroupSites = array(SITE_ID);

			$arFields = array(
				"ROLE" => SONET_ROLES_USER,
				"=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			);
			if (CSocNetUserToGroup::Update($arResult["ID"], $arFields))
			{
				$events = GetModuleEvents("socialnetwork", "OnSocNetUserConfirmRequestToBeMember");
				while ($arEvent = $events->Fetch())
					ExecuteModuleEventEx($arEvent, array($arResult["ID"], $arResult));

				if ($bAutoSubscribe)
					CSocNetLogEvents::AutoSubscribe($targetUserID, SONET_ENTITY_GROUP, $arResult["GROUP_ID"]);

				if (CModule::IncludeModule("im"))
				{
					$arMessageFields = array(
						"MESSAGE_TYPE" => IM_MESSAGE_SYSTEM,
						"TO_USER_ID" => $arResult["INITIATED_BY_USER_ID"],
						"FROM_USER_ID" => $arResult['USER_ID'],
						"NOTIFY_TYPE" => IM_NOTIFY_FROM,
						"NOTIFY_MODULE" => "socialnetwork",
						"NOTIFY_EVENT" => "invite_group",
						"NOTIFY_TAG" => "SOCNET|INVITE_GROUP_SUCCESS|".intval($arResult["GROUP_ID"]),
						"NOTIFY_MESSAGE" => str_replace("#NAME#", $arResult["GROUP_NAME"], GetMessage("SONET_UG_CONFIRM_MEMBER_MESSAGE")),
					);
					CIMNotify::Add($arMessageFields);
				}
				else
				{
					$arMessageFields = array(
						"FROM_USER_ID" => $targetUserID,
						"TO_USER_ID" => $arResult["INITIATED_BY_USER_ID"],
						"MESSAGE" => str_replace("#NAME#", $arResult["GROUP_NAME"], GetMessage("SONET_UG_CONFIRM_MEMBER_MESSAGE")),
						"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
						"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM
					);
					CSocNetMessages::Add($arMessageFields);
				}

				$site = (in_array($extranet_site_id, $arGroupSites)) ? $extranet_site_id : $arGroupSites["0"];
				$groupUrl = $serverName.str_replace("#group_id#", $arResult["GROUP_ID"], COption::GetOptionString("socialnetwork", "group_path_template", "/workgroups/group/#group_id#/", $site));

				$logID = CSocNetLog::Add(
					array(
						"ENTITY_TYPE" => SONET_ENTITY_GROUP,
						"ENTITY_ID" => $arResult["GROUP_ID"],
						"EVENT_ID" => "system",
						"=LOG_DATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
						"TITLE_TEMPLATE" => false,
						"TITLE" => "join",
						"MESSAGE" => $targetUserID,
						"URL" => $groupUrl,
						"MODULE_ID" => false,
						"CALLBACK_FUNC" => false,
						"SITE_ID" => $arGroupSites,
						"USER_ID" => $targetUserID
					),
					false
				);
				if (intval($logID) > 0)
				{
					$tmpID = $logID;
					CSocNetLog::Update($logID, array("TMP_ID" => $tmpID));
					CSocNetLogRights::Add($logID, array("SA", "U".$targetUserID, "S".SONET_ENTITY_GROUP.$arResult["GROUP_ID"], "S".SONET_ENTITY_GROUP.$arResult["GROUP_ID"]."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$arResult["GROUP_ID"]."_".SONET_ROLES_MODERATOR, "S".SONET_ENTITY_GROUP.$arResult["GROUP_ID"]."_".SONET_ROLES_USER));
					CSocNetLog::SendEvent($logID, "SONET_NEW_EVENT", $tmpID);
				}
			}
			else
			{
				$errorMessage = "";
				if ($e = $APPLICATION->GetException())
					$errorMessage = $e->GetString();
				if (StrLen($errorMessage) <= 0)
					$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_USER2GROUP");

				$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_CREATE_RELATION");
				return false;
			}
		}
		else
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_NO_USER2GROUP"), "ERROR_NO_GROUP_REQUEST");
			return false;
		}

		CSocNetUserToGroup::__SpeedFileCheckMessages($targetUserID);

		return true;
	}

	function UserRejectRequestToBeMember($targetUserID, $relationID)
	{
		global $APPLICATION;

		$targetUserID = IntVal($targetUserID);
		if ($targetUserID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_SENDER_USER_ID");
			return false;
		}

		$relationID = IntVal($relationID);
		if ($relationID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_RELATIONID"), "ERROR_RELATION_ID");
			return false;
		}

		$dbResult = CSocNetUserToGroup::GetList(
			array(),
			array(
				"ID" => $relationID,
				"USER_ID" => $targetUserID,
				"ROLE" => SONET_ROLES_REQUEST,
				"INITIATED_BY_TYPE" => SONET_INITIATED_BY_GROUP
			),
			false,
			false,
			array("ID", "USER_ID", "GROUP_ID", "INITIATED_BY_USER_ID", "GROUP_NAME")
		);

		if ($arResult = $dbResult->Fetch())
		{
			if (CSocNetUserToGroup::Delete($arResult["ID"]))
			{
				$events = GetModuleEvents("socialnetwork", "OnSocNetUserRejectRequestToBeMember");
				while ($arEvent = $events->Fetch())
					ExecuteModuleEventEx($arEvent, array($arResult["ID"], $arResult));

				if (CModule::IncludeModule("im"))
				{
					$arMessageFields = array(
						"MESSAGE_TYPE" => IM_MESSAGE_SYSTEM,
						"TO_USER_ID" => $arResult["INITIATED_BY_USER_ID"],
						"FROM_USER_ID" => $arResult['USER_ID'],
						"NOTIFY_TYPE" => IM_NOTIFY_FROM,
						"NOTIFY_MODULE" => "socialnetwork",
						"NOTIFY_EVENT" => "invite_group",
						"NOTIFY_TAG" => "SOCNET|INVITE_GROUP_REJECT|".intval($arResult["GROUP_ID"]),
						"NOTIFY_MESSAGE" => str_replace("#NAME#", $arResult["GROUP_NAME"], GetMessage("SONET_UG_REJECT_MEMBER_MESSAGE")),
					);
					CIMNotify::Add($arMessageFields);
				}
				else
				{
					$arMessageFields = array(
						"FROM_USER_ID" => $targetUserID,
						"TO_USER_ID" => $arResult["INITIATED_BY_USER_ID"],
						"MESSAGE" => str_replace("#NAME#", $arResult["GROUP_NAME"], GetMessage("SONET_UG_REJECT_MEMBER_MESSAGE")),
						"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
						"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM
					);
					CSocNetMessages::Add($arMessageFields);
				}
			}
			else
			{
				$errorMessage = "";
				if ($e = $APPLICATION->GetException())
					$errorMessage = $e->GetString();
				if (StrLen($errorMessage) <= 0)
					$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_USER2GROUP");

				$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_DELETE_RELATION");
				return false;
			}
		}
		else
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_NO_USER2GROUP"), "ERROR_NO_MEMBER_REQUEST");
			return false;
		}

		CSocNetUserToGroup::__SpeedFileCheckMessages($targetUserID);

		return true;
	}

	function TransferModerator2Member($userID, $groupID, $arRelationID, $currentUserIsAdmin)
	{
		global $APPLICATION, $DB;

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_USERID");
			return false;
		}

		$groupID = IntVal($groupID);
		if ($groupID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_GROUPID"), "ERROR_GROUPID");
			return false;
		}

		if (!is_array($arRelationID))
			return true;

		$arGroup = CSocNetGroup::GetByID($groupID);
		if (!$arGroup || !is_array($arGroup))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_GROUP_ID"), "ERROR_NO_GROUP");
			return false;
		}

		$arUserPerms = CSocNetUserToGroup::InitUserPerms($userID, $arGroup, $currentUserIsAdmin);

		if (!$arUserPerms["UserCanModifyGroup"])
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_PERMS"), "ERROR_NO_PERMS");
			return false;
		}

		$bSuccess = true;
		$arSuccessID = array();
		for ($i = 0; $i < count($arRelationID); $i++)
		{
			$arRelationID[$i] = IntVal($arRelationID[$i]);
			if ($arRelationID[$i] <= 0)
				continue;

			$arRelation = CSocNetUserToGroup::GetByID($arRelationID[$i]);
			if (!$arRelation)
				continue;

			if ($arRelation["GROUP_ID"] != $groupID || $arRelation["ROLE"] != SONET_ROLES_MODERATOR)
				continue;

			$arFields = array(
				"ROLE" => SONET_ROLES_USER,
				"=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			);
			if (CSocNetUserToGroup::Update($arRelation["ID"], $arFields))
			{
				$arSuccessID[] = $arRelation["USER_ID"];

				$arMessageFields = array(
					"FROM_USER_ID" => $userID,
					"TO_USER_ID" => $arRelation["USER_ID"],
					"MESSAGE" => str_replace("#NAME#", $arGroup["NAME"], GetMessage("SONET_UG_MOD2MEMBER_MESSAGE")),
					"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM
				);
				CSocNetMessages::Add($arMessageFields);
			}
			else
			{
				$errorMessage = "";
				if ($e = $APPLICATION->GetException())
					$errorMessage = $e->GetString();
				if (StrLen($errorMessage) <= 0)
					$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_USER2GROUP");

				$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_MOD2MEMBER");
				$bSuccess = false;
			}
		}

		if (count($arSuccessID) > 0)
		{
			$logID = CSocNetLog::Add(
				array(
					"ENTITY_TYPE" => SONET_ENTITY_GROUP,
					"ENTITY_ID" => $groupID,
					"EVENT_ID" => "system",
					"=LOG_DATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"TITLE_TEMPLATE" => false,
					"TITLE" => "unmoderate",
					"MESSAGE" => implode(",", $arSuccessID),
					"URL" => false,
					"MODULE_ID" => false,
					"CALLBACK_FUNC" => false
				),
				false
			);
			if (intval($logID) > 0)
			{
				$arTmp = array();
				foreach($arSuccessID as $success_id)
					$arTmp[] = "U".$success_id;

				CSocNetLog::Update($logID, array("TMP_ID" => $logID));
				CSocNetLogRights::Add($logID, array_merge($arTmp, array("SA", "S".SONET_ENTITY_GROUP.$groupID, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_MODERATOR, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_USER)));
				CSocNetLog::SendEvent($logID, "SONET_NEW_EVENT", $logID);
			}
		}
		else
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_ERROR_MOD2MEM_INCORRECT_PARAMS"), "MOD2MEM_INCORRECT_PARAMS");
			$bSuccess = false;
		}

		return $bSuccess;
	}

	function TransferMember2Moderator($userID, $groupID, $arRelationID, $currentUserIsAdmin)
	{
		global $APPLICATION, $DB;

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_USERID");
			return false;
		}

		$groupID = IntVal($groupID);
		if ($groupID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_GROUPID"), "ERROR_GROUPID");
			return false;
		}

		if (!is_array($arRelationID))
			return true;

		$arGroup = CSocNetGroup::GetByID($groupID);
		if (!$arGroup || !is_array($arGroup))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_GROUP_ID"), "ERROR_NO_GROUP");
			return false;
		}

		$arUserPerms = CSocNetUserToGroup::InitUserPerms($userID, $arGroup, $currentUserIsAdmin);

		if (!$arUserPerms["UserCanModifyGroup"])
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_PERMS"), "ERROR_NO_PERMS");
			return false;
		}

		$bSuccess = true;
		$arSuccessID = array();
		for ($i = 0; $i < count($arRelationID); $i++)
		{
			$arRelationID[$i] = IntVal($arRelationID[$i]);
			if ($arRelationID[$i] <= 0)
				continue;

			$arRelation = CSocNetUserToGroup::GetByID($arRelationID[$i]);
			if (!$arRelation)
				continue;

			if ($arRelation["GROUP_ID"] != $groupID || $arRelation["ROLE"] != SONET_ROLES_USER)
				continue;

			$arFields = array(
				"ROLE" => SONET_ROLES_MODERATOR,
				"=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			);
			if (CSocNetUserToGroup::Update($arRelation["ID"], $arFields))
			{
				$arSuccessID[] = $arRelation["USER_ID"];

				$arMessageFields = array(
					"FROM_USER_ID" => $userID,
					"TO_USER_ID" => $arRelation["USER_ID"],
					"MESSAGE" => str_replace("#NAME#", $arGroup["NAME"], GetMessage("SONET_UG_MEMBER2MOD_MESSAGE")),
					"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM
				);
				CSocNetMessages::Add($arMessageFields);
			}
			else
			{
				$errorMessage = "";
				if ($e = $APPLICATION->GetException())
					$errorMessage = $e->GetString();
				if (StrLen($errorMessage) <= 0)
					$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_USER2GROUP");

				$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_MEMBER2MOD");
				$bSuccess = false;
			}
		}

		if (count($arSuccessID) > 0)
		{
			$logID = CSocNetLog::Add(
				array(
					"ENTITY_TYPE" => SONET_ENTITY_GROUP,
					"ENTITY_ID" => $groupID,
					"EVENT_ID" => "system",
					"=LOG_DATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"TITLE_TEMPLATE" => false,
					"TITLE" => "moderate",
					"MESSAGE" => implode(",", $arSuccessID),
					"URL" => false,
					"MODULE_ID" => false,
					"CALLBACK_FUNC" => false
				),
				false
			);
			if (intval($logID) > 0)
			{
				$arTmp = array();
				foreach($arSuccessID as $success_id)
					$arTmp[] = "U".$success_id;

				CSocNetLog::Update($logID, array("TMP_ID" => $logID));
				CSocNetLogRights::Add($logID, array_merge($arTmp, array("SA", "S".SONET_ENTITY_GROUP.$groupID, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_MODERATOR, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_USER)));
				CSocNetLog::SendEvent($logID, "SONET_NEW_EVENT", $logID);
			}
		}
		else
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_ERROR_MEM2MOD_INCORRECT_PARAMS"), "MEM2MOD_INCORRECT_PARAMS");
			$bSuccess = false;
		}

		return $bSuccess;
	}

	function BanMember($userID, $groupID, $arRelationID, $currentUserIsAdmin)
	{
		global $APPLICATION, $DB;

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_USERID");
			return false;
		}

		$groupID = IntVal($groupID);
		if ($groupID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_GROUPID"), "ERROR_GROUPID");
			return false;
		}

		if (!is_array($arRelationID))
			return true;

		$arGroup = CSocNetGroup::GetByID($groupID);
		if (!$arGroup || !is_array($arGroup))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_GROUP_ID"), "ERROR_NO_GROUP");
			return false;
		}

		$arUserPerms = CSocNetUserToGroup::InitUserPerms($userID, $arGroup, $currentUserIsAdmin);

		if (!$arUserPerms["UserCanModifyGroup"] && !$arUserPerms["UserCanModerateGroup"])
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_PERMS"), "ERROR_NO_PERMS");
			return false;
		}

		$bSuccess = true;
		for ($i = 0; $i < count($arRelationID); $i++)
		{
			$arRelationID[$i] = IntVal($arRelationID[$i]);
			if ($arRelationID[$i] <= 0)
				continue;

			$arRelation = CSocNetUserToGroup::GetByID($arRelationID[$i]);
			if (!$arRelation)
				continue;

			if ($arRelation["GROUP_ID"] != $groupID || $arRelation["ROLE"] != SONET_ROLES_USER)
				continue;

			$arFields = array(
				"ROLE" => SONET_ROLES_BAN,
				"=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			);
			if (CSocNetUserToGroup::Update($arRelation["ID"], $arFields))
			{
				$arMessageFields = array(
					"FROM_USER_ID" => $userID,
					"TO_USER_ID" => $arRelation["USER_ID"],
					"MESSAGE" => str_replace("#NAME#", $arGroup["NAME"], GetMessage("SONET_UG_BANMEMBER_MESSAGE")),
					"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM
				);
				CSocNetMessages::Add($arMessageFields);
			}
			else
			{
				$errorMessage = "";
				if ($e = $APPLICATION->GetException())
					$errorMessage = $e->GetString();
				if (StrLen($errorMessage) <= 0)
					$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_USER2GROUP");

				$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_BANMEMBER");
				$bSuccess = false;
			}
		}

		return $bSuccess;
	}

	function UnBanMember($userID, $groupID, $arRelationID, $currentUserIsAdmin)
	{
		global $APPLICATION, $DB;

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_USERID");
			return false;
		}

		$groupID = IntVal($groupID);
		if ($groupID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_GROUPID"), "ERROR_GROUPID");
			return false;
		}

		if (!is_array($arRelationID))
			return true;

		$arGroup = CSocNetGroup::GetByID($groupID);
		if (!$arGroup || !is_array($arGroup))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_GROUP_ID"), "ERROR_NO_GROUP");
			return false;
		}

		$arUserPerms = CSocNetUserToGroup::InitUserPerms($userID, $arGroup, $currentUserIsAdmin);

		if (!$arUserPerms["UserCanModifyGroup"] && !$arUserPerms["UserCanModerateGroup"])
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UG_ERROR_NO_PERMS"), "ERROR_NO_PERMS");
			return false;
		}

		$bSuccess = true;
		for ($i = 0; $i < count($arRelationID); $i++)
		{
			$arRelationID[$i] = IntVal($arRelationID[$i]);
			if ($arRelationID[$i] <= 0)
				continue;

			$arRelation = CSocNetUserToGroup::GetByID($arRelationID[$i]);
			if (!$arRelation)
				continue;

			if ($arRelation["GROUP_ID"] != $groupID || $arRelation["ROLE"] != SONET_ROLES_BAN)
				continue;

			$arFields = array(
				"ROLE" => SONET_ROLES_USER,
				"=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
			);
			if (CSocNetUserToGroup::Update($arRelation["ID"], $arFields))
			{
				$arMessageFields = array(
					"FROM_USER_ID" => $userID,
					"TO_USER_ID" => $arRelation["USER_ID"],
					"MESSAGE" => str_replace("#NAME#", $arGroup["NAME"], GetMessage("SONET_UG_UNBANMEMBER_MESSAGE")),
					"=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
					"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM
				);
				CSocNetMessages::Add($arMessageFields);
			}
			else
			{
				$errorMessage = "";
				if ($e = $APPLICATION->GetException())
					$errorMessage = $e->GetString();
				if (StrLen($errorMessage) <= 0)
					$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_USER2GROUP");

				$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_UNBANMEMBER");
				$bSuccess = false;
			}
		}

		return $bSuccess;
	}

	function SetOwner($userID, $groupID, $arGroup = false)
	{
		global $DB, $APPLICATION, $USER;

		if (!$arGroup)
			$arGroup = CSocNetGroup::GetByID($groupID);

		if (!$arGroup)
			return false;

		$DB->StartTransaction();
				
		// setting relations for the old owner
		$dbRelation = CSocNetUserToGroup::GetList(array(), array("USER_ID" => $arGroup["OWNER_ID"], "GROUP_ID" => $groupID), false, false, array("ID"));
		if ($arRelation = $dbRelation->Fetch())
		{
			$arFields = array(
				"ROLE" => SONET_ROLES_USER,
				"=DATE_UPDATE" => $DB->CurrentTimeFunction(),
				"INITIATED_BY_TYPE" => SONET_INITIATED_BY_USER,
				"INITIATED_BY_USER_ID" => $USER->GetID(),
			);

			if (!CSocNetUserToGroup::Update($arRelation["ID"], $arFields))
			{
				$errorMessage = "";
				if ($e = $APPLICATION->GetException())
					$errorMessage = $e->GetString();
				if (StrLen($errorMessage) <= 0)
					$errorMessage = GetMessage("SONET_UG_ERROR_CANNOT_UPDATE_CURRENT_OWNER");

				$APPLICATION->ThrowException($errorMessage, "ERROR_UPDATE_USER2GROUP");
				$DB->Rollback();
				return false;
			}
		}
		else
		{
			$errorMessage = "";
			if ($e = $APPLICATION->GetException())
				$errorMessage = $e->GetString();
			if (StrLen($errorMessage) <= 0)
				$errorMessage = GetMessage("SONET_UG_ERROR_CANNOT_GET_CURRENT_OWNER_RELATION");

			$APPLICATION->ThrowException($errorMessage, "ERROR_GET_USER2GROUP");
			$DB->Rollback();
			return false;
		}

		// delete requests to the old owner
		if (strlen($errorMessage) <= 0)
			CSocNetUserToGroup::__SpeedFileDelete($arGroup["OWNER_ID"]);

		if (strlen($errorMessage) <= 0)
		{
			// setting relations for the new owner
			$dbRelation = CSocNetUserToGroup::GetList(array(), array("USER_ID" => $userID, "GROUP_ID" => $groupID), false, false, array("ID"));
			if ($arRelation = $dbRelation->Fetch())
			{
				$arFields = array(
					"ROLE" => SONET_ROLES_OWNER,
					"=DATE_UPDATE" => $DB->CurrentTimeFunction(),
					"INITIATED_BY_TYPE" => SONET_INITIATED_BY_USER,
					"INITIATED_BY_USER_ID" => $USER->GetID(),
				);

				if (!CSocNetUserToGroup::Update($arRelation["ID"], $arFields))
				{
					$errorMessage = "";
					if ($e = $APPLICATION->GetException())
						$errorMessage = $e->GetString();
					if (StrLen($errorMessage) <= 0)
						$errorMessage = GetMessage("SONET_UG_ERROR_CANNOT_UPDATE_NEW_OWNER_RELATION");

					$APPLICATION->ThrowException($errorMessage, "ERROR_UPDATE_USER2GROUP");
					$DB->Rollback();
					return false;
				}
			}
			else
			{
				$arFields = array(
					"USER_ID" => $userID,
					"GROUP_ID" => $groupID,
					"ROLE" => SONET_ROLES_OWNER,
					"=DATE_CREATE" => $DB->CurrentTimeFunction(),
					"=DATE_UPDATE" => $DB->CurrentTimeFunction(),
					"INITIATED_BY_TYPE" => SONET_INITIATED_BY_USER,
					"INITIATED_BY_USER_ID" => $USER->GetID(),
					"MESSAGE" => false,
				);

				if (!CSocNetUserToGroup::Add($arFields))
				{
					$errorMessage = "";
					if ($e = $APPLICATION->GetException())
						$errorMessage = $e->GetString();
					if (StrLen($errorMessage) <= 0)
						$errorMessage = GetMessage("SONET_UG_ERROR_CANNOT_ADD_NEW_OWNER_RELATION");

					$APPLICATION->ThrowException($errorMessage, "ERROR_ADD_USER2GROUP");
					$DB->Rollback();
					return false;
				}
			}
		}

		if (strlen($errorMessage) <= 0)
		{
			$GROUP_ID = CSocNetGroup::Update($groupID, array("OWNER_ID" => $userID));
			if (!$GROUP_ID || IntVal($GROUP_ID) <= 0)
			{
				$errorMessage = "";
				if ($e = $APPLICATION->GetException())
					$errorMessage = $e->GetString();
				if (StrLen($errorMessage) <= 0)
					$errorMessage = GetMessage("SONET_UG_ERROR_CANNOT_UPDATE_GROUP");

				$APPLICATION->ThrowException($errorMessage, "ERROR_UPDATE_GROUP");
				$DB->Rollback();
				return false;
			}
		}

		// send message to the old owner
		$arMessageFields = array(
			"FROM_USER_ID" => $USER->GetID(),
			"TO_USER_ID" => $arGroup["OWNER_ID"],
			"MESSAGE" => str_replace("#NAME#", $arGroup["NAME"], GetMessage("SONET_UG_OWNER2MEMBER_MESSAGE")),
			"=DATE_CREATE" => $DB->CurrentTimeFunction(),
			"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM
		);
		CSocNetMessages::Add($arMessageFields);

		// send message to the new owner
		$arMessageFields = array(
			"FROM_USER_ID" => $USER->GetID(),
			"TO_USER_ID" => $userID,
			"MESSAGE" => str_replace("#NAME#", $arGroup["NAME"], GetMessage("SONET_UG_MEMBER2OWNER_MESSAGE")),
			"=DATE_CREATE" => $DB->CurrentTimeFunction(),
			"MESSAGE_TYPE" => SONET_MESSAGE_SYSTEM
		);
		CSocNetMessages::Add($arMessageFields);

		// add entry to log
		$logID = CSocNetLog::Add(
			array(
				"ENTITY_TYPE" => SONET_ENTITY_GROUP,
				"SITE_ID" => $arGroup["SITE_ID"],
				"ENTITY_ID" => $groupID,
				"EVENT_ID" => "system",
				"=LOG_DATE" => $DB->CurrentTimeFunction(),
				"TITLE_TEMPLATE" => false,
				"TITLE" => "owner",
				"MESSAGE" => $userID,
				"URL" => false,
				"MODULE_ID" => false,
				"CALLBACK_FUNC" => false,
				"USER_ID" => $userID,
			)
		);
		if (intval($logID) > 0)
		{
			CSocNetLog::Update($logID, array("TMP_ID" => $logID));
			CSocNetLogRights::Add($logID, array("SA", "S".SONET_ENTITY_GROUP.$groupID, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_MODERATOR, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_USER));
			CSocNetLog::SendEvent($logID, "SONET_NEW_EVENT", $logID);
		}

		if (strlen($errorMessage) <= 0)
		{
			$DB->Commit();
			return true;
		}
		else
		{
			$DB->Rollback();
			return false;	
		}
	}

	function DeleteRelation($userID, $groupID)
	{
		global $APPLICATION;

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_USERID"), "ERROR_USER_ID");
			return false;
		}

		$groupID = IntVal($groupID);
		if ($groupID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_UR_EMPTY_GROUPID"), "ERROR_GROUPID");
			return false;
		}

		$dbResult = CSocNetUserToGroup::GetList(
			array(),
			array(
				"GROUP_ID" => $groupID,
				"USER_ID" => $userID,
			),
			false,
			false,
			array("ID", "ROLE", "GROUP_VISIBLE")
		);

		if ($arResult = $dbResult->Fetch())
		{
			if ($arResult["ROLE"] != SONET_ROLES_USER && $arResult["ROLE"] != SONET_ROLES_MODERATOR)
				return false;

			if (CSocNetUserToGroup::Delete($arResult["ID"]))
			{
				$arGroupSiteID = array();
				$rsGroupSite = CSocNetGroup::GetSite($groupID);
				while($arGroupSite = $rsGroupSite->Fetch())
				{
					$arGroupSiteID[] = $arGroupSite["LID"];

					//get server name
					$rsSites = CSite::GetByID($arGroupSite["LID"]);
					$arSite = $rsSites->Fetch();
					$serverName = $arSite["SERVER_NAME"];
					if (strlen($serverName) <= 0)
					{
						if (defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME) > 0)
							$serverName = SITE_SERVER_NAME;
						else
							$serverName = COption::GetOptionString("main", "server_name", $GLOBALS["SERVER_NAME"]);
					}

					if (strlen($serverName) > 0)
					{
						$protocol = (CMain::IsHTTPS() ? "https" : "http");
						$serverName = $protocol."://".$serverName;
					}
				}

				$fullWorkgroupsUrl = $serverName.COption::GetOptionString("socialnetwork", "workgroups_page", false, $arGroupSiteID["0"]);

				$logID = CSocNetLog::Add(
					array(
						"ENTITY_TYPE" => SONET_ENTITY_GROUP,
						"ENTITY_ID" => $groupID,
						"EVENT_ID" => "system",
						"=LOG_DATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
						"TITLE_TEMPLATE" => false,
						"TITLE" => "unjoin",
						"MESSAGE" => $userID,
						"URL" => $fullWorkgroupsUrl,
						"MODULE_ID" => false,
						"CALLBACK_FUNC" => false,
						"USER_ID" => $userID,
						"SITE_ID" => $arGroupSiteID
					),
					false
				);
				if (intval($logID) > 0)
				{
					$tmpID = $logID;
					CSocNetLog::Update($logID, array("TMP_ID" => $tmpID));
					CSocNetLogRights::Add($logID, array("SA", "U".$userID, "S".SONET_ENTITY_GROUP.$groupID, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_MODERATOR, "S".SONET_ENTITY_GROUP.$groupID."_".SONET_ROLES_USER));
					CSocNetLog::SendEvent($logID, "SONET_NEW_EVENT", $tmpID);
				}
			}
			else
			{
				$errorMessage = "";
				if ($e = $APPLICATION->GetException())
					$errorMessage = $e->GetString();
				if (StrLen($errorMessage) <= 0)
					$errorMessage = GetMessage("SONET_UR_ERROR_CREATE_USER2GROUP");

				$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_DELETE_RELATION");
				return false;
			}
		}
		else
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_NO_USER2GROUP"), "ERROR_NO_MEMBER_REQUEST");
			return false;
		}

		CSocNetUserToGroup::__SpeedFileCheckMessages($userID);

		return true;
	}

	function InitUserPerms($userID, $arGroup, $bCurrentUserIsAdmin)
	{
		global $arSocNetAllowedInitiatePerms;
		global $arSocNetAllowedSpamPerms;

		$arReturn = array();

		$userID = IntVal($userID);
		$groupID = IntVal($arGroup["ID"]);
		$groupOwnerID = IntVal($arGroup["OWNER_ID"]);
		$groupInitiatePerms = Trim($arGroup["INITIATE_PERMS"]);
		$groupVisible = Trim($arGroup["VISIBLE"]);
		$groupOpened = Trim($arGroup["OPENED"]);
		$groupSpamPerms = Trim($arGroup["SPAM_PERMS"]);

		if ($groupID <= 0 || $groupOwnerID <= 0 || !in_array($groupInitiatePerms, $arSocNetAllowedInitiatePerms))
			return false;

		$arReturn["Operations"] = array();

		if (!in_array($groupSpamPerms, $arSocNetAllowedSpamPerms))
			$groupSpamPerms = "K";

		// UserRole - User role in group. False if user is not group member.
		// UserIsMember - True in user is group member.
		// UserIsOwner - True if user is group owner.
		// UserCanInitiate - True if user can invite friends to group.
		// UserCanViewGroup - True if user can view group.
		// UserCanAutoJoinGroup - True if user can join group automatically.
		// UserCanModifyGroup - True if user can modify group.
		// UserCanModerateGroup - True if user can moderate group.

		if ($userID <= 0)
		{
			$arReturn["UserRole"] = false;
			$arReturn["UserIsMember"] = false;
			$arReturn["UserIsOwner"] = false;
			$arReturn["UserCanInitiate"] = false;
			$arReturn["UserCanViewGroup"] = ($groupVisible == "Y");
			$arReturn["UserCanAutoJoinGroup"] = false;
			$arReturn["UserCanModifyGroup"] = false;
			$arReturn["UserCanModerateGroup"] = false;
			$arReturn["UserCanSpamGroup"] = false;
			$arReturn["InitiatedByType"] = false;
			$arReturn["Operations"]["viewsystemevents"] = false;
		}
		else
		{
			$arReturn["UserRole"] = CSocNetUserToGroup::GetUserRole($userID, $groupID);
			$arReturn["UserIsMember"] = ($arReturn["UserRole"]
				&& in_array($arReturn["UserRole"], array(SONET_ROLES_OWNER, SONET_ROLES_MODERATOR, SONET_ROLES_USER)));

			$arReturn["InitiatedByType"] = false;
			if ($arReturn["UserRole"] == SONET_ROLES_REQUEST)
			{
				$dbRelation = CSocNetUserToGroup::GetList(array(), array("USER_ID" => $userID, "GROUP_ID" => $groupID), false, false, array("INITIATED_BY_TYPE"));
				if ($arRelation = $dbRelation->Fetch())
					$arReturn["InitiatedByType"] = $arRelation["INITIATED_BY_TYPE"];
			}

			$arReturn["UserIsOwner"] = ($userID == $groupOwnerID);

			if ($bCurrentUserIsAdmin)
			{
				$arReturn["UserCanInitiate"] = true;
				$arReturn["UserCanViewGroup"] = true;
				$arReturn["UserCanAutoJoinGroup"] = true;
				$arReturn["UserCanModifyGroup"] = true;
				$arReturn["UserCanModerateGroup"] = true;
				$arReturn["UserCanSpamGroup"] = true;
				$arReturn["Operations"]["viewsystemevents"] = true;
			}
			else
			{
				if ($arReturn["UserIsMember"])
				{
					$arReturn["UserCanInitiate"] = (
						($groupInitiatePerms == SONET_ROLES_OWNER && $arReturn["UserIsOwner"])
						|| ($groupInitiatePerms == SONET_ROLES_MODERATOR
							&& in_array($arReturn["UserRole"], array(SONET_ROLES_OWNER, SONET_ROLES_MODERATOR)))
						|| ($groupInitiatePerms == SONET_ROLES_USER && $arReturn["UserIsMember"]));
					$arReturn["UserCanViewGroup"] = true;
					$arReturn["UserCanAutoJoinGroup"] = false;
					$arReturn["UserCanModifyGroup"] = $arReturn["UserIsOwner"];
					$arReturn["UserCanModerateGroup"] = (in_array($arReturn["UserRole"], array(SONET_ROLES_OWNER, SONET_ROLES_MODERATOR)));
					$arReturn["UserCanSpamGroup"] = (
						($groupSpamPerms == SONET_ROLES_OWNER && $arReturn["UserIsOwner"])
						|| ($groupSpamPerms == SONET_ROLES_MODERATOR
							&& in_array($arReturn["UserRole"], array(SONET_ROLES_OWNER, SONET_ROLES_MODERATOR)))
						|| ($groupSpamPerms == SONET_ROLES_USER && $arReturn["UserIsMember"])
						|| ($groupSpamPerms == SONET_ROLES_ALL));
					$arReturn["Operations"]["viewsystemevents"] = true;
				}
				else
				{
					$arReturn["UserCanInitiate"] = false;
					$arReturn["UserCanViewGroup"] = ($groupVisible == "Y");
					$arReturn["UserCanAutoJoinGroup"] = ($arReturn["UserCanViewGroup"] && ($groupOpened == "Y"));
					$arReturn["UserCanModifyGroup"] = false;
					$arReturn["UserCanModerateGroup"] = false;
					$arReturn["UserCanSpamGroup"] = ($groupSpamPerms == SONET_ROLES_ALL);
					$arReturn["Operations"]["viewsystemevents"] = false;
				}
			}
		}

		if (CModule::IncludeModule('extranet') && CExtranet::IsExtranetSite())
			$arReturn["UserCanSpamGroup"] = true;

		if (!CBXFeatures::IsFeatureEnabled("WebMessenger"))
			$arReturn["UserCanSpamGroup"] = false;



		return $arReturn;
	}

	function __SpeedFileCheckMessages($userID)
	{
		$userID = IntVal($userID);
		if ($userID <= 0)
			return;

		$cnt = 0;
		$dbResult = $GLOBALS["DB"]->Query(
			"SELECT COUNT(ID) as CNT ".
			"FROM b_sonet_user2group ".
			"WHERE USER_ID = ".$userID." ".
			"	AND ROLE = '".$GLOBALS["DB"]->ForSql(SONET_ROLES_REQUEST, 1)."' ".
			"	AND INITIATED_BY_TYPE = '".$GLOBALS["DB"]->ForSql(SONET_INITIATED_BY_GROUP, 1)."' "
		);
		if ($arResult = $dbResult->Fetch())
			$cnt = IntVal($arResult["CNT"]);

		if ($cnt > 0)
			CSocNetUserToGroup::__SpeedFileCreate($userID);
		else
			CSocNetUserToGroup::__SpeedFileDelete($userID);
	}

	function __SpeedFileCreate($userID)
	{
		global $CACHE_MANAGER;

		$userID = IntVal($userID);
		if ($userID <= 0)
			return;

		if ($CACHE_MANAGER->Read(86400*30, "socnet_cg_".$userID))
			$CACHE_MANAGER->Clean("socnet_cg_".$userID);

/*
		$filePath = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/managed_flags/socnet/c/".IntVal($userID / 1000)."/";
		$fileName = $userID."_g";

		if (!file_exists($filePath.$fileName))
		{
			CheckDirPath($filePath);
			@fclose(@fopen($filePath.$fileName, "w"));
		}
*/
	}

	function __SpeedFileDelete($userID)
	{
		global $CACHE_MANAGER;

		$userID = IntVal($userID);
		if ($userID <= 0)
			return;

		if (!$CACHE_MANAGER->Read(86400*30, "socnet_cg_".$userID))
			$CACHE_MANAGER->Set("socnet_cg_".$userID, true);

/*
		$fileName = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/managed_flags/socnet/c/".IntVal($userID / 1000)."/".$userID."_g";
		if (file_exists($fileName))
			@unlink($fileName);
*/
	}

	function SpeedFileExists($userID)
	{
		global $CACHE_MANAGER;

		$userID = IntVal($userID);
		if ($userID <= 0)
			return;

		return (!$CACHE_MANAGER->Read(86400*30, "socnet_cg_".$userID));
	}

	/* Module IM callback */
	function OnBeforeConfirmNotify($module, $tag, $value, $arParams)
	{
		if ($module == "socialnetwork")
		{
			$arTag = explode("|", $tag);
			if (count($arTag) == 4 && $arTag[1] == 'INVITE_GROUP')
			{
				if ($value == 'Y')
				{
					self::UserConfirmRequestToBeMember($arTag[2], $arTag[3]);
					return true;
				}
				else
				{
					self::UserRejectRequestToBeMember($arTag[2], $arTag[3]);
					return true;
				}
			}
		}
	}
}
?>