<?
class CSocNetLogRights
{

	function Add($LOG_ID, $GROUP_CODE)
	{
		global $DB;

		if (is_array($GROUP_CODE))
		{
			foreach($GROUP_CODE as $GROUP_CODE_TMP)
				CSocNetLogRights::Add($LOG_ID, $GROUP_CODE_TMP);
			return false;
		}
		else
		{
			$db_events = GetModuleEvents("socialnetwork", "OnBeforeSocNetLogRightsAdd");
			while ($arEvent = $db_events->Fetch())
				if (ExecuteModuleEventEx($arEvent, array($LOG_ID, &$GROUP_CODE))===false)
					return false;

			$NEW_RIGHT_ID = $DB->Add("b_sonet_log_right", array(
				"LOG_ID" => $LOG_ID,
				"GROUP_CODE" => $GROUP_CODE,
			));

			return $NEW_RIGHT_ID;
		}
	}

	function Update($RIGHT_ID, $GROUP_CODE)
	{
		global $DB;
		$RIGHT_ID = intval($RIGHT_ID);

		if (is_array($GROUP_CODE))
		{
			foreach($GROUP_CODE as $GROUP_CODE_TMP)
				CSocNetLogRights::Update($RIGHT_ID, $GROUP_CODE_TMP);
			return false;
		}
		else
		{
			$db_events = GetModuleEvents("socialnetwork", "OnBeforeSocNetLogRightsUpdate");
			while ($arEvent = $db_events->Fetch())
				if (ExecuteModuleEventEx($arEvent, array($RIGHT_ID, &$GROUP_CODE))===false)
					return false;

			$strUpdate = $DB->PrepareUpdate("b_sonet_log_right", array(
				"GROUP_CODE" => $GROUP_CODE
			));
			$DB->Query("UPDATE b_sonet_log_right SET ".$strUpdate." WHERE ID = ".$RIGHT_ID);
			return $RIGHT_ID;
		}
	}

	function Delete($RIGHT_ID)
	{
		global $DB;
		$RIGHT_ID = intval($RIGHT_ID);
		$DB->Query("DELETE FROM b_sonet_log_right WHERE ID = ".$RIGHT_ID);
	}

	function DeleteByLogID($LOG_ID)
	{
		global $DB;
		$LOG_ID = intval($LOG_ID);
		$DB->Query("DELETE FROM b_sonet_log_right WHERE LOG_ID = ".$LOG_ID);
	}

