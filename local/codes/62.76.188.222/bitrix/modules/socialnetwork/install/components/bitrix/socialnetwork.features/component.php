<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("socialnetwork"))
{
	ShowError(GetMessage("SONET_MODULE_NOT_INSTALL"));
	return;
}

$arParams["GROUP_ID"] = IntVal($arParams["GROUP_ID"]);
$arParams["USER_ID"] = IntVal($arParams["USER_ID"]);
if ($arParams["USER_ID"] <= 0)
	$arParams["USER_ID"] = $GLOBALS["USER"]->GetID();
$arParams["PAGE_ID"] = Trim($arParams["PAGE_ID"]);
if (StrLen($arParams["PAGE_ID"]) <= 0)
	$arParams["PAGE_ID"] = "user_features";

$arParams["SET_NAV_CHAIN"] = ($arParams["SET_NAV_CHAIN"] == "N" ? "N" : "Y");

if (strLen($arParams["USER_VAR"]) <= 0)
	$arParams["USER_VAR"] = "user_id";
if (strLen($arParams["PAGE_VAR"]) <= 0)
	$arParams["PAGE_VAR"] = "page";
if (strLen($arParams["GROUP_VAR"]) <= 0)
	$arParams["GROUP_VAR"] = "group_id";

$arParams["PATH_TO_USER"] = trim($arParams["PATH_TO_USER"]);
if (strlen($arParams["PATH_TO_USER"]) <= 0)
	$arParams["PATH_TO_USER"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_GROUP"] = trim($arParams["PATH_TO_GROUP"]);
if (strlen($arParams["PATH_TO_GROUP"]) <= 0)
	$arParams["PATH_TO_GROUP"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group&".$arParams["GROUP_VAR"]."=#group_id#");

if (strlen($arParams["NAME_TEMPLATE"]) <= 0)
	$arParams["NAME_TEMPLATE"] = CSite::GetNameFormat();
$bUseLogin = $arParams['SHOW_LOGIN'] != "N" ? true : false;

$arResult["FatalError"] = "";

if (!$GLOBALS["USER"]->IsAuthorized())
	$arResult["NEED_AUTH"] = "Y";
else
{
	if ($arParams["PAGE_ID"] == "user_features" && $arParams["USER_ID"] <= 0)
		$arResult["FatalError"] = GetMessage("SONET_C3_NO_USER_ID").".";
	elseif ($arParams["PAGE_ID"] == "group_features" && $arParams["GROUP_ID"] <= 0)
		$arResult["FatalError"] = GetMessage("SONET_C3_NO_GROUP_ID").".";

	if (StrLen($arResult["FatalError"]) <= 0)
	{
		if ($arParams["PAGE_ID"] == "group_features")
		{
			$arGroup = CSocNetGroup::GetByID($arParams["GROUP_ID"]);
			if ($arGroup && ($arGroup["OWNER_ID"] == $GLOBALS["USER"]->GetID() || CSocNetUser::IsCurrentUserModuleAdmin()))
			{
				$arResult["CurrentUserPerms"] = CSocNetUserToGroup::InitUserPerms($GLOBALS["USER"]->GetID(), $arGroup, CSocNetUser::IsCurrentUserModuleAdmin());

				if ($arResult["CurrentUserPerms"]["UserCanModifyGroup"])
				{
					$arResult["Group"] = $arGroup;
					$arResult["Features"] = array();

					$arFeaturesTmp = array();
					$dbResultTmp = CSocNetFeatures::GetList(
						array(),
						array("ENTITY_ID" => $arResult["Group"]["ID"], "ENTITY_TYPE" => SONET_ENTITY_GROUP)
					);
					while ($arResultTmp = $dbResultTmp->GetNext())
						$arFeaturesTmp[$arResultTmp["FEATURE"]] = $arResultTmp;

					global $arSocNetFeaturesSettings;
					foreach ($arSocNetFeaturesSettings as $feature => $arFeature)
					{
						if (!is_array($arFeature["allowed"]) || !in_array(SONET_ENTITY_GROUP, $arFeature["allowed"]))
							continue;

						$arResult["Features"][$feature] = array(
							"FeatureName" => $arFeaturesTmp[$feature]["FEATURE_NAME"],
							"Active" => (array_key_exists($feature, $arFeaturesTmp) ? ($arFeaturesTmp[$feature]["ACTIVE"] == "Y") : true),
							"Operations" => array(),
						);

						if (
							$feature == 'calendar' 
							&& (
								!IsModuleInstalled("intranet")
								|| COption::GetOptionString("intranet", "calendar_2", "N") == "Y" 
							)
							&& CModule::IncludeModule("calendar")
						)
						{
							$arResult["Features"][$feature]['note'] = GetMessage('SONET_CALENDAR_ACCESS_NOTE');
							continue;
						}

						if ($feature == 'files')
						{
							$arResult["Features"][$feature]['note'] = GetMessage("SONET_WEBDAV_RIGHS_NOTE");
							continue;
						}

						if($feature == "blog" && $arParams["PAGE_ID"] != "group_features")
							$arResult["Features"][$feature]["Active"] = true;

						foreach ($arFeature["operations"] as $op => $arOp)
							$arResult["Features"][$feature]["Operations"][$op] = CSocNetFeaturesPerms::GetOperationPerm(SONET_ENTITY_GROUP, $arResult["Group"]["ID"], $feature, $op);
					}
				}
				else
					$arResult["FatalError"] = GetMessage("SONET_C3_PERMS").".";
			}
			else
				$arResult["FatalError"] = GetMessage("SONET_C3_NO_GROUP").".";
		}
		else
		{
			$dbUser = CUser::GetByID($arParams["USER_ID"]);
			$arResult["User"] = $dbUser->GetNext();

			if (is_array($arResult["User"]))
			{
				$arResult["User"]["NAME_FORMATTED"] = CUser::FormatName($arParams['NAME_TEMPLATE'], $arResult['User'], $bUseLogin);

				CSocNetUserPerms::InitUserPerms($GLOBALS["USER"]->GetID(), $arResult["User"]["ID"], CSocNetUser::IsCurrentUserModuleAdmin());

				$arResult["CurrentUserPerms"] = CSocNetUserPerms::InitUserPerms($GLOBALS["USER"]->GetID(), $arResult["User"]["ID"], CSocNetUser::IsCurrentUserModuleAdmin());
				if ($arResult["CurrentUserPerms"]["Operations"]["modifyuser"])
				{
					$arResult["Features"] = array();

					$arFeaturesTmp = array();
					$dbResultTmp = CSocNetFeatures::GetList(
						array(),
						array("ENTITY_ID" => $arResult["User"]["ID"], "ENTITY_TYPE" => SONET_ENTITY_USER)
					);
					while ($arResultTmp = $dbResultTmp->GetNext())
						$arFeaturesTmp[$arResultTmp["FEATURE"]] = $arResultTmp;
					global $arSocNetFeaturesSettings;
					foreach ($arSocNetFeaturesSettings as $feature => $arFeature)
					{
						if (!is_array($arFeature["allowed"]) || !in_array(SONET_ENTITY_USER, $arFeature["allowed"]))
							continue;

						$arResult["Features"][$feature] = array(
							"FeatureName" => $arFeaturesTmp[$feature]["FEATURE_NAME"],
							"Active" => (array_key_exists($feature, $arFeaturesTmp) ? ($arFeaturesTmp[$feature]["ACTIVE"] == "Y") : true),
							"Operations" => array(),
						);

						if ($feature == 'files')
						{
							$arResult["Features"][$feature]['note'] = GetMessage("SONET_WEBDAV_RIGHS_NOTE");
							continue;
						}

						if (
							$feature == 'calendar' 
							&& (
								!IsModuleInstalled("intranet")
								|| COption::GetOptionString("intranet", "calendar_2", "N") == "Y"
							)
							&& CModule::IncludeModule("calendar"))
						{
							$arResult["Features"][$feature]['note'] = GetMessage('SONET_CALENDAR_ACCESS_NOTE');
							continue;
						}

						if($feature == "blog" && $arParams["PAGE_ID"] != "group_features")
							$arResult["Features"][$feature]["Active"] = true;

						if (is_array($arFeature["operations"]))
							foreach ($arFeature["operations"] as $op => $arOp)
							{
								if(!($feature == "blog" && !array_key_exists(SONET_ENTITY_USER, $arOp)))
									$arResult["Features"][$feature]["Operations"][$op] = CSocNetFeaturesPerms::GetOperationPerm(SONET_ENTITY_USER, $arResult["User"]["ID"], $feature, $op);
							}
					}
				}
				else
					$arResult["FatalError"] = GetMessage("SONET_C3_PERMS").".";
			}
			else
				$arResult["FatalError"] = GetMessage("SONET_P_USER_NO_USER").".";
		}
	}

	if (StrLen($arResult["FatalError"]) <= 0)
	{
		$arResult["Urls"]["User"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $arParams["USER_ID"]));
		$arResult["Urls"]["Group"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP"], array("group_id" => $arParams["GROUP_ID"]));

		if ($arParams["PAGE_ID"] != "group_features" && ($arParams["SET_TITLE"] == "Y" || $arParams["SET_NAV_CHAIN"] != "N"))
		{
			$arParams["TITLE_NAME_TEMPLATE"] = str_replace(
				array("#NOBR#", "#/NOBR#"),
				array("", ""),
				$arParams["NAME_TEMPLATE"]
			);

			$arTmpUser = array(
				'NAME' => $arResult["User"]["~NAME"],
				'LAST_NAME' => $arResult["User"]["~LAST_NAME"],
				'SECOND_NAME' => $arResult["User"]["~SECOND_NAME"],
				'LOGIN' => $arResult["User"]["~LOGIN"],
			);
			$strTitleFormatted = CUser::FormatName($arParams['TITLE_NAME_TEMPLATE'], $arTmpUser, $bUseLogin);
		}

		if ($arParams["SET_TITLE"] == "Y")
		{
			if ($arParams["PAGE_ID"] == "group_features")
				$APPLICATION->SetTitle($arResult["Group"]["NAME"].": ".GetMessage("SONET_C3_GROUP_SETTINGS"));
			else
				$APPLICATION->SetTitle($strTitleFormatted.": ".GetMessage("SONET_C3_USER_SETTINGS"));
		}
		if ($arParams["SET_NAV_CHAIN"] != "N")
		{
			if ($arParams["PAGE_ID"] == "group_features")
			{
				$APPLICATION->AddChainItem($arResult["Group"]["NAME"], $arResult["Urls"]["Group"]);
				$APPLICATION->AddChainItem(GetMessage("SONET_C3_GROUP_SETTINGS"));
			}
			else
			{
				$APPLICATION->AddChainItem($strTitleFormatted, $arResult["Urls"]["User"]);
				$APPLICATION->AddChainItem(GetMessage("SONET_C3_USER_SETTINGS"));
			}
		}

		$arResult["ShowForm"] = "Input";

		if ($_SERVER["REQUEST_METHOD"]=="POST" && strlen($_POST["save"]) > 0 && check_bitrix_sessid())
		{
			$errorMessage = "";

			foreach ($arResult["Features"] as $feature => $arFeature)
			{
				if($feature == "blog" && $arParams["PAGE_ID"] != "group_features")
					$_REQUEST["blog_active"] = "Y";

				$idTmp = CSocNetFeatures::SetFeature(
					($arParams["PAGE_ID"] == "group_features") ? SONET_ENTITY_GROUP : SONET_ENTITY_USER,
					($arParams["PAGE_ID"] == "group_features") ? $arResult["Group"]["ID"] : $arResult["User"]["ID"],
					$feature,
					($_REQUEST[$feature."_active"] == "Y") ? true : false,
					(StrLen($_REQUEST[$feature."_name"]) > 0) ? $_REQUEST[$feature."_name"] : false
				);

				if (
					$idTmp
					&& $_REQUEST[$feature."_active"] == "Y"
					&& (
						!array_key_exists("hide_operations_settings", $GLOBALS["arSocNetFeaturesSettings"][$feature])
						|| !$GLOBALS["arSocNetFeaturesSettings"][$feature]["hide_operations_settings"]
					)
				)
				{
					foreach ($arFeature["Operations"] as $operation => $perm)
					{
						if (
							!array_key_exists("restricted", $GLOBALS["arSocNetFeaturesSettings"][$feature]["operations"][$operation])
							|| !in_array($key, $GLOBALS["arSocNetFeaturesSettings"][$feature]["operations"][$operation]["restricted"][($arParams["PAGE_ID"] == "group_features" ? SONET_ENTITY_GROUP : SONET_ENTITY_USER)])
						)
						{
							$id1Tmp = CSocNetFeaturesPerms::SetPerm(
								$idTmp,
								$operation,
								$_REQUEST[$feature."_".$operation."_perm"]
							);
							if (!$id1Tmp && $e = $APPLICATION->GetException())
								$errorMessage .= $e->GetString();
						}
					}
				}
				elseif ($e = $APPLICATION->GetException())
						$errorMessage .= $e->GetString();
			}

			if (strlen($errorMessage) > 0)
				$arResult["ErrorMessage"] = $errorMessage;
			else
			{
				if ($_REQUEST['backurl'])
					LocalRedirect($_REQUEST['backurl']);
				else
				{
					if ($arParams["PAGE_ID"] == "group_features")
						LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP"], array("group_id" => $arParams["GROUP_ID"])));
					else
						LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $arParams["USER_ID"])));
				}
			}
		}

		if ($arResult["ShowForm"] == "Input")
		{
			if ($arParams["PAGE_ID"] == "group_features")
			{
				$arResult["ENTITY_TYPE"] = SONET_ENTITY_GROUP;
				$arResult["PermsVar"] = array(
					SONET_ROLES_OWNER => GetMessage("SONET_C3_PVG_OWNER"),
					SONET_ROLES_MODERATOR => GetMessage("SONET_C3_PVG_MOD"),
					SONET_ROLES_USER => GetMessage("SONET_C3_PVG_USER"),
					SONET_ROLES_AUTHORIZED => GetMessage("SONET_C3_PVG_AUTHORIZED"),
					SONET_ROLES_ALL => GetMessage("SONET_C3_PVG_ALL"),
				);
			}
			else
			{
				$arResult["ENTITY_TYPE"] = SONET_ENTITY_USER;
				if (CSocNetUser::IsFriendsAllowed())
					$arResult["PermsVar"] = array(
						SONET_RELATIONS_TYPE_NONE => GetMessage("SONET_C3_PVU_NONE"),
						SONET_RELATIONS_TYPE_FRIENDS => GetMessage("SONET_C3_PVU_FR"),
						SONET_RELATIONS_TYPE_FRIENDS2 => GetMessage("SONET_C3_PVU_FR2"),
						SONET_RELATIONS_TYPE_AUTHORIZED => GetMessage("SONET_C3_PVU_AUTHORIZED"),
						SONET_RELATIONS_TYPE_ALL => GetMessage("SONET_C3_PVU_ALL"),
					);
				else
					$arResult["PermsVar"] = array(
						SONET_RELATIONS_TYPE_NONE => GetMessage("SONET_C3_PVU_NONE"),
						SONET_RELATIONS_TYPE_AUTHORIZED => GetMessage("SONET_C3_PVU_AUTHORIZED"),
						SONET_RELATIONS_TYPE_ALL => GetMessage("SONET_C3_PVU_ALL"),
					);
			}
		}
	}
}
$this->IncludeComponentTemplate();
?>
