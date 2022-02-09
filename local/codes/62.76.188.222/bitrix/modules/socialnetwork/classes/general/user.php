<?
IncludeModuleLangFile(__FILE__);

class CAllSocNetUser
{
	function OnUserDelete($ID)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($ID))
			return false;

		$ID = IntVal($ID);
		$bSuccess = True;

		if (!CSocNetGroup::DeleteNoDemand($ID))
		{
			if($ex = $GLOBALS["APPLICATION"]->GetException())
				$err = $ex->GetString();
			$GLOBALS["APPLICATION"]->ThrowException($err);				
			$bSuccess = false;
		}

		if ($bSuccess)
		{
			CSocNetUserRelations::DeleteNoDemand($ID);
			CSocNetUserPerms::DeleteNoDemand($ID);
			CSocNetUserEvents::DeleteNoDemand($ID);
			CSocNetMessages::DeleteNoDemand($ID);
			CSocNetUserToGroup::DeleteNoDemand($ID);
			CSocNetLogEvents::DeleteNoDemand($ID);
			CSocNetLog::DeleteNoDemand($ID);
			CSocNetLogComments::DeleteNoDemand($ID);
			CSocNetFeatures::DeleteNoDemand($ID);

			CUserOptions::DeleteOption("socialnetwork", "~menu_".SONET_ENTITY_USER."_".$ID, false, 0);
		}

		return $bSuccess;
	}

	function OnBeforeUserUpdate(&$arFields)
	{
		$rsUser = CUser::GetByID($arFields["ID"]);
		if ($arUser = $rsUser->Fetch())
			define("GLOBAL_ACTIVE_VALUE", $arUser["ACTIVE"]);
	}

	function OnAfterUserAdd(&$arFields)
	{
		return;
	}

	function OnAfterUserLogout(&$arParams)
	{
		if (array_key_exists("SONET_ADMIN", $_SESSION))
			unset($_SESSION["SONET_ADMIN"]);
	}

	function OnAfterUserUpdate(&$arFields)
	{
		if (array_key_exists("ACTIVE", $arFields) && defined("GLOBAL_ACTIVE_VALUE") && GLOBAL_ACTIVE_VALUE != $arFields["ACTIVE"]):

			$dbResult = CSocNetUserToGroup::GetList(array(), array("USER_ID" => $arFields["ID"]), false, false, array("GROUP_ID"));
			while ($arResult = $dbResult->Fetch())
				$arGroups[] = $arResult["GROUP_ID"];

			for ($i = 0; $i < count($arGroups); $i++)
				CSocNetGroup::SetStat($arGroups[$i]);

		endif;
	}
	
	function OnBeforeProlog()
	{
		if (!$GLOBALS["USER"]->IsAuthorized())
			return;

		CUser::SetLastActivityDate($GLOBALS["USER"]->GetID());
	}

	function OnUserInitialize($user_id, $arFields = array())
	{
		if (intval($user_id) <= 0)
			return false;

		if (CModule::IncludeModule("im"))
			$bIM = true;

		$dbRelation = CSocNetUserToGroup::GetList(
			array(), 
			array(
				"USER_ID" => $user_id, 
				"ROLE" => SONET_ROLES_REQUEST, 
				"INITIATED_BY_TYPE" => SONET_INITIATED_BY_GROUP
			), 
			false, 
			false, 
			array("ID", "GROUP_ID")
		);
		while ($arRelation = $dbRelation->Fetch())
			if (CSocNetUserToGroup::UserConfirmRequestToBeMember($user_id, $arRelation["ID"], false) && defined("BX_COMP_MANAGED_CACHE"))
			{
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("sonet_user2group_G".$arRelation["GROUP_ID"]);
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("sonet_user2group_U".$user_id);
				if ($bIM)
					CIMNotify::DeleteByTag("SOCNET|INVITE_GROUP|".$user_id."|".intval($arRelation["ID"]));
			}
			
	}

	function IsOnLine($userID)
	{
		$userID = IntVal($userID);
		if ($userID <= 0)
			return false;

		return CUser::IsOnLine($userID, 120);
	}

	function IsFriendsAllowed()
	{
		if (array_key_exists("SONET_ALLOW_FRIENDS_CACHE", $GLOBALS) && !array_key_exists("SONET_ALLOW_FRIENDS_CACHE", $_REQUEST))
			return $GLOBALS["SONET_ALLOW_FRIENDS_CACHE"];

		$GLOBALS["SONET_ALLOW_FRIENDS_CACHE"] = (COption::GetOptionString("socialnetwork", "allow_frields", "Y") == "Y");
		return $GLOBALS["SONET_ALLOW_FRIENDS_CACHE"];
	}

	function IsCurrentUserModuleAdmin($site_id = SITE_ID, $bUseSession = true)
	{
		if (!is_object($GLOBALS["USER"]) || !$GLOBALS["USER"]->IsAuthorized())
			return false;
			
		if ($bUseSession && !isset($_SESSION["SONET_ADMIN"]))
			return false;

		if ($GLOBALS["USER"]->IsAdmin())
			return true;

		if (is_array($site_id))
		{
			foreach ($site_id as $site_id_tmp)
			{
				$modulePerms = $GLOBALS["APPLICATION"]->GetGroupRight("socialnetwork", false, "Y", "Y", array($site_id_tmp, false));
				if ($modulePerms >= "W")
					return true;
			}
			return false;
		}
		else
		{
			$modulePerms = $GLOBALS["APPLICATION"]->GetGroupRight("socialnetwork", false, "Y", "Y", ($site_id ? array($site_id, false) : false));
			return ($modulePerms >= "W");
		}
	}

	function IsUserModuleAdmin($userID, $site_id = SITE_ID)	
	{
		global $DB;
		static $arSocnetModuleAdminsCache = array();

		if ($userID <= 0)
			return false;

		if ($site_id && !is_array($site_id))
			$site_id = array($site_id, false);
		elseif ($site_id && is_array($site_id))
			$site_id = array_merge($site_id, array(false));

		$cache_key = serialize($site_id);
		if (!array_key_exists($cache_key, $arSocnetModuleAdminsCache))
		{
			if (!$site_id)
				$strSqlSite = "and MG.SITE_ID IS NULL";
			else
			{
				$strSqlSite = " and (";
				foreach($site_id as $i => $site_id_tmp)
				{
					if ($i > 0)
						$strSqlSite .= " OR ";

					$strSqlSite .= "MG.SITE_ID ".($site_id_tmp ? "= '".$DB->ForSQL($site_id_tmp)."'" : "IS NULL");
				}
				$strSqlSite .= ")";
			}

			$strSql = "SELECT ".
				"UG.USER_ID U_ID, ".
				"G.ID G_ID, ".
				"max(MG.G_ACCESS) G_ACCESS ".
				"FROM b_user_group UG, b_module_group MG, b_group G  ".
				"WHERE ".
					"	(G.ID = MG.GROUP_ID or G.ID = 1) ".
					"	AND MG.MODULE_ID = 'socialnetwork' ".
					"	AND G.ID = UG.GROUP_ID ".
					"	AND G.ACTIVE = 'Y' ".
					"	AND G_ACCESS >= 'W'	".
					"	AND UG.USER_ID = ".intval($userID)." ".
					"	AND ((UG.DATE_ACTIVE_FROM IS NULL) OR (UG.DATE_ACTIVE_FROM <= ".$DB->CurrentTimeFunction().")) ".
					"	AND ((UG.DATE_ACTIVE_TO IS NULL) OR (UG.DATE_ACTIVE_TO >= ".$DB->CurrentTimeFunction().")) ".
					"	AND (G.ANONYMOUS<>'Y' OR G.ANONYMOUS IS NULL) ".
					$strSqlSite." ".
				"GROUP BY ".
					"	UG.USER_ID, G.ID";

			$arModuleAdmins = array();
			$result = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			while($ar = $result->Fetch())
				if (!in_array($ar["U_ID"], $arModuleAdmins))
					$arModuleAdmins[] = $ar["U_ID"];
					
			$arSocnetModuleAdminsCache[$cache_key] = $arModuleAdmins;
		}

		return (in_array($userID, $arSocnetModuleAdminsCache[$cache_key]));
	}
			
	function FormatName($name, $lastName, $login)
	{
		$name = Trim($name);
		$lastName = Trim($lastName);
		$login = Trim($login);

		$formatName = $name;
		if (StrLen($formatName) > 0 && StrLen($lastName) > 0)
			$formatName .= " ";
		$formatName .= $lastName;
		if (StrLen($formatName) <= 0)
			$formatName = $login;

		return $formatName;
	}

	function FormatNameEx($name, $secondName, $lastName, $login, $email, $id)
	{
		$name = Trim($name);
		$lastName = Trim($lastName);
		$secondName = Trim($secondName);
		$login = Trim($login);
		$email = Trim($email);
		$id = IntVal($id);

		$formatName = $name;
		if (StrLen($formatName) > 0 && StrLen($secondName) > 0)
			$formatName .= " ";
		$formatName .= $secondName;
		if (StrLen($formatName) > 0 && StrLen($lastName) > 0)
			$formatName .= " ";
		$formatName .= $lastName;
		if (StrLen($formatName) <= 0)
			$formatName = $login;

		if (StrLen($email) > 0)
			$formatName .= " &lt;".$email."&gt;";
		$formatName .= " [".$id."]";

		return $formatName;
	}

	function SearchUser($user, $bIntranet = false)
	{
		$user = Trim($user);
		if (StrLen($user) <= 0)
			return false;

		$userID = 0;
		if ($user."|" == IntVal($user)."|")
			$userID = IntVal($user);

		if ($userID <= 0)
		{
			$arMatches = array();
			if (preg_match("#\[(\d+)\]#i", $user, $arMatches))
				$userID = IntVal($arMatches[1]);
		}


		$dbUsers =  false;
		if ($userID > 0)
		{
			$arFilter = array("ID_EQUAL_EXACT" => $userID);

			$dbUsers = CUser::GetList(
				($by = "LAST_NAME"),
				($order = "asc"),
				$arFilter,
				array(
					"NAV_PARAMS" => false,
				)
			);
		}
		else
		{
			$email = "";
			$arMatches = array();
			if (preg_match("#<(.+?)>#i", $user, $arMatches))
			{

				if (check_email($arMatches[1]))
				{
					$email = $arMatches[1];
					$user = Trim(Str_Replace("<".$email.">", "", $user));
				}
			}

			$arUser = array();
			$arUserTmp = Explode(" ", $user);
			foreach ($arUserTmp as $s)
			{
				$s = Trim($s);
				if (StrLen($s) > 0)
					$arUser[] = $s;
			}

			if (count($arUser) <= 0 && strlen($email) > 0):
				$arFilter = array
					(
						"ACTIVE" => "Y",
						"EMAIL" => $email,
					);
				$dbUsers = CUser::GetList(($by="id"), ($order="asc"), $arFilter);
			else:
				$dbUsers = CUser::SearchUserByName($arUser, $email);
			endif;

		}

		if ($dbUsers)
		{
			$arResult = array();
			while ($arUsers = $dbUsers->GetNext())
			{
				$arResult[$arUsers["ID"]] = CSocNetUser::FormatNameEx(
					$arUsers["NAME"],
					$arUsers["SECOND_NAME"],
					$arUsers["LAST_NAME"],
					$arUsers["LOGIN"],
					($bIntranet ? $arUsers["EMAIL"] : ""),
					$arUsers["ID"]
				);
			}

			return $arResult;
		}

		return false;
	}

	function GetByID($ID)
	{
		$ID = IntVal($ID);

		$dbUser = CUser::GetByID($ID);
		if ($arUser = $dbUser->GetNext())
		{
			$arUser["NAME_FORMATTED"] = CUser::FormatName(CSite::GetNameFormat(false), $arUser);
			$arUser["~NAME_FORMATTED"] = htmlspecialcharsback($arUser["NAME_FORMATTED"]);
			return $arUser;
		}
		else
			return false;
	}

}
?>