	function GetList($aSort=array(), $aFilter=array())
	{
		global $DB;

		$arFilter = array();
		foreach($aFilter as $key=>$val)
		{
			$val = $DB->ForSql($val);
			if(strlen($val)<=0)
				continue;
			switch(strtoupper($key))
			{
				case "ID":
					$arFilter[] = "R.ID=".intval($val);
					break;
				case "LOG_ID":
					$arFilter[] = "R.LOG_ID=".intval($val);
					break;
				case "GROUP_CODE":
					$arFilter[] = "R.GROUP_CODE='".$val."'";
					break;
			}
		}

		$arOrder = array();
		foreach($aSort as $key=>$val)
		{
			$ord = (strtoupper($val) <> "ASC"?"DESC":"ASC");
			switch(strtoupper($key))
			{
				case "ID":
					$arOrder[] = "R.ID ".$ord;
					break;
				case "LOG_ID":
					$arOrder[] = "R.LOG_ID ".$ord;
					break;
				case "GROUP_CODE":
					$arOrder[] = "R.GROUP_CODE ".$ord;
					break;
			}
		}
		if(count($arOrder) == 0)
			$arOrder[] = "R.ID DESC";
		$sOrder = "\nORDER BY ".implode(", ",$arOrder);

		if(count($arFilter) == 0)
			$sFilter = "";
		else
			$sFilter = "\nWHERE ".implode("\nAND ", $arFilter);

		$strSql = "
			SELECT
				R.ID
				,R.LOG_ID
				,R.GROUP_CODE
			FROM
				b_sonet_log_right R
			".$sFilter.$sOrder;

		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	function SetForSonet($logID, $entity_type, $entity_id, $feature, $operation, $bNew = false)
	{
		$bFlag = true;
		if (!$bNew)
		{
			$rsRights = CSocNetLogRights::GetList(array(), array("LOG_ID" => $logID));
			if ($arRights = $rsRights->Fetch())
				$bFlag = false;
		}

		if ($bFlag)
		{
			$bExtranet = false;
			$perm = CSocNetFeaturesPerms::GetOperationPerm($entity_type, $entity_id, $feature, $operation);
			if ($perm)
			{
				if (CModule::IncludeModule("extranet") && $extranet_site_id = CExtranet::GetExtranetSiteID())
				{
					$arLogSites = array();
					$rsLogSite = CSocNetLog::GetSite($logID);
					while($arLogSite = $rsLogSite->Fetch())
						$arLogSites[] = $arLogSite["LID"];

					if (in_array($extranet_site_id, $arLogSites))
						$bExtranet = true;
				}

				if ($bExtranet)
				{
					if ($entity_type == SONET_ENTITY_GROUP && $perm == SONET_ROLES_OWNER)
						CSocNetLogRights::Add($logID, array("SA", "S".SONET_ENTITY_GROUP.$entity_id, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_OWNER));
					elseif ($entity_type == SONET_ENTITY_GROUP && $perm == SONET_ROLES_MODERATOR)
						CSocNetLogRights::Add($logID, array("SA", "S".SONET_ENTITY_GROUP.$entity_id, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_MODERATOR));
					elseif ($entity_type == SONET_ENTITY_GROUP && in_array($perm, array(SONET_ROLES_USER, SONET_ROLES_AUTHORIZED, SONET_ROLES_ALL)))
						CSocNetLogRights::Add($logID, array("SA", "S".SONET_ENTITY_GROUP.$entity_id, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_MODERATOR, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_USER));
					elseif ($entity_type == SONET_ENTITY_USER && $perm == SONET_RELATIONS_TYPE_NONE)
						CSocNetLogRights::Add($logID, array("SA", "U".$entity_id));
					elseif ($entity_type == SONET_ENTITY_USER && in_array($perm, array(SONET_RELATIONS_TYPE_FRIENDS, SONET_RELATIONS_TYPE_FRIENDS2, SONET_RELATIONS_TYPE_AUTHORIZED, SONET_RELATIONS_TYPE_ALL)))
					{
						$arCode = array("SA");
						$arLog = CSocNetLog::GetByID($logID);
						if ($arLog)
						{
							$dbUsersInGroup = CSocNetUserToGroup::GetList(
								array(),
								array(
									"USER_ID" => $arLog["USER_ID"],
									"<=ROLE" => SONET_ROLES_USER,
									"GROUP_SITE_ID" => $extranet_site_id,
									"GROUP_ACTIVE" => "Y"
								),
								false,
								false,
								array("ID", "GROUP_ID")
							);
							while ($arUsersInGroup = $dbUsersInGroup->Fetch())
								if (!in_array("S".SONET_ENTITY_GROUP.$arUsersInGroup["GROUP_ID"]."_".SONET_ROLES_USER, $arCode))
									$arCode = array_merge(
										$arCode,
										array(
											"S".SONET_ENTITY_GROUP.$arUsersInGroup["GROUP_ID"]."_".SONET_ROLES_OWNER,
											"S".SONET_ENTITY_GROUP.$arUsersInGroup["GROUP_ID"]."_".SONET_ROLES_MODERATOR,
											"S".SONET_ENTITY_GROUP.$arUsersInGroup["GROUP_ID"]."_".SONET_ROLES_USER
										)
									);

							CSocNetLogRights::Add($logID, $arCode);
						}
					}
				}
				else
				{
					if ($entity_type == SONET_ENTITY_GROUP && $perm == SONET_ROLES_OWNER)
						CSocNetLogRights::Add($logID, array("SA", "S".SONET_ENTITY_GROUP.$entity_id, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_OWNER));
					elseif ($entity_type == SONET_ENTITY_GROUP && $perm == SONET_ROLES_MODERATOR)
						CSocNetLogRights::Add($logID, array("SA", "S".SONET_ENTITY_GROUP.$entity_id, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_MODERATOR));
					elseif ($entity_type == SONET_ENTITY_GROUP && $perm == SONET_ROLES_USER)
						CSocNetLogRights::Add($logID, array("SA", "S".SONET_ENTITY_GROUP.$entity_id, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_OWNER, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_MODERATOR, "S".SONET_ENTITY_GROUP.$entity_id."_".SONET_ROLES_USER));
					elseif ($entity_type == SONET_ENTITY_USER && in_array($perm, array(SONET_RELATIONS_TYPE_FRIENDS, SONET_RELATIONS_TYPE_FRIENDS2)))
						CSocNetLogRights::Add($logID, array("SA", "U".$entity_id, "S".$entity_type.$entity_id."_".$perm));
					elseif ($entity_type == SONET_ENTITY_USER && $perm == SONET_RELATIONS_TYPE_NONE)
						CSocNetLogRights::Add($logID, array("SA", "U".$entity_id));
					elseif ($entity_type == SONET_ENTITY_GROUP && $perm == SONET_ROLES_AUTHORIZED)
						CSocNetLogRights::Add($logID, array("SA", "S".$entity_type.$entity_id, "AU"));
					elseif ($entity_type == SONET_ENTITY_USER && $perm == SONET_RELATIONS_TYPE_AUTHORIZED)
						CSocNetLogRights::Add($logID, array("SA", "AU"));
					elseif ($entity_type == SONET_ENTITY_GROUP && $perm == SONET_ROLES_ALL)
						CSocNetLogRights::Add($logID, array("SA", "S".$entity_type.$entity_id, "G2"));
					elseif ($entity_type == SONET_ENTITY_USER && $perm == SONET_RELATIONS_TYPE_ALL)
						CSocNetLogRights::Add($logID, array("SA", "G2"));
				}
			}
		}
	}

	function CheckForUser($logID, $userID, $siteID = SITE_ID)
	{
		$strSql = "SELECT SLR.ID FROM b_sonet_log_right SLR
			INNER JOIN b_user_access UA ON 0=1 ".
//			(CSocNetUser::IsUserModuleAdmin($userID, $siteID) ? " OR SLR.GROUP_CODE = 'SA'" : "").
			(intval($userID) > 0 ? " OR (SLR.GROUP_CODE = 'AU')" : "").
			" OR (SLR.GROUP_CODE = 'G2')".
			(intval($userID) > 0 ? " OR (UA.ACCESS_CODE = SLR.GROUP_CODE AND UA.USER_ID = ".$userID.")" : "")."
			WHERE SLR.LOG_ID = ".$logID;

		$result = $GLOBALS["DB"]->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		if($ar = $result->Fetch())
			return true;

		return false;
	}

}
?>