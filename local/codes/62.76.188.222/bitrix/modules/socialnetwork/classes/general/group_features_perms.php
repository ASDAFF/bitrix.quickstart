<?
IncludeModuleLangFile(__FILE__);

$GLOBALS["arSonetFeaturesPermsCache"] = array();

class CAllSocNetFeaturesPerms
{
	/***************************************/
	/********  DATA MODIFICATION  **********/
	/***************************************/
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		global $DB, $arSocNetFeaturesSettings, $arSocNetAllowedRolesForFeaturesPerms, $arSocNetAllowedEntityTypes, $arSocNetAllowedRelationsType;

		if ($ACTION != "ADD" && IntVal($ID) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("System error 870164", "ERROR");
			return false;
		}

		if ((is_set($arFields, "FEATURE_ID") || $ACTION=="ADD") && IntVal($arFields["FEATURE_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GFP_EMPTY_GROUP_FEATURE_ID"), "EMPTY_FEATURE_ID");
			return false;
		}
		elseif (is_set($arFields, "FEATURE_ID"))
		{
			$arResult = CSocNetFeatures::GetByID($arFields["FEATURE_ID"]);
			if ($arResult == false)
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["FEATURE_ID"], GetMessage("SONET_GFP_ERROR_NO_GROUP_FEATURE_ID")), "ERROR_NO_FEATURE_ID");
				return false;
			}
		}

		$groupFeature = "";
		$groupFeatureType = "";

		if ((is_set($arFields, "OPERATION_ID") || $ACTION=="ADD") && StrLen($arFields["OPERATION_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GFP_EMPTY_OPERATION_ID"), "EMPTY_OPERATION_ID");
			return false;
		}
		elseif (is_set($arFields, "OPERATION_ID"))
		{
			$arFields["OPERATION_ID"] = strtolower($arFields["OPERATION_ID"]);

			if (is_set($arFields, "FEATURE_ID"))
			{
				$arGroupFeature = CSocNetFeatures::GetByID($arFields["FEATURE_ID"]);
				if ($arGroupFeature != false)
				{
					$groupFeature = $arGroupFeature["FEATURE"];
					$groupFeatureType = $arGroupFeature["ENTITY_TYPE"];
				}
			}
			elseif ($ACTION != "ADD" && IntVal($ID) > 0)
			{
				$dbGroupFeature = CSocNetFeaturesPerms::GetList(
					array(),
					array("ID" => $ID),
					false,
					false,
					array("FEATURE_FEATURE", "FEATURE_ENTITY_TYPE")
				);
				if ($arGroupFeature = $dbGroupFeature->Fetch())
				{
					$groupFeature = $arGroupFeature["FEATURE_FEATURE"];
					$groupFeatureType = $arGroupFeature["FEATURE_ENTITY_TYPE"];
				}
			}
			if (StrLen($groupFeature) <= 0 || !array_key_exists($groupFeature, $arSocNetFeaturesSettings))
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GFP_BAD_OPERATION_ID"), "BAD_OPERATION_ID");
				return false;
			}

			if (!array_key_exists($arFields["OPERATION_ID"], $arSocNetFeaturesSettings[$groupFeature]["operations"]))
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GFP_NO_OPERATION_ID"), "NO_OPERATION_ID");
				return false;
			}
		}

		if ((is_set($arFields, "ROLE") || $ACTION=="ADD") && strlen($arFields["ROLE"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GFP_EMPTY_ROLE"), "EMPTY_ROLE");
			return false;
		}
		elseif (is_set($arFields, "ROLE"))
		{
			if (StrLen($groupFeatureType) <= 0)
			{
				if (is_set($arFields, "FEATURE_ID"))
				{
					$arGroupFeature = CSocNetFeatures::GetByID($arFields["FEATURE_ID"]);
					if ($arGroupFeature != false)
					{
						$groupFeature = $arGroupFeature["FEATURE"];
						$groupFeatureType = $arGroupFeature["ENTITY_TYPE"];
					}
				}
				elseif ($ACTION != "ADD" && IntVal($ID) > 0)
				{
					$dbGroupFeature = CSocNetFeaturesPerms::GetList(
						array(),
						array("ID" => $ID),
						false,
						false,
						array("FEATURE_FEATURE", "FEATURE_ENTITY_TYPE")
					);
					if ($arGroupFeature = $dbGroupFeature->Fetch())
					{
						$groupFeature = $arGroupFeature["FEATURE_FEATURE"];
						$groupFeatureType = $arGroupFeature["FEATURE_ENTITY_TYPE"];
					}
				}
			}
			if (StrLen($groupFeatureType) <= 0 || !in_array($groupFeatureType, $arSocNetAllowedEntityTypes))
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_ENTITY_TYPE"), "BAD_TYPE");
				return false;
			}
			if ($groupFeatureType == SONET_ENTITY_GROUP)
			{
				if (!in_array($arFields["ROLE"], $arSocNetAllowedRolesForFeaturesPerms))
				{
					$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["ROLE"], GetMessage("SONET_GFP_ERROR_NO_ROLE")), "ERROR_NO_SITE");
					return false;
				}
			}
			else
			{
				if (!in_array($arFields["ROLE"], $arSocNetAllowedRelationsType))
				{
					$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["ROLE"], GetMessage("SONET_GFP_ERROR_NO_ROLE")), "ERROR_NO_SITE");
					return false;
				}
			}
		}

		return True;
	}

	function Delete($ID)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($ID))
			return false;

		$ID = IntVal($ID);
		$bSuccess = True;

		$db_events = GetModuleEvents("socialnetwork", "OnBeforeSocNetFeaturesPermsDelete");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
				return false;

		$events = GetModuleEvents("socialnetwork", "OnSocNetFeaturesPermsDelete");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID));

		if ($bSuccess)
			$bSuccess = $DB->Query("DELETE FROM b_sonet_features2perms WHERE ID = ".$ID."", true);

		return $bSuccess;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($ID))
			return false;

		$ID = IntVal($ID);

		$arFields1 = array();
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1) == "=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}

		if (!CSocNetFeaturesPerms::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$db_events = GetModuleEvents("socialnetwork", "OnBeforeSocNetFeaturesPermsUpdate");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID, $arFields))===false)
				return false;

		$strUpdate = $DB->PrepareUpdate("b_sonet_features2perms", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($strUpdate) > 0)
				$strUpdate .= ", ";
			$strUpdate .= $key."=".$value." ";
		}

		if (strlen($strUpdate) > 0)
		{
			$strSql =
				"UPDATE b_sonet_features2perms SET ".
				"	".$strUpdate." ".
				"WHERE ID = ".$ID." ";
			$DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);

			$events = GetModuleEvents("socialnetwork", "OnSocNetFeaturesPermsUpdate");
			while ($arEvent = $events->Fetch())
				ExecuteModuleEventEx($arEvent, array($ID, $arFields));
		}
		else
		{
			$ID = False;
		}

		return $ID;
	}

	function SetPerm($featureID, $operation, $perm)
	{
		$featureID = IntVal($featureID);
		$operation = Trim($operation);
		$perm = Trim($perm);

		$dbResult = CSocNetFeaturesPerms::GetList(
			array(),
			array(
				"FEATURE_ID" => $featureID,
				"OPERATION_ID" => $operation,
			),
			false,
			false,
			array("ID", "FEATURE_ENTITY_TYPE", "FEATURE_ENTITY_ID", "FEATURE_FEATURE", "OPERATION_ID", "ROLE")
		);

		if ($arResult = $dbResult->Fetch())
			$r = CSocNetFeaturesPerms::Update($arResult["ID"], array("ROLE" => $perm));
		else
			$r = CSocNetFeaturesPerms::Add(array("FEATURE_ID" => $featureID, "OPERATION_ID" => $operation, "ROLE" => $perm));

		if (!$r)
		{
			$errorMessage = "";
			if ($e = $GLOBALS["APPLICATION"]->GetException())
				$errorMessage = $e->GetString();
			if (StrLen($errorMessage) <= 0)
				$errorMessage = GetMessage("SONET_GF_ERROR_SET").".";

			$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_SET_RECORD");
			return false;
		}
		else
		{

			if (!$arResult)
			{
				$arFeature = CSocNetFeatures::GetByID($featureID);
				$entity_type = $arFeature["ENTITY_TYPE"];
				$entity_id = $arFeature["ENTITY_ID"];
				$feature = $arFeature["FEATURE"];
			}
			else
			{
				$entity_type = $arResult["FEATURE_ENTITY_TYPE"];
				$entity_id = $arResult["FEATURE_ENTITY_ID"];
				$feature = $arResult["FEATURE_FEATURE"];
			}

			if(empty($arResult) || $arResult["ROLE"] != $perm)
			{
				if($arResult && ($arResult["ROLE"] != $perm))
					CSocNetSearch::SetFeaturePermissions($entity_type, $entity_id, $feature, $arResult["OPERATION_ID"], $perm);
				else
					CSocNetSearch::SetFeaturePermissions($entity_type, $entity_id, $feature, $operation, $perm);
			}

			if (
				!in_array($feature, array("tasks", "files", "blog"))
				&& is_array($GLOBALS["arSocNetFeaturesSettings"][$feature]["subscribe_events"]))
			{
				$arEventsTmp = array_keys($GLOBALS["arSocNetFeaturesSettings"][$feature]["subscribe_events"]);
				$rsLog = CSocNetLog::GetList(
					array(), 
					array(
						"ENTITY_TYPE" => $entity_type,
						"ENTITY_ID" => $entity_id,
						"EVENT_ID" => $arEventsTmp
					), 
					false, 
					false, 
					array("ID", "EVENT_ID")
				);
				while($arLog = $rsLog->Fetch())
				{
					CSocNetLogRights::DeleteByLogID($arLog["ID"]);
					CSocNetLogRights::SetForSonet(
						$arLog["ID"], 
						$entity_type, 
						$entity_id, 
						$feature, 
						$GLOBALS["arSocNetFeaturesSettings"][$feature]["subscribe_events"][$arLog["EVENT_ID"]]["OPERATION"]
					);
				}
			}

		}

		return $r;
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

		$dbResult = CSocNetFeaturesPerms::GetList(Array(), Array("ID" => $ID));
		if ($arResult = $dbResult->GetNext())
		{
			return $arResult;
		}

		return False;
	}

	/***************************************/
	/**********  COMMON METHODS  ***********/
	/***************************************/
	function CurrentUserCanPerformOperation($type, $id, $feature, $operation, $site_id = SITE_ID)
	{
		$userID = 0;
		if (is_object($GLOBALS["USER"]) && $GLOBALS["USER"]->IsAuthorized())
			$userID = IntVal($GLOBALS["USER"]->GetID());

		$bCurrentUserIsAdmin = CSocNetUser::IsCurrentUserModuleAdmin($site_id);

		return CSocNetFeaturesPerms::CanPerformOperation($userID, $type, $id, $feature, $operation, $bCurrentUserIsAdmin);
	}

	function CanPerformOperation($userID, $type, $id, $feature, $operation, $bCurrentUserIsAdmin = false)
	{
		global $arSocNetFeaturesSettings, $arSocNetAllowedEntityTypes;

		$userID = IntVal($userID);

		if ((is_array($id) && count($id) <= 0) || (!is_array($id) && $id <= 0))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_ENTITY_ID"), "ERROR_EMPTY_ENTITY_ID");
			return false;
		}

		$type = Trim($type);
		if ((StrLen($type) <= 0) || !in_array($type, $arSocNetAllowedEntityTypes))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_ENTITY_TYPE"), "ERROR_EMPTY_TYPE");
			return false;
		}

		$featureOperationPerms = CSocNetFeaturesPerms::GetOperationPerm($type, $id, $feature, $operation);

		if ($type == SONET_ENTITY_GROUP)
		{
			if (is_array($id))
			{
				$arGroupToGet = array();
				foreach($id as $group_id)
				{
					if ($featureOperationPerms[$group_id] == false)
						$arReturn[$group_id] = false;
					else
						$arGroupToGet[] = $group_id;
				}

				$userRoleInGroup = CSocNetUserToGroup::GetUserRole($userID, $arGroupToGet);

				$arGroupToGet = array();
				if (is_array($userRoleInGroup))
				{
					foreach($userRoleInGroup as $group_id => $role)
					{
						if ($userRoleInGroup[$group_id] == SONET_ROLES_BAN)
							$arReturn[$group_id] = false;
						else
							$arGroupToGet[] = $group_id;
					}
				}

				if (
					(is_array($arGroupToGet) && count($arGroupToGet) <= 0)
					|| (!is_array($arGroupToGet) && intval($arGroupToGet) <= 0)
				)
				{
					$arReturn = array();
					foreach($id as $group_id)
						$arReturn[$group_id] = false;
					return $arReturn;
				}

				$resGroupTmp = CSocNetGroup::GetList(array("ID"=>"ASC"), array("ID"=>$arGroupToGet));
				while ($arGroupTmp = $resGroupTmp->Fetch())
				{
					if ($arGroupTmp["CLOSED"] == "Y" && !in_array($operation, $arSocNetFeaturesSettings[$feature]["minoperation"])):
						if (COption::GetOptionString("socialnetwork", "work_with_closed_groups", "N") != "Y")
						{
							$arReturn[$arGroupTmp["ID"]] = false;
							continue;
						}
						else
							$featureOperationPerms[$arGroupTmp["ID"]] = SONET_ROLES_OWNER;
					endif;

					if ($bCurrentUserIsAdmin)
					{
						$arReturn[$arGroupTmp["ID"]] = true;
						continue;
					}

					if ($featureOperationPerms[$arGroupTmp["ID"]] == SONET_ROLES_ALL)
					{
						if ($arGroupTmp["VISIBLE"] == "N")
							$featureOperationPerms[$arGroupTmp["ID"]] = SONET_ROLES_USER;
						else
						{
							$arReturn[$arGroupTmp["ID"]] = true;
							continue;
						}
					}

					if ($featureOperationPerms[$arGroupTmp["ID"]] == SONET_ROLES_AUTHORIZED)
					{
						if ($userID > 0)
						{
							$arReturn[$arGroupTmp["ID"]] = true;
							continue;
						}
						else
						{
							$arReturn[$arGroupTmp["ID"]] = false;
							continue;
						}
					}

					if ($userRoleInGroup[$arGroupTmp["ID"]] == false)
					{
						$arReturn[$arGroupTmp["ID"]] = false;
						continue;
					}

					if ($featureOperationPerms[$arGroupTmp["ID"]] == SONET_ROLES_MODERATOR)
					{
						if ($userRoleInGroup[$arGroupTmp["ID"]] == SONET_ROLES_MODERATOR || $userRoleInGroup[$arGroupTmp["ID"]] == SONET_ROLES_OWNER)
						{
							$arReturn[$arGroupTmp["ID"]] = true;
							continue;
						}
						else
						{
							$arReturn[$arGroupTmp["ID"]] = false;
							continue;
						}
					}
					elseif ($featureOperationPerms[$arGroupTmp["ID"]] == SONET_ROLES_USER)
					{
						if ($userRoleInGroup[$arGroupTmp["ID"]] == SONET_ROLES_MODERATOR || $userRoleInGroup[$arGroupTmp["ID"]] == SONET_ROLES_OWNER || $userRoleInGroup[$arGroupTmp["ID"]] == SONET_ROLES_USER)
						{
							$arReturn[$arGroupTmp["ID"]] = true;
							continue;
						}
						else
						{
							$arReturn[$arGroupTmp["ID"]] = false;
							continue;
						}
					}
					elseif ($featureOperationPerms[$arGroupTmp["ID"]] == SONET_ROLES_OWNER)
					{
						if ($userRoleInGroup[$arGroupTmp["ID"]] == SONET_ROLES_OWNER)
						{
							$arReturn[$arGroupTmp["ID"]] = true;
							continue;
						}
						else
						{
							$arReturn[$arGroupTmp["ID"]] = false;
							continue;
						}
					}
				}

				return $arReturn;

			}
			else // not array of groups
			{
				$group_id = IntVal($id);

				if ($featureOperationPerms == false)
					return false;

				$userRoleInGroup = CSocNetUserToGroup::GetUserRole($userID, $id);
				if ($userRoleInGroup == SONET_ROLES_BAN)
					return false;

				$arGroupTmp = CSocNetGroup::GetByID($id);

				if ($arGroupTmp["CLOSED"] == "Y" && !in_array($operation, $arSocNetFeaturesSettings[$feature]["minoperation"])):
					if (COption::GetOptionString("socialnetwork", "work_with_closed_groups", "N") != "Y")
						return false;
					else
						$featureOperationPerms = SONET_ROLES_OWNER;
				endif;


				if ($bCurrentUserIsAdmin)
					return true;

				if ($featureOperationPerms == SONET_ROLES_ALL)
				{
					if ($arGroupTmp["VISIBLE"] == "N")
						$featureOperationPerms = SONET_ROLES_USER;
					else
						return true;
				}

				if ($featureOperationPerms == SONET_ROLES_AUTHORIZED)
				{
					if ($userID > 0)
						return true;
					else
						return false;
				}

				if ($userRoleInGroup == false)
					return false;

				if ($featureOperationPerms == SONET_ROLES_MODERATOR)
				{
					if ($userRoleInGroup == SONET_ROLES_MODERATOR || $userRoleInGroup == SONET_ROLES_OWNER)
						return true;
					else
						return false;
				}
				elseif ($featureOperationPerms == SONET_ROLES_USER)
				{
					if ($userRoleInGroup == SONET_ROLES_MODERATOR || $userRoleInGroup == SONET_ROLES_OWNER || $userRoleInGroup == SONET_ROLES_USER)
						return true;
					else
						return false;
				}
				elseif ($featureOperationPerms == SONET_ROLES_OWNER)
				{
					if ($userRoleInGroup == SONET_ROLES_OWNER)
						return true;
					else
						return false;
				}
			}
		}
		else // user
		{
			if (is_array($id))
			{

				foreach($id as $entity_id)
				{

					if ($featureOperationPerms[$entity_id] == false)
					{
						$arReturn[$entity_id] = false;
						continue;
					}

					$usersRelation = CSocNetUserRelations::GetRelation($userID, $entity_id);

					if ($type == SONET_ENTITY_USER && $userID == $entity_id)
					{
						$arReturn[$entity_id] = true;
						continue;
					}

					if ($bCurrentUserIsAdmin)
					{
						$arReturn[$entity_id] = true;
						continue;
					}

					if ($userID == $entity_id)
					{
						$arReturn[$entity_id] = true;
						continue;
					}

					if ($usersRelation == SONET_RELATIONS_BAN)
					{
						$arReturn[$entity_id] = false;
						continue;
					}

					if ($featureOperationPerms[$entity_id] == SONET_RELATIONS_TYPE_NONE)
					{
						$arReturn[$entity_id] = false;
						continue;
					}

					if ($featureOperationPerms[$entity_id] == SONET_RELATIONS_TYPE_ALL)
					{
						$arReturn[$entity_id] = true;
						continue;
					}

					if ($featureOperationPerms[$entity_id] == SONET_RELATIONS_TYPE_AUTHORIZED)
					{
						if ($userID > 0)
							$arReturn[$entity_id] = true;
						else
							$arReturn[$entity_id] = false;
						continue;
					}

					if ($featureOperationPerms[$entity_id] == SONET_RELATIONS_TYPE_FRIENDS)
					{
						if (CSocNetUserRelations::IsFriends($userID, $entity_id))
						{
							$arReturn[$entity_id] = true;
							continue;
						}
						else
						{
							$arReturn[$entity_id] = false;
							continue;
						}
					}

					if ($featureOperationPerms[$entity_id] == SONET_RELATIONS_TYPE_FRIENDS2)
					{
						if (CSocNetUserRelations::IsFriends($userID, $entity_id))
						{
							$arReturn[$entity_id] = true;
							continue;
						}
						elseif (CSocNetUserRelations::IsFriends2($userID, $entity_id))
						{
							$arReturn[$entity_id] = true;
							continue;
						}
						else
						{
							$arReturn[$entity_id] = false;
							continue;
						}
					}
				}

				return $arReturn;
			}
			else // not array
			{

				if ($featureOperationPerms == false)
					return false;

				if ($type == SONET_ENTITY_USER && $userID == $id)
					return true;

				if ($bCurrentUserIsAdmin)
					return true;

				if ($userID == $id)
					return true;

				$usersRelation = CSocNetUserRelations::GetRelation($userID, $id);
				if ($usersRelation == SONET_RELATIONS_BAN)
					return false;

				if ($featureOperationPerms == SONET_RELATIONS_TYPE_NONE)
					return false;

				if ($featureOperationPerms == SONET_RELATIONS_TYPE_ALL)
					return true;

				if ($featureOperationPerms == SONET_RELATIONS_TYPE_AUTHORIZED)
				{
					if ($userID > 0)
						return true;
					else
						return false;
				}

				if ($featureOperationPerms == SONET_RELATIONS_TYPE_FRIENDS)
				{
					if (CSocNetUserRelations::IsFriends($userID, $id))
						return true;
					else
						return false;
				}

				if ($featureOperationPerms == SONET_RELATIONS_TYPE_FRIENDS2)
				{
					if (CSocNetUserRelations::IsFriends($userID, $id))
						return true;
					elseif (CSocNetUserRelations::IsFriends2($userID, $id))
						return true;
					else
						return false;
				}
			}

		}

		return false;
	}

	function GetOperationPerm($type, $id, $feature, $operation)
	{
		global $arSocNetFeaturesSettings, $arSocNetAllowedEntityTypes;

		$type = Trim($type);
		if ((StrLen($type) <= 0) || !in_array($type, $arSocNetAllowedEntityTypes))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_ENTITY_TYPE"), "ERROR_EMPTY_TYPE");
			if (is_array($id))
			{
				$arReturn = array();
				foreach($id as $TmpGroupID)
					$arReturn[$TmpGroupID] = false;
				return $arReturn;
			}
			else
				return false;
		}

		$feature = StrToLower(Trim($feature));
		if (StrLen($feature) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_FEATURE_ID"), "ERROR_EMPTY_FEATURE_ID");
			if (is_array($id))
			{
				$arReturn = array();
				foreach($id as $TmpGroupID)
					$arReturn[$TmpGroupID] = false;
				return $arReturn;
			}
			else
				return false;
		}

		if (
			!array_key_exists($feature, $arSocNetFeaturesSettings) 
			|| !array_key_exists("allowed", $arSocNetFeaturesSettings[$feature])
			|| !in_array($type, $arSocNetFeaturesSettings[$feature]["allowed"]))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_FEATURE_ID"), "ERROR_NO_FEATURE_ID");
			if (is_array($id))
			{
				$arReturn = array();
				foreach($id as $TmpGroupID)
					$arReturn[$TmpGroupID] = false;
				return $arReturn;
			}
			else
				return false;
		}

		$operation = StrToLower(Trim($operation));
		if (
			!array_key_exists("operations", $arSocNetFeaturesSettings[$feature])
			|| !array_key_exists($operation, $arSocNetFeaturesSettings[$feature]["operations"])
		)
		{
			if (is_array($id))
			{
				$arReturn = array();
				foreach($id as $TmpGroupID)
					$arReturn[$TmpGroupID] = false;
				return $arReturn;
			}
			else
				return false;
		}

		global $arSonetFeaturesPermsCache;
		if (!isset($arSonetFeaturesPermsCache) || !is_array($arSonetFeaturesPermsCache))
			$arSonetFeaturesPermsCache = array();

		if (is_array($id))
		{
			$arFeaturesPerms = array();
			$arGroupToGet = array();
			foreach($id as $TmpGroupID)
			{
				$arFeaturesPerms[$TmpGroupID] = array();

				if (!array_key_exists($type."_".$TmpGroupID, $arSonetFeaturesPermsCache))
					$arGroupToGet[] = $TmpGroupID;
				else
					$arFeaturesPerms[$TmpGroupID] = $arSonetFeaturesPermsCache[$type."_".$TmpGroupID];
			}

			$dbResult = CSocNetFeaturesPerms::GetList(
				Array(),
				Array("FEATURE_ENTITY_ID" => $arGroupToGet, "FEATURE_ENTITY_TYPE" => $type, "GROUP_FEATURE_ACTIVE" => "Y"),
				false,
				false,
				array("OPERATION_ID", "FEATURE_ENTITY_ID", "FEATURE_FEATURE", "ROLE")
			);
			while ($arResult = $dbResult->Fetch())
			{
				if (!array_key_exists($arResult["FEATURE_ENTITY_ID"], $arFeaturesPerms) || !array_key_exists($arResult["FEATURE_FEATURE"], $arFeaturesPerms[$arResult["FEATURE_ENTITY_ID"]]))
					$arFeaturesPerms[$arResult["FEATURE_ENTITY_ID"]][$arResult["FEATURE_FEATURE"]] = array();
				$arFeaturesPerms[$arResult["FEATURE_ENTITY_ID"]][$arResult["FEATURE_FEATURE"]][$arResult["OPERATION_ID"]] = $arResult["ROLE"];
			}

			$arReturn = array();

			foreach($id as $TmpEntityID)
			{
				$arSonetFeaturesPermsCache[$type."_".$TmpGroupID] = $arFeaturesPerms[$TmpEntityID];

				if ($type == SONET_ENTITY_GROUP)
				{
					$featureOperationPerms = SONET_ROLES_OWNER;

					if (!array_key_exists($feature, $arFeaturesPerms[$TmpEntityID]))
						$featureOperationPerms = $arSocNetFeaturesSettings[$feature]["operations"][$operation][SONET_ENTITY_GROUP];
					elseif (!array_key_exists($operation, $arFeaturesPerms[$TmpEntityID][$feature]))
						$featureOperationPerms = $arSocNetFeaturesSettings[$feature]["operations"][$operation][SONET_ENTITY_GROUP];
					else
						$featureOperationPerms = $arFeaturesPerms[$TmpEntityID][$feature][$operation];
				}
				else
				{
					$featureOperationPerms = SONET_RELATIONS_TYPE_NONE;

					if (!array_key_exists($feature, $arFeaturesPerms[$TmpEntityID]))
						$featureOperationPerms = $arSocNetFeaturesSettings[$feature]["operations"][$operation][SONET_ENTITY_USER];
					elseif (!array_key_exists($operation, $arFeaturesPerms[$TmpEntityID][$feature]))
						$featureOperationPerms = $arSocNetFeaturesSettings[$feature]["operations"][$operation][SONET_ENTITY_USER];
					else
						$featureOperationPerms = $arFeaturesPerms[$TmpEntityID][$feature][$operation];
				}

				$arReturn[$TmpEntityID] = $featureOperationPerms;

			}

			return $arReturn;

		}
		else // not array
		{
			$id = IntVal($id);
			if ($id <= 0)
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_ENTITY_ID"), "ERROR_EMPTY_ENTITY_ID");
				return false;
			}

			$arFeaturesPerms = array();
			if (array_key_exists($type."_".$id, $arSonetFeaturesPermsCache))
			{
				$arFeaturesPerms = $arSonetFeaturesPermsCache[$type."_".$id];
			}
			else
			{
				$dbResult = CSocNetFeaturesPerms::GetList(
					Array(),
					Array("FEATURE_ENTITY_ID" => $id, "FEATURE_ENTITY_TYPE" => $type, "GROUP_FEATURE_ACTIVE" => "Y"),
					false,
					false,
					array("OPERATION_ID", "FEATURE_FEATURE", "ROLE")
				);
				while ($arResult = $dbResult->Fetch())
				{
					if (!array_key_exists($arResult["FEATURE_FEATURE"], $arFeaturesPerms))
						$arFeaturesPerms[$arResult["FEATURE_FEATURE"]] = array();
					$arFeaturesPerms[$arResult["FEATURE_FEATURE"]][$arResult["OPERATION_ID"]] = $arResult["ROLE"];
				}
				$arSonetFeaturesPermsCache[$type."_".$id] = $arFeaturesPerms;
			}

			if ($type == SONET_ENTITY_GROUP)
			{
				$featureOperationPerms = SONET_ROLES_OWNER;

				if (!array_key_exists($feature, $arFeaturesPerms))
					$featureOperationPerms = $arSocNetFeaturesSettings[$feature]["operations"][$operation][SONET_ENTITY_GROUP];
				elseif (!array_key_exists($operation, $arFeaturesPerms[$feature]))
					$featureOperationPerms = $arSocNetFeaturesSettings[$feature]["operations"][$operation][SONET_ENTITY_GROUP];
				else
					$featureOperationPerms = $arFeaturesPerms[$feature][$operation];
			}
			else
			{
				$featureOperationPerms = SONET_RELATIONS_TYPE_NONE;

				if (!array_key_exists($feature, $arFeaturesPerms))
					$featureOperationPerms = $arSocNetFeaturesSettings[$feature]["operations"][$operation][SONET_ENTITY_USER];
				elseif (!array_key_exists($operation, $arFeaturesPerms[$feature]))
					$featureOperationPerms = $arSocNetFeaturesSettings[$feature]["operations"][$operation][SONET_ENTITY_USER];
				else
					$featureOperationPerms = $arFeaturesPerms[$feature][$operation];
			}

			return $featureOperationPerms;

		}

	}
}
?>