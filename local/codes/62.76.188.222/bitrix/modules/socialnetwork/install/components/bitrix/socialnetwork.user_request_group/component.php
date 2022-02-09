<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("socialnetwork"))
{
	ShowError(GetMessage("SONET_MODULE_NOT_INSTALL"));
	return;
}

$arParams["GROUP_ID"] = IntVal($arParams["GROUP_ID"]);

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

$arParams["PATH_TO_GROUP_REQUESTS"] = trim($arParams["PATH_TO_GROUP_REQUESTS"]);
if (strlen($arParams["PATH_TO_GROUP_REQUESTS"]) <= 0)
	$arParams["PATH_TO_GROUP_REQUESTS"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group_requests&".$arParams["GROUP_VAR"]."=#group_id#");

$arParams["SET_NAV_CHAIN"] = ($arParams["SET_NAV_CHAIN"] == "N" ? "N" : "Y");
$bAutoSubscribe = (array_key_exists("USE_AUTOSUBSCRIBE", $arParams) && $arParams["USE_AUTOSUBSCRIBE"] == "N" ? false : true);

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
		$arResult["FatalError"] = GetMessage("SONET_P_USER_NO_GROUP").". ";
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

			$arResult["Urls"]["Group"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP"], array("group_id" => $arResult["Group"]["ID"]));
			$arResult["Urls"]["GroupRequests"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_REQUESTS"], array("group_id" => $arResult["Group"]["ID"]));

			if ($arParams["SET_TITLE"] == "Y")
				$APPLICATION->SetTitle(GetMessage("SONET_C39_PAGE_TITLE"));

			if (!$arResult["CurrentUserPerms"]["UserCanViewGroup"])
				$arResult["FatalError"] = GetMessage("SONET_C39_CANT_VIEW").". ";
			else
			{
				if ($arParams["SET_TITLE"] == "Y")
					$APPLICATION->SetTitle($arResult["Group"]["NAME"].": ".GetMessage("SONET_C39_PAGE_TITLE"));

				if ($arParams["SET_NAV_CHAIN"] != "N")
				{
					$APPLICATION->AddChainItem($arResult["Group"]["NAME"], $arResult["Urls"]["Group"]);
					$APPLICATION->AddChainItem(GetMessage("SONET_C39_PAGE_TITLE"));
				}

				if ($arResult["CurrentUserPerms"]["UserIsMember"])
					$arResult["FatalError"] = GetMessage("SONET_C39_ALREADY_MEMBER").". ";
				elseif ($arResult["CurrentUserPerms"]["UserRole"] && $_REQUEST["EventType"] != "GroupRequest")
				{
				
					if ($arResult["CurrentUserPerms"]["UserRole"] == SONET_ROLES_REQUEST)
					{
						$dbUserRequests = CSocNetUserToGroup::GetList(
							array("DATE_CREATE" => "ASC"),
							array(
							"USER_ID" => $GLOBALS["USER"]->GetID(),
							"GROUP_ID" => $arParams["GROUP_ID"],						
							"ROLE" => SONET_ROLES_REQUEST,
							"INITIATED_BY_TYPE" => SONET_INITIATED_BY_GROUP,
						),
						false,
						false,
						array("ID", "INITIATED_BY_USER_ID", "MESSAGE", "INITIATED_BY_USER_NAME", "DATE_CREATE", "INITIATED_BY_USER_LAST_NAME", "INITIATED_BY_USER_SECOND_NAME", "INITIATED_BY_USER_LOGIN", "INITIATED_BY_USER_PHOTO", "INITIATED_BY_USER_GENDER", "GROUP_ID", "GROUP_NAME", "GROUP_IMAGE_ID", "GROUP_VISIBLE")
						);

						if ($arUserRequests = $dbUserRequests->GetNext())
						{
							$parser = new CSocNetTextParser(LANGUAGE_ID, $arParams["PATH_TO_SMILE"]);

							if ($arResult["Events"] == false)
								$arResult["Events"] = array();

							$arEventTmp["EventType"] = "GroupRequest";

							$pu = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $arUserRequests["INITIATED_BY_USER_ID"]));
							$canViewProfileU = CSocNetUserPerms::CanPerformOperation($GLOBALS["USER"]->GetID(), $arUserRequests["INITIATED_BY_USER_ID"], "viewprofile", CSocNetUser::IsCurrentUserModuleAdmin());

							if (intval($arUserRequests["INITIATED_BY_USER_PHOTO"]) <= 0)
							{
								switch ($arUserRequests["INITIATED_BY_USER_GENDER"])
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
								$arUserRequests["INITIATED_BY_USER_PHOTO"] = COption::GetOptionInt("socialnetwork", "default_user_picture_".$suffix, false, SITE_ID);
							}
							$arImage = CSocNetTools::InitImage($arUserRequests["INITIATED_BY_USER_PHOTO"], 150, "/bitrix/images/socialnetwork/nopic_user_150.gif", 150, $pu, $canViewProfileU);

							$pg = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP"], array("group_id" => $arUserRequests["GROUP_ID"]));
							$canViewProfileG = (CSocNetUser::IsCurrentUserModuleAdmin() || ($arUserRequests["GROUP_VISIBLE"] == "Y"));

							if (intval($arUserRequests["GROUP_IMAGE_ID"]) <= 0)
								$arUserRequests["GROUP_IMAGE_ID"] = COption::GetOptionInt("socialnetwork", "default_group_picture", false, SITE_ID);

							$arImageG = CSocNetTools::InitImage($arUserRequests["GROUP_IMAGE_ID"], 150, "/bitrix/images/socialnetwork/nopic_group_150.gif", 150, $pg, $canViewProfileG);

							$arTmpUser = array(
								"NAME" => $arUserRequests["INITIATED_BY_USER_NAME"],
								"LAST_NAME" => $arUserRequests["INITIATED_BY_USER_LAST_NAME"],
								"SECOND_NAME" => $arUserRequests["INITIATED_BY_USER_SECOND_NAME"],
								"LOGIN" => $arUserRequests["INITIATED_BY_USER_LOGIN"],
							);
							$strNameFormatted = CUser::FormatName($arParams['NAME_TEMPLATE'], $arTmpUser, $bUseLogin);	

							$arEventTmp["Event"] = array(
								"ID" => $arUserRequests["ID"],
								"USER_ID" => $arUserRequests["INITIATED_BY_USER_ID"],
								"USER_NAME" => $arUserRequests["INITIATED_BY_USER_NAME"],
								"USER_LAST_NAME" => $arUserRequests["INITIATED_BY_USER_LAST_NAME"],
								"USER_SECOND_NAME" => $arUserRequests["INITIATED_BY_USER_SECOND_NAME"],
								"USER_LOGIN" => $arUserRequests["INITIATED_BY_USER_LOGIN"],
								"USER_NAME_FORMATTED" => $strNameFormatted,			
								"USER_PERSONAL_PHOTO" => $arUserRequests["INITIATED_BY_USER_PHOTO"],
								"USER_PERSONAL_PHOTO_FILE" => $arImage["FILE"],
								"USER_PERSONAL_PHOTO_IMG" => $arImage["IMG"],
								"USER_PROFILE_URL" => $pu,
								"SHOW_PROFILE_LINK" => $canViewProfileU,
								"DATE_CREATE" => $arUserRequests["DATE_CREATE"],
								"GROUP_NAME" => $arUserRequests["GROUP_NAME"],
								"GROUP_IMAGE_ID" => $arUserRequests["GROUP_IMAGE_ID"],
								"GROUP_IMAGE_ID_FILE" => $arImageG["FILE"],
								"GROUP_IMAGE_ID_IMG" => $arImageG["IMG"],
								"GROUP_PROFILE_URL" => $pg,
								"SHOW_GROUP_LINK" => $canViewProfileG,
								"MESSAGE" => $parser->convert(
									$arUserRequests["~MESSAGE"],
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

							$arEventTmp["Urls"]["FriendAdd"] = htmlspecialcharsbx($APPLICATION->GetCurUri("EventType=GroupRequest&eventID=".$arUserRequests["ID"]."&action=add&".bitrix_sessid_get().""));
							$arEventTmp["Urls"]["FriendReject"] = htmlspecialcharsbx($APPLICATION->GetCurUri("EventType=GroupRequest&eventID=".$arUserRequests["ID"]."&action=reject&".bitrix_sessid_get().""));

							$arResult["Events"][] = $arEventTmp;
						}
					}
					else
						$arResult["FatalError"] = GetMessage("SONET_C39_ALREADY_JOINED").". ";
				}
				else
				{
					$errorMessage = "";

					if ($_REQUEST["EventType"] == "GroupRequest" && check_bitrix_sessid() && IntVal($_REQUEST["eventID"]) > 0)
					{
						if ($_REQUEST["action"] == "add")
						{
							if (!CSocNetUserToGroup::UserConfirmRequestToBeMember($GLOBALS["USER"]->GetID(), IntVal($_REQUEST["eventID"]), $bAutoSubscribe))
							{
								if ($e = $APPLICATION->GetException())
									$errorMessage .= $e->GetString();
							}
							else
								$arResult["Success"] = "Added";						
						}
						elseif ($_REQUEST["action"] == "reject")
						{
							if (!CSocNetUserToGroup::UserRejectRequestToBeMember($GLOBALS["USER"]->GetID(), IntVal($_REQUEST["eventID"])))
							{
								if ($e = $APPLICATION->GetException())
									$errorMessage .= $e->GetString();
							}
							else
								$arResult["Success"] = "Rejected";
						}
					}					
					elseif ($arResult["Group"]["OPENED"] == "Y")
					{
						if (
							!CSocNetUserToGroup::SendRequestToBeMember($GLOBALS["USER"]->GetID(), $arResult["Group"]["ID"], "", "", $bAutoSubscribe)
							&& ($e = $APPLICATION->GetException())
						)
							$errorMessage .= $e->GetString();

						if (strlen($errorMessage) > 0)
							$arResult["ErrorMessage"] = $errorMessage;
						else
							$arResult["ShowForm"] = "Confirm";					
					}
					else
					{
						$arResult["ShowForm"] = "Input";
						if ($_SERVER["REQUEST_METHOD"]=="POST" && strlen($_POST["save"]) > 0 && check_bitrix_sessid())
						{
							$errorMessage = "";

							if (strlen($_POST["MESSAGE"]) <= 0)
								$errorMessage .= GetMessage("SONET_C39_NO_TEXT").". ";

							if (strlen($errorMessage) <= 0)
							{
								$arResult["Urls"]["GroupRequests"] = (CMain::IsHTTPS() ? "https://" : "http://").$_SERVER['HTTP_HOST'].$arResult["Urls"]["GroupRequests"];
								if (
									!CSocNetUserToGroup::SendRequestToBeMember($GLOBALS["USER"]->GetID(), $arResult["Group"]["ID"], $_POST["MESSAGE"], $arResult["Urls"]["GroupRequests"], $bAutoSubscribe)
									&& ($e = $APPLICATION->GetException())
								)
									$errorMessage .= $e->GetString();
							}

							if (strlen($errorMessage) > 0)
								$arResult["ErrorMessage"] = $errorMessage;
							else
								$arResult["ShowForm"] = "Confirm";
						}
					}
				}
			}
		}
	}
}
$this->IncludeComponentTemplate();
?>