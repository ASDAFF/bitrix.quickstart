<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("socialnetwork"))
{
	ShowError(GetMessage("SONET_MODULE_NOT_INSTALL"));
	return;
}

$arParams["GROUP_ID"] = IntVal($arParams["GROUP_ID"]);

$arParams["SET_NAVCHAIN"] = ($arParams["SET_NAVCHAIN"] == "N" ? "N" : "Y");

if (strLen($arParams["USER_VAR"]) <= 0)
	$arParams["USER_VAR"] = "user_id";
if (strLen($arParams["GROUP_VAR"]) <= 0)
	$arParams["GROUP_VAR"] = "group_id";
if (strLen($arParams["PAGE_VAR"]) <= 0)
	$arParams["PAGE_VAR"] = "page";

$arParams["PATH_TO_USER"] = trim($arParams["PATH_TO_USER"]);
if (strlen($arParams["PATH_TO_USER"]) <= 0)
	$arParams["PATH_TO_USER"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user&".$arParams["USER_VAR"]."=#user_id#");
$arParams["PATH_TO_GROUP"] = trim($arParams["PATH_TO_GROUP"]);
if (strlen($arParams["PATH_TO_GROUP"]) <= 0)
	$arParams["PATH_TO_GROUP"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group&".$arParams["GROUP_VAR"]."=#group_id#");
$arParams["PATH_TO_GROUP_REQUEST_SEARCH"] = trim($arParams["PATH_TO_GROUP_REQUEST_SEARCH"]);
if (strlen($arParams["PATH_TO_GROUP_REQUEST_SEARCH"]) <= 0)
	$arParams["PATH_TO_GROUP_REQUEST_SEARCH"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group_request_search&".$arParams["GROUP_VAR"]."=#group_id#");

$arParams["ITEMS_COUNT"] = IntVal($arParams["ITEMS_COUNT"]);
if ($arParams["ITEMS_COUNT"] <= 0)
	$arParams["ITEMS_COUNT"] = 30;

$arParams["PATH_TO_SMILE"] = Trim($arParams["PATH_TO_SMILE"]);

// for bitrix:main.user.link
if (IsModuleInstalled('intranet'))
{
	$arTooltipFieldsDefault	= serialize(array(
		"EMAIL",
		"PERSONAL_MOBILE",
		"WORK_PHONE",
		"PERSONAL_ICQ",
		"PERSONAL_PHOTO",
		"PERSONAL_CITY",
		"WORK_COMPANY",
		"WORK_POSITION",
	));
	$arTooltipPropertiesDefault = serialize(array(
		"UF_DEPARTMENT",
		"UF_PHONE_INNER",
	));
}
else
{
	$arTooltipFieldsDefault = serialize(array(
		"PERSONAL_ICQ",
		"PERSONAL_BIRTHDAY",
		"PERSONAL_PHOTO",
		"PERSONAL_CITY",
		"WORK_COMPANY",
		"WORK_POSITION"
	));
	$arTooltipPropertiesDefault = serialize(array());
}

if (!array_key_exists("SHOW_FIELDS_TOOLTIP", $arParams))
	$arParams["SHOW_FIELDS_TOOLTIP"] = unserialize(COption::GetOptionString("socialnetwork", "tooltip_fields", $arTooltipFieldsDefault));
if (!array_key_exists("USER_PROPERTY_TOOLTIP", $arParams))
	$arParams["USER_PROPERTY_TOOLTIP"] = unserialize(COption::GetOptionString("socialnetwork", "tooltip_properties", $arTooltipPropertiesDefault));

if (!$GLOBALS["USER"]->IsAuthorized())
	$arResult["NEED_AUTH"] = "Y";
else
{
	$arGroup = CSocNetGroup::GetByID($arParams["GROUP_ID"]);

	if (
		!$arGroup 
		|| !is_array($arGroup) 
		|| $arGroup["ACTIVE"] != "Y" 
	)
		$arResult["FatalError"] = GetMessage("SONET_P_USER_NO_GROUP");
	else
	{
		$arGroupSites = array();
		$rsGroupSite = CSocNetGroup::GetSite($arGroup["ID"]);
		while ($arGroupSite = $rsGroupSite->Fetch())
			$arGroupSites[] = $arGroupSite["LID"];

		if (!in_array(SITE_ID, $arGroupSites))
			$arResult["FatalError"] = GetMessage("SONET_P_USER_NO_GROUP");
		else
		{
			$arResult["Group"] = $arGroup;

			$arResult["CurrentUserPerms"] = CSocNetUserToGroup::InitUserPerms($GLOBALS["USER"]->GetID(), $arResult["Group"], CSocNetUser::IsCurrentUserModuleAdmin());
			
			if (!$arResult["CurrentUserPerms"] || !$arResult["CurrentUserPerms"]["UserCanViewGroup"])
				$arResult["FatalError"] = GetMessage("SONET_C12_NO_PERMS").". ";
			else
			{
				$arResult["Urls"]["Group"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP"], array("group_id" => $arResult["Group"]["ID"]));
				$arResult["Urls"]["RequestSearch"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_REQUEST_SEARCH"], array("group_id" => $arResult["Group"]["ID"]));

				if ($arParams["SET_TITLE"] == "Y")
					$APPLICATION->SetTitle($arResult["Group"]["NAME"].": ".GetMessage("SONET_C12_TITLE"));

				if ($arParams["SET_NAVCHAIN"] != "N")
				{
					$APPLICATION->AddChainItem($arResult["Group"]["NAME"], $arResult["Urls"]["Group"]);
					$APPLICATION->AddChainItem(GetMessage("SONET_C12_TITLE"));
				}

				if (!$arResult["CurrentUserPerms"]["UserCanInitiate"])
					$arResult["FatalError"] = GetMessage("SONET_C12_CANT_INVITE").". ";
				else
				{
					$arNavParams = array("nPageSize" => $arParams["ITEMS_COUNT"], "bDescPageNumbering" => false);
					$arNavigation = CDBResult::GetNavParams($arNavParams);

					if ($_SERVER["REQUEST_METHOD"]=="POST" && (strlen($_POST["resend"]) > 0 || strlen($_POST["reject"]) > 0) && check_bitrix_sessid())
					{
						$errorMessage = "";

						$arIDs = array();
						if (strlen($errorMessage) <= 0)
						{
							for ($i = 0; $i <= IntVal($_POST["max_count"]); $i++)
							{
								if ($_POST["checked_".$i] == "Y")
									$arIDs[] = IntVal($_POST["id_".$i]);
							}

							if (count($arIDs) <= 0)
								$errorMessage .= GetMessage("SONET_C12_NOT_SELECTED").". ";
						}

						if (strlen($errorMessage) <= 0)
						{
							if (strlen($_POST["reject"]) > 0)
							{
								$errorMessage = "";
								foreach($arIDs as $relation_id)
								{
									$arRelation = CSocNetUserToGroup::GetByID($relation_id);
									if (!$arRelation)
										continue;

									if (!CSocNetUserToGroup::Delete($arRelation["ID"]))
									{
										if ($e = $APPLICATION->GetException())
											$errorMessage .= $e->GetString();
										if (StrLen($errorMessage) <= 0)
											$errorMessage .= GetMessage("SONET_C12_CANT_DELETE_INVITATION").$arRelation["ID"];
									}
								}
								$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_DELETE_RELATION");
							}
						}

						if (strlen($errorMessage) > 0)
							$arResult["ErrorMessage"] = $errorMessage;
					}
					elseif (CModule::IncludeModule('extranet') && CExtranet::IsExtranetSite() && intval($_REQUEST["invite_user_id"]) > 0)
					{
						$errorMessage = "";

						$rsInvitedUser = CUser::GetByID(intval($_REQUEST["invite_user_id"]));
						if (($arInvitedUser = $rsInvitedUser->Fetch()) && (!is_array($arInvitedUser["UF_DEPARTMENT"]) || intval($arInvitedUser["UF_DEPARTMENT"][0]) <= 0))
						{
							if (strlen($arInvitedUser["LAST_LOGIN"]) <= 0 && strlen($arInvitedUser["LAST_ACTIVITY_DATE"]) <= 0)
							{

								$checkword = randString(8);

								$user = new CUser;
								$arFields = array(
									"CHECKWORD"		=> $checkword,
								);
								$user->Update($arInvitedUser["ID"], $arFields);
								$errorMessage .= $user->LAST_ERROR;

								if (strlen($errorMessage) <= 0)
								{
									$event = new CEvent;
									$arFields = Array(
										"USER_ID"	=>	$arInvitedUser["ID"],
										"CHECKWORD"	=>	$checkword,
										"EMAIL"	=>	$arInvitedUser["EMAIL"]
									);

									$event->Send("EXTRANET_INVITATION", SITE_ID, $arFields);
								}	

								if (strlen($errorMessage) > 0)
									$arResult["ErrorMessage"] = $errorMessage;
								else
									LocalRedirect($APPLICATION->GetCurPageParam("invite_sent=Y", array("invite_user_id", "employee")));
							}
						}
					}

					$parser = new CSocNetTextParser(LANGUAGE_ID, $arParams["PATH_TO_SMILE"]);

					$arResult["Requests"] = false;
					$dbRequests = CSocNetUserToGroup::GetList(
						array("DATE_CREATE" => "ASC"),
						array(
							"GROUP_ID" => $arResult["Group"]["ID"],
							"ROLE" => SONET_ROLES_REQUEST,
							"INITIATED_BY_TYPE" => SONET_INITIATED_BY_GROUP
						),
						false,
						$arNavParams,
						array("ID", "USER_ID", "DATE_CREATE", "DATE_UPDATE", "MESSAGE", "USER_NAME", "USER_LAST_NAME", "USER_SECOND_NAME", "USER_LOGIN", "USER_PERSONAL_PHOTO", "USER_PERSONAL_GENDER")
					);
					if ($dbRequests)
					{
						$arResult["Requests"] = array();
						$arResult["Requests"]["List"] = false;
						while ($arRequests = $dbRequests->GetNext())
						{
							if ($arResult["Requests"]["List"] == false)
								$arResult["Requests"]["List"] = array();

							$pu = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $arRequests["USER_ID"]));
							$canViewProfile = CSocNetUserPerms::CanPerformOperation($GLOBALS["USER"]->GetID(), $arRequests["USER_ID"], "viewprofile", CSocNetUser::IsCurrentUserModuleAdmin());

							if (intval($arRequests["USER_PERSONAL_PHOTO"]) <= 0)
							{
								switch ($arRequests["USER_PERSONAL_GENDER"])
								{
									case "M":
										$suffix = "male";
										break;
									case "F":
										$suffix = "female";
										break;
									default:
										$suffix = "unknown";
								}
								$arRequests["USER_PERSONAL_PHOTO"] = COption::GetOptionInt("socialnetwork", "default_user_picture_".$suffix, false, SITE_ID);
							}
							$arImage = CSocNetTools::InitImage($arRequests["USER_PERSONAL_PHOTO"], 150, "/bitrix/images/socialnetwork/nopic_user_150.gif", 150, $pu, $canViewProfile);

							$arResult["Requests"]["List"][] = array(
								"ID" => $arRequests["ID"],
								"USER_ID" => $arRequests["USER_ID"],
								"USER_NAME" => $arRequests["USER_NAME"],
								"USER_LAST_NAME" => $arRequests["USER_LAST_NAME"],
								"USER_SECOND_NAME" => $arRequests["USER_SECOND_NAME"],
								"USER_LOGIN" => $arRequests["USER_LOGIN"],
								"USER_PERSONAL_PHOTO" => $arRequests["USER_PERSONAL_PHOTO"],
								"USER_PERSONAL_PHOTO_FILE" => $arImage["FILE"],
								"USER_PERSONAL_PHOTO_IMG" => $arImage["IMG"],
								"USER_PROFILE_URL" => $pu,
								"SHOW_PROFILE_LINK" => $canViewProfile,
								"DATE_CREATE" => FormatDateFromDB($arRequests["DATE_CREATE"]),
								"MESSAGE" => $parser->convert(
									$arRequests["~MESSAGE"],
									false,
									array(),
									array(
										"HTML" => "N",
										"ANCHOR" => "Y",
										"BIU" => "Y",
										"IMG" => "Y",
										"LIST" => "Y",
										"QUOTE" => "Y",
										"CODE" => "Y",
										"FONT" => "Y",
										"SMILES" => "Y",
										"UPLOAD" => "N",
										"NL2BR" => "N"
									)
								)
							);
						}
						$arResult["NAV_STRING"] = $dbRequests->GetPageNavStringEx($navComponentObject, GetMessage("SONET_C12_NAV"), "", false);
					}
				}
			}
		}
	}
}
$this->IncludeComponentTemplate();
?>