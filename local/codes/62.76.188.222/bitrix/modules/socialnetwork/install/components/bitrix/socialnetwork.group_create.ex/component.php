<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/bitrix/socialnetwork.group_create.ex/include.php");

if (!CModule::IncludeModule("socialnetwork"))
{
	ShowError(GetMessage("SONET_MODULE_NOT_INSTALL"));
	return;
}

if (intval($_REQUEST["SONET_GROUP_ID"]) > 0)
	$arParams["GROUP_ID"] = intval($_REQUEST["SONET_GROUP_ID"]);
else
	$arParams["GROUP_ID"] = intval($arParams["GROUP_ID"]);

$arParams["SET_NAV_CHAIN"] = ($arParams["SET_NAV_CHAIN"] == "N" ? "N" : "Y");
$bAutoSubscribe = (array_key_exists("USE_AUTOSUBSCRIBE", $arParams) && $arParams["USE_AUTOSUBSCRIBE"] == "N" ? false : true);

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

$arParams["PATH_TO_GROUP_EDIT"] = trim($arParams["PATH_TO_GROUP_EDIT"]);
if (strlen($arParams["PATH_TO_GROUP_EDIT"]) <= 0)
	$arParams["PATH_TO_GROUP_EDIT"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group_edit&".$arParams["GROUP_VAR"]."=#group_id#");

$arParams["PATH_TO_GROUP_CREATE"] = trim($arParams["PATH_TO_GROUP_CREATE"]);
if (strlen($arParams["PATH_TO_GROUP_CREATE"]) <= 0)
	$arParams["PATH_TO_GROUP_CREATE"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=group_create&".$arParams["USER_VAR"]."=#user_id#");

$arParams["IUS_INPUT_NAME"] = "ius_ids";
$arParams["IUS_INPUT_NAME_SUSPICIOUS"] = "ius_susp";
$arParams["IUS_INPUT_NAME_STRING"] = "users_list_string_ius";
$arParams["IUS_INPUT_NAME_EXTRANET"] = "ius_ids_extranet";
$arParams["IUS_INPUT_NAME_SUSPICIOUS_EXTRANET"] = "ius_susp_extranet";
$arParams["IUS_INPUT_NAME_STRING_EXTRANET"] = "users_list_string_ius_extranet";

if (strlen($arParams["NAME_TEMPLATE"]) <= 0)
	$arParams["NAME_TEMPLATE"] = CSite::GetNameFormat();
$bUseLogin = $arParams["SHOW_LOGIN"] != "N" ? true : false;

if ($arParams["USE_KEYWORDS"] != "N") $arParams["USE_KEYWORDS"] = "Y";

$arResult["GROUP_PROPERTIES"] = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("SONET_GROUP", 0, LANGUAGE_ID);

foreach($arResult["GROUP_PROPERTIES"] as $field => $arUserField)
{
	$arResult["GROUP_PROPERTIES"][$field]["EDIT_FORM_LABEL"] = StrLen($arUserField["EDIT_FORM_LABEL"]) > 0 ? $arUserField["EDIT_FORM_LABEL"] : $arUserField["FIELD_NAME"];
	$arResult["GROUP_PROPERTIES"][$field]["EDIT_FORM_LABEL"] = htmlspecialcharsEx($arResult["GROUP_PROPERTIES"][$field]["EDIT_FORM_LABEL"]);
	$arResult["GROUP_PROPERTIES"][$field]["~EDIT_FORM_LABEL"] = $arResult["GROUP_PROPERTIES"][$field]["EDIT_FORM_LABEL"];
}

$arResult["bVarsFromForm"] = false;

$arResult["IS_IFRAME"] = $_GET["IFRAME"] == "Y";
if (in_array($_GET["CALLBACK"], array("REFRESH", "GROUP")))
	$arResult["CALLBACK"] = $_GET["CALLBACK"];

if (strlen($_GET["tab"]) > 0)
	$arResult["TAB"] = $_GET["tab"];

$arResult["POST"]["FEATURES"] = array();
$arResult["POST"]["USER_IDS"] = false;
$arResult["POST"]["EMAILS"] = "";

if (!$GLOBALS["USER"]->IsAuthorized())
	$arResult["NEED_AUTH"] = "Y";
else
{
	$arResult["bIntranet"] = IsModuleInstalled("intranet");
	$arResult["bExtranet"] = (CModule::IncludeModule('extranet') && CExtranet::IsExtranetSite());

	$arResult["isCurrentUserIntranet"] = (!CModule::IncludeModule('extranet') || CExtranet::IsIntranetUser());

	$arResult["POST"] = array();

	if ($arParams["GROUP_ID"] > 0)
		__GCEGetGroup($arParams["GROUP_ID"], $arResult["GROUP_PROPERTIES"], $arResult["POST"], $arResult["TAB"]);
	else
	{
		$arParams["GROUP_ID"] = 0;
		$arResult["POST"]["VISIBLE"] = "Y";
		if ($arResult["bExtranet"])
			$arResult["POST"]["INITIATE_PERMS"] = "E";
		else
			$arResult["POST"]["INITIATE_PERMS"] = "K";
		$arResult["POST"]["SPAM_PERMS"] = "K";
		$arResult["POST"]["IMAGE_ID_IMG"] = '<img src="/bitrix/images/1.gif" height="60" class="sonet-group-create-popup-image" id="sonet_group_create_popup_image" border="0">';
	}

	$arResult["Urls"]["User"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $GLOBALS["USER"]->GetID()));
	$arResult["Urls"]["Group"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP"], array("group_id" => $arParams["GROUP_ID"]));

	if ($arResult["TAB"] != "invite")
	{
		if ($arParams["GROUP_ID"] <= 0)
		{
			if (!CSocNetUser::IsCurrentUserModuleAdmin() && $GLOBALS["APPLICATION"]->GetGroupRight("socialnetwork", false, "Y", "Y", array(SITE_ID, false)) < "K")
				$arResult["FatalError"] = GetMessage("SONET_GCE_ERR_CANT_CREATE").". ";
		}
		elseif (
			strlen($errorMessage) <= 0
			&& $arParams["GROUP_ID"] > 0
			&& $arResult["POST"]["OWNER_ID"] != $GLOBALS["USER"]->GetID()
			&& !CSocNetUser::IsCurrentUserModuleAdmin()
		)
			$arResult["FatalError"] = GetMessage("SONET_GCE_ERR_SECURITY").". ";
	}

	if (StrLen($arResult["FatalError"]) <= 0)
	{
		if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "edit")
			__GCE_GetFeatures($arParams["GROUP_ID"], $arResult["POST"]["FEATURES"]);

		$arResult["ShowForm"] = "Input";
		$arResult["ErrorFields"] = array();

		if ($_SERVER["REQUEST_METHOD"]=="POST" && strlen($_POST["save"]) > 0 && check_bitrix_sessid())
		{
			$errorMessage = "";
			$warningMessage = "";

			if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "edit")
			{
				if (intval($_POST["GROUP_IMAGE_ID"]) > 0)
				{
					if (intval($arResult["POST"]["IMAGE_ID"]) != intval($_POST["GROUP_IMAGE_ID"]))
					{
						$arImageID = CFile::MakeFileArray($_POST["GROUP_IMAGE_ID"]);
						$arImageID["old_file"] = $arResult["POST"]["IMAGE_ID"];
						$arImageID["del"] = "N";
						CFile::ResizeImage($arImageID, array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL);
					}
				}
				else
					$arImageID = array("del" => "Y", "old_file" => $arResult["POST"]["IMAGE_ID"]);

				$arResult["POST"]["NAME"] = $_POST["GROUP_NAME"];
				$arResult["POST"]["DESCRIPTION"] = $_POST["GROUP_DESCRIPTION"];
				$arResult["POST"]["IMAGE_ID_DEL"] = ($_POST["GROUP_IMAGE_ID_DEL"] == "Y" ? "Y" : "N");
				$arResult["POST"]["SUBJECT_ID"] = $_POST["GROUP_SUBJECT_ID"];
				$arResult["POST"]["VISIBLE"] = ($_POST["GROUP_VISIBLE"] == "Y" ? "Y" : "N");
				$arResult["POST"]["OPENED"] = ($_POST["GROUP_OPENED"] == "Y" ? "Y" : "N");
				$arResult["POST"]["IS_EXTRANET_GROUP"] = ($_POST["IS_EXTRANET_GROUP"] == "Y" ? "Y" : "N");
				$arResult["POST"]["CLOSED"] = ($_POST["GROUP_CLOSED"] == "Y" ? "Y" : "N");
				$arResult["POST"]["KEYWORDS"] = $_POST["GROUP_KEYWORDS"];
				$arResult["POST"]["INITIATE_PERMS"] = $_POST["GROUP_INITIATE_PERMS"];
				$arResult["POST"]["SPAM_PERMS"] = $_POST["GROUP_SPAM_PERMS"];

				foreach($arResult["GROUP_PROPERTIES"] as $field => $arUserField)
					if (array_key_exists($field, $_POST))
						$arResult["POST"]["PROPERTIES"][$field] = $_POST[$field];

				if (strlen($_POST["GROUP_NAME"]) <= 0)
				{
					$errorMessage .= GetMessage("SONET_GCE_ERR_NAME").".<br />";
					$arResult["ErrorFields"][] = "GROUP_NAME";
				}
				if (IntVal($_POST["GROUP_SUBJECT_ID"]) <= 0)
				{
					$errorMessage .= GetMessage("SONET_GCE_ERR_SUBJECT").".<br />";
					$arResult["ErrorFields"][] = "GROUP_SUBJECT_ID";
				}
				if (strlen($_POST["GROUP_INITIATE_PERMS"]) <= 0)
				{
					$errorMessage .= GetMessage("SONET_GCE_ERR_PERMS").".<br />";
					$arResult["ErrorFields"][] = "GROUP_INITIATE_PERMS";
				}
				if (strlen($_POST["GROUP_SPAM_PERMS"]) <= 0)
				{
					$errorMessage .= GetMessage("SONET_GCE_ERR_SPAM_PERMS").".<br />";
					$arResult["ErrorFields"][] = "GROUP_SPAM_PERMS";
				}

				foreach ($arResult["POST"]["FEATURES"] as $feature => $arFeature)
					$arResult["POST"]["FEATURES"][$feature]["Active"] = ($_POST[$feature."_active"] == "Y");
			}

			if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "invite")
			{
				if ($arResult["bIntranet"])
				{
					if (
						is_array($_POST["USER_IDS"])
						&& count($_POST["USER_IDS"]) > 0
					)
						$arResult["POST"]["USER_IDS"] = $_POST["USER_IDS"];

					//adding e-mail from the input field to the list
					if (array_key_exists("EMAIL", $_POST) && strlen($_POST["EMAIL"]) > 0 && check_email($_POST["EMAIL"]))
						$_POST["EMAILS"] .= (empty($_POST["EMAILS"]) ? "" : ", ").trim($_POST["EMAIL"]);

					if (array_key_exists("EMAILS", $_POST))
						$arResult["POST"]["EMAILS"] = $_POST["EMAILS"];
				}
				else
				{
					$arUserIDs = array();

					$arUsersListTmp = Explode(",", $_POST["users_list"]);
					foreach ($arUsersListTmp as $userTmp)
					{
						$userTmp = Trim($userTmp);
						if (StrLen($userTmp) > 0)
							$arUsersList[] = $userTmp;
					}

					if ($arResult["TAB"] == "invite" && Count($arUsersList) <= 0)
					{
						$errorMessage .= GetMessage("SONET_GCE_NO_USERS").". ";
						$arResult["ErrorFields"][] = "USERS";
					}

					if (StrLen($errorMessage) <= 0)
						foreach ($arUsersList as $user)
						{
							$arFoundUsers = CSocNetUser::SearchUser($user);
							if ($arFoundUsers && is_array($arFoundUsers) && count($arFoundUsers) > 0)
								foreach ($arFoundUsers as $userID => $userName)
									if (intval($userID) > 0)
										$arUserIDs[] = $userID;
						}

					$arResult["POST"]["USER_IDS"] = $arUserIDs;
				}
			}

			if ((!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "edit") && strlen($errorMessage) <= 0)
			{
				$arFields = array(
					"NAME" => $_POST["GROUP_NAME"],
					"DESCRIPTION" => $_POST["GROUP_DESCRIPTION"],
					"VISIBLE" => ($_POST["GROUP_VISIBLE"] == "Y" ? "Y" : "N"),
					"OPENED" => ($_POST["GROUP_OPENED"] == "Y" ? "Y" : "N"),
					"CLOSED" => ($_POST["GROUP_CLOSED"] == "Y" ? "Y" : "N"),
					"SUBJECT_ID" => $_POST["GROUP_SUBJECT_ID"],
					"KEYWORDS" => $_POST["GROUP_KEYWORDS"],
					"IMAGE_ID" => $arImageID,
					"INITIATE_PERMS" => $_POST["GROUP_INITIATE_PERMS"],
					"SPAM_PERMS" => $_POST["GROUP_SPAM_PERMS"],
				);

				if (!CModule::IncludeModule("extranet") || !CExtranet::IsExtranetSite())
				{
					$arFields["SITE_ID"] = array(SITE_ID);
					if (CModule::IncludeModule("extranet") && !CExtranet::IsExtranetSite() && $_POST["IS_EXTRANET_GROUP"] == "Y")
					{
						$arFields["SITE_ID"][] = CExtranet::GetExtranetSiteID();
						$arFields["VISIBLE"] = "N";
						$arFields["OPENED"] = "N";
					}
				}
				elseif(CModule::IncludeModule("extranet") && CExtranet::IsExtranetSite())
				{
					$arFields["SITE_ID"] = array(SITE_ID, CSite::GetDefSite());
				}

				foreach($arResult["GROUP_PROPERTIES"] as $field => $arUserField)
					if (array_key_exists($field, $_POST))
						$arFields[$field] = $_POST[$field];

				$GLOBALS["USER_FIELD_MANAGER"]->EditFormAddFields("SONET_GROUP", $arFields);

				if ($arParams["GROUP_ID"] <= 0)
				{
					if (CModule::IncludeModule("extranet") && CExtranet::IsExtranetSite())
						$arFields["SITE_ID"][] = CSite::GetDefSite();

					$arResult["GROUP_ID"] = CSocNetGroup::CreateGroup($GLOBALS["USER"]->GetID(), $arFields, $bAutoSubscribe);
					if (!$arResult["GROUP_ID"])
					{
						if ($e = $APPLICATION->GetException())
						{
							$errorMessage .= $e->GetString();
							$errorID = $e->GetID();
							if (strlen($errorID) > 0)
								$arResult["ErrorFields"][] = $errorID;
						}
					}
					else
						$bFirstStepSuccess = true;
				}
				else
				{
					$arFields["=DATE_UPDATE"] = $GLOBALS["DB"]->CurrentTimeFunction();
					$arFields["=DATE_ACTIVITY"] = $GLOBALS["DB"]->CurrentTimeFunction();

					$arResult["GROUP_ID"] = CSocNetGroup::Update($arParams["GROUP_ID"], $arFields, $bAutoSubscribe);

					if (!$arResult["GROUP_ID"] && ($e = $APPLICATION->GetException()))
					{
						$errorMessage .= $e->GetString();
						$errorID = $e->GetID();
						if ($errorID == "ERROR_IMAGE_ID")
							$arResult["ErrorFields"][] = "GROUP_IMAGE_ID";
						elseif (isset($e->messages) && is_array($e->messages) && is_array($e->messages[0]) && array_key_exists("id", $e->messages[0]))
							$arResult["ErrorFields"][] = $e->messages[0]["id"];
					}
					else
					{
						$rsSite = CSite::GetList($by="sort", $order="desc", Array("ACTIVE" => "Y"));
						while($arSite = $rsSite->Fetch())
							BXClearCache(true, $arSite["ID"]."/bitrix/search.tags.cloud/");
					}
				}
			}

			if (strlen($errorMessage) <= 0 && array_key_exists("TAB", $arResult) && $arResult["TAB"] != "edit")
				$arResult["GROUP_ID"] = $arParams["GROUP_ID"];

			if (StrLen($arImageID["tmp_name"]) > 0)
				CFile::ResizeImageDeleteCache($arImageID);

			if (strlen($errorMessage) > 0)
			{
				$arResult["ErrorMessage"] = $errorMessage;
				$arResult["bVarsFromForm"] = true;
			}
			elseif ($arResult["GROUP_ID"] > 0)
			{
				/* features */
				if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "edit")
				{
					foreach ($arResult["POST"]["FEATURES"] as $feature => $arFeature)
					{
						$idTmp = CSocNetFeatures::SetFeature(
							SONET_ENTITY_GROUP,
							$arResult["GROUP_ID"],
							$feature,
							($_POST[$feature."_active"] == "Y") ? true : false,
							(strlen($arFeature["FeatureName"]) > 0) ? $arFeature["FeatureName"] : false
						);

						if (!$idTmp)
						{
							if ($e = $APPLICATION->GetException())
								$errorMessage .= $e->GetString();
						}
						else
							$bSecondStepSuccess = true;
					}
				}

				/* invite */
				if (strlen($errorMessage) <= 0 && (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "invite"))
				{
					$arUsersList = array();

					if (
						is_array($_POST["USER_IDS"])
						&& count($_POST["USER_IDS"]) > 0
					)
						$arUserIDs = $_POST["USER_IDS"];

					if (CModule::IncludeModule('extranet') && strlen($_POST["EMAILS"]) > 0)
					{
						$arEmail = array();
						$arEmailsOfExistingUsers = array();
						$arEmailOriginal = preg_split("/[\n\r\t\,;]+/", $_POST["EMAILS"]);

						foreach($arEmailOriginal as $addr)
						{
							if(strlen($addr) > 0 && check_email($addr))
							{
								$addrX = "";
								$phraseX = "";
								$white_space = "(?:(?:\\r\\n)?[ \\t])";
								$spec = '()<>@,;:\\\\".\\[\\]';
								$cntl = '\\000-\\037\\177';
								$dtext = "[^\\[\\]\\r\\\\]";
								$domain_literal = "\\[(?:$dtext|\\\\.)*\\]$white_space*";
								$quoted_string = "\"(?:[^\\\"\\r\\\\]|\\\\.|$white_space)*\"$white_space*";
								$atom = "[^$spec $cntl]+(?:$white_space+|\\Z|(?=[\\[\"$spec]))";
								$word = "(?:$atom|$quoted_string)";
								$localpart = "$word(?:\\.$white_space*$word)*";
								$sub_domain = "(?:$atom|$domain_literal)";
								$domain = "$sub_domain(?:\\.$white_space*$sub_domain)*";
								$addr_spec = "$localpart\@$white_space*$domain";
								$phrase = "$word*";

								if(preg_match("/$addr_spec/", $addr, $arMatches))
									$addrX = $arMatches[0];

								if(preg_match("/$localpart/", $addr, $arMatches))
									$phraseX = trim(trim($arMatches[0]), "\"");

								$arEmail[] = array("EMAIL" => $addrX, "NAME" => $phraseX);
							}
						}

						if (count($arEmail) > 0)
						{
							$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
							if($def_group != "")
							{
								$GROUP_ID = explode(",", $def_group);
								$arPolicy = $USER->GetGroupPolicy($GROUP_ID);
							}
							else
								$arPolicy = $USER->GetGroupPolicy(array());

							$password_min_length = intval($arPolicy["PASSWORD_LENGTH"]);
							if($password_min_length <= 0)
								$password_min_length = 6;
							$password_chars = array(
								"abcdefghijklnmopqrstuvwxyz",
								"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
								"0123456789",
							);

							if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
								$password_chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";

							foreach($arEmail as $email)
							{
								$arFilter = array(
									"ACTIVE" => "Y",
									"=EMAIL" => $email["EMAIL"]
								);

								$rsUser = CUser::GetList(($by="id"), ($order="asc"), $arFilter, array("SELECT" => array("UF_DEPARTMENT")));
								if ($arUser = $rsUser->Fetch())
								{
									//if user with this e-mail is registered, but is external user
									if ((sizeof($arUser["UF_DEPARTMENT"]) == 0) && IsModuleInstalled("extranet"))
									{
										$arUserIDs[] = $userID = $arUser["ID"];
										$checkword 	= $arUser["CHECKWORD"];
									}
									else
									{
										$arEmailsOfExistingUsers[] = $email["EMAIL"];
										continue;
									}
								}
								else
								{
									//creating user with specified e-mail
									$password = randString($password_min_length, $password_chars);
									$checkword = randString(8);

									$name = $last_name = "";
									if (strlen($email["NAME"]) > 0)
										list($name, $last_name) = explode(" ", $email["NAME"]);

									$arFields = array(
										"EMAIL" => $email["EMAIL"],
										"LOGIN" => $email["EMAIL"],
										"NAME" => $name,
										"LAST_NAME" => $last_name,
										"ACTIVE" => "Y",
										"GROUP_ID" => (CExtranet::GetExtranetUserGroupID() > 0 ? array(2, CExtranet::GetExtranetUserGroupID()) : array(2)),
										"PASSWORD" => $password,
										"CONFIRM_PASSWORD" => $password,
										"CHECKWORD" => $checkword,
										"LID" => SITE_ID
									);

									$user = new CUser;
									$NEW_USER_ID = $user->Add($arFields);

									if (intval($NEW_USER_ID) > 0)
										$arUserIDs[] = $userID = $NEW_USER_ID;
									else
									{
										$strError = $user->LAST_ERROR;
										if ($GLOBALS["APPLICATION"]->GetException())
										{
											$err = $GLOBALS["APPLICATION"]->GetException();
											$strError .= $err->GetString();
											$GLOBALS["APPLICATION"]->ResetException();
										}

										$warningMessage .= str_replace("#EMAIL#", HtmlSpecialCharsEx($email["EMAIL"]), GetMessage("SONET_GCE_CANNOT_USER_ADD").$strError);
									}
								}

								//sending invitation to user ($userID) with this e-mail
								if ($userID > 0)
								{
									$event = new CEvent;
									$arFields = Array(
										"USER_ID" => $userID,
										"CHECKWORD" => $checkword,
										"EMAIL" => $email["EMAIL"]
									);

									$event->Send("EXTRANET_INVITATION", SITE_ID, $arFields);
								}
							}
						}
					}

					if (is_array($arUserIDs) && count($arUserIDs) > 0)
					{
						foreach($arUserIDs as $user_id)
						{
							$isCurrentUserTmp = ($GLOBALS["USER"]->GetID() == $user_id);
							$canInviteGroup = CSocNetUserPerms::CanPerformOperation($GLOBALS["USER"]->GetID(), $user_id, "invitegroup", CSocNetUser::IsCurrentUserModuleAdmin());
							$user2groupRelation = CSocNetUserToGroup::GetUserRole($user_id, $arResult["GROUP_ID"]);

							if (!$isCurrentUserTmp && $canInviteGroup && !$user2groupRelation)
							{
								if (!CSocNetUserToGroup::SendRequestToJoinGroup($GLOBALS["USER"]->GetID(), $user_id, $arResult["GROUP_ID"], $_POST["MESSAGE"]))
								{
									$rsUser = CUser::GetByID($user_id);
									if ($arUser = $rsUser->Fetch())
									{
										$arErrorUsers[] = array(
											CUser::FormatName($arParams["NAME_TEMPLATE"], $arUser, $bUseLogin),
											CSocNetUserPerms::CanPerformOperation($GLOBALS["USER"]->GetID(), $arUser["ID"], "viewprofile", CSocNetUser::IsCurrentUserModuleAdmin())
												? CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $arUser["ID"]))
												: ""
										);
										if ($e = $APPLICATION->GetException())
											$warningMessage .= $e->GetString();
									}
								}
							}
						}
					}
				}

				//if some e-mails belong to internal users and can't be used for invitation
				if (count($arEmailsOfExistingUsers) == 1)
					$warningMessage .= str_replace("#EMAIL#", HtmlSpecialCharsEx(implode("", $arEmailsOfExistingUsers)), GetMessage("SONET_GCE_CANNOT_EMAIL_ADD"));
				elseif (count($arEmailsOfExistingUsers) > 1)
					$warningMessage .= str_replace("#EMAIL#", HtmlSpecialCharsEx(implode(", ", $arEmailsOfExistingUsers)), GetMessage("SONET_GCE_CANNOT_EMAILS_ADD"));

				//if no users were invited
				if ($arResult["TAB"] == "invite" && (!is_array($arUserIDs) || count($arUserIDs) <= 0))
				{
					$errorMessage .= GetMessage("SONET_GCE_NO_USERS").". ";
					$arResult["ErrorFields"][] = "USERS";
				}
			}

			if (strlen($errorMessage) <= 0 && strlen($warningMessage) <= 0)
			{
				if (!array_key_exists("TAB", $arResult))
					$redirectPath = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_CREATE"], array("group_id" => $arResult["GROUP_ID"], "user_id" => $GLOBALS["USER"]->GetID()));
				elseif ($arResult["TAB"] == "edit")
					$redirectPath = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_EDIT"], array("group_id" => $arResult["GROUP_ID"], "user_id" => $GLOBALS["USER"]->GetID()));
				elseif ($arResult["TAB"] == "invite")
					$redirectPath = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_EDIT"], array("group_id" => $arResult["GROUP_ID"], "user_id" => $GLOBALS["USER"]->GetID()));

				if ($arResult["IS_IFRAME"])
				{
					$redirectPath .= (strpos($redirectPath, "?") === false ? "?" :  "&")."IFRAME=Y&SONET=Y";
					if ($arResult["TAB"] == "invite")
						$redirectPath .= (strpos($redirectPath, "?") === false ? "?" :  "&")."tab=invite";
					elseif ($arResult["TAB"] == "edit")
						$redirectPath .= (strpos($redirectPath, "?") === false ? "?" :  "&")."tab=edit";

					if ($bFirstStepSuccess)
						$redirectPath .= "&CALLBACK=GROUP&GROUP_ID=".$arResult["GROUP_ID"];
					else
						$redirectPath .= "&CALLBACK=REFRESH";
				}

				$APPLICATION->RestartBuffer();
				LocalRedirect($redirectPath);
			}
			else
			{
				$arResult["WarningMessage"] = $warningMessage;
				$arResult["ErrorMessage"] = $errorMessage;

				if (!array_key_exists("TAB", $arResult))
				{
					if ($bFirstStepSuccess)
					{
						__GCEGetGroup($arResult["GROUP_ID"], $arResult["GROUP_PROPERTIES"], $arResult["POST"]);
						$arResult["CALLBACK"] = "EDIT";
					}

					if ($bSecondStepSuccess)
						__GCE_GetFeatures($arResult["GROUP_ID"], $arResult["POST"]["FEATURES"]);
				}
			}
		}
		else
			$arResult["GROUP_ID"] = $arParams["GROUP_ID"];

		if ($arResult["ShowForm"] == "Input")
		{
			if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "edit")
			{
				$arResult["Subjects"] = array();
				$dbSubjects = CSocNetGroupSubject::GetList(
					array("SORT"=>"ASC", "NAME" => "ASC"),
					array("SITE_ID" => SITE_ID),
					false,
					false,
					array("ID", "NAME")
				);
				while ($arSubject = $dbSubjects->GetNext())
					$arResult["Subjects"][$arSubject["ID"]] = $arSubject["NAME"];

				$arResult["InitiatePerms"] = array(
					SONET_ROLES_OWNER => GetMessage("SONET_GCE_IP_OWNER"),
					SONET_ROLES_MODERATOR => GetMessage("SONET_GCE_IP_MOD"),
					SONET_ROLES_USER => GetMessage("SONET_GCE_IP_USER"),
				);

				$arResult["SpamPerms"] = array(
					SONET_ROLES_OWNER => GetMessage("SONET_GCE_IP_OWNER"),
					SONET_ROLES_MODERATOR => GetMessage("SONET_GCE_IP_MOD"),
					SONET_ROLES_USER => GetMessage("SONET_GCE_IP_USER"),
					SONET_ROLES_ALL => GetMessage("SONET_GCE_IP_ALL"),
				);
			}
		}
	}
}

if ($arResult["IS_IFRAME"])
	SonetShowInFrame($this);
else
	$this->IncludeComponentTemplate();
?>