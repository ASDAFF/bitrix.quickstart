<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("socialnetwork"))
{
	ShowError(GetMessage("SONET_MODULE_NOT_INSTALL"));
	return;
}

$arParams["ID"] = IntVal($arParams["ID"]);
if(strLen($arParams["USER_VAR"])<=0)
	$arParams["USER_VAR"] = "id";
if(strLen($arParams["PAGE_VAR"])<=0)
	$arParams["PAGE_VAR"] = "page";
$arParams["PATH_TO_USER"] = trim($arParams["PATH_TO_USER"]);
if(strlen($arParams["PATH_TO_USER"])<=0)
	$arParams["PATH_TO_USER"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user&".$arParams["USER_VAR"]."=#user_id#");
if(strlen($arParams["PATH_TO_USER_EDIT"])<=0)
	$arParams["PATH_TO_USER_EDIT"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user&".$arParams["USER_VAR"]."=#user_id#&mode=edit");
$arParams["DATE_TIME_FORMAT"] = trim(empty($arParams["DATE_TIME_FORMAT"]) ? $DB->DateFormatToPHP(CSite::GetDateFormat("FULL")) : $arParams["DATE_TIME_FORMAT"]);

$arParams['IS_FORUM'] = CModule::IncludeModule('forum') ? 'Y' : 'N';
$arParams['IS_BLOG'] = CModule::IncludeModule('blog') ? 'Y' : 'N';

TrimArr($arParams['USER_FIELDS_PERSONAL']);
TrimArr($arParams['USER_FIELDS_CONTACT']);
TrimArr($arParams['USER_FIELDS_MAIN']);
TrimArr($arParams['USER_PROPERTY_PERSONAL']);
TrimArr($arParams['USER_PROPERTY_CONTACT']);
TrimArr($arParams['USER_PROPERTY_MAIN']);
TrimArr($arParams['EDITABLE_FIELDS']);

if (!is_array($arParams['EDITABLE_FIELDS']) || count($arParams['EDITABLE_FIELDS']) <= 0)
{
	$arParams['EDITABLE_FIELDS'] = array('LOGIN', 'NAME', 'SECOND_NAME', 'LAST_NAME', 'EMAIL', 'TIME_ZONE', 'PERSONAL_BIRTHDAY', 'PERSONAL_CITY', 'PERSONAL_COUNTRY', 'PERSONAL_FAX', 'PERSONAL_GENDER', 'PERSONAL_ICQ', 'PERSONAL_MAILBOX', 'PERSONAL_MOBILE', 'PERSONAL_PAGER', 'PERSONAL_PHONE', 'PERSONAL_PHOTO', 'PERSONAL_STATE', 'PERSONAL_STREET', 'PERSONAL_WWW', 'PERSONAL_ZIP');

	if ($arParams['IS_FORUM'] == 'Y')
		$arParams['EDITABLE_FIELDS'] = array_merge($arParams['EDITABLE_FIELDS'], array('FORUM_SHOW_NAME', 'FORUM_DESCRIPTION', 'FORUM_INTERESTS', 'FORUM_SIGNATURE', 'FORUM_AVATAR', 'FORUM_HIDE_FROM_ONLINE', 'FORUM_SUBSC_GROUP_MESSAGE', 'FORUM_SUBSC_GET_MY_MESSAGE'));

	if ($arParams['IS_BLOG'] == 'Y')
		$arParams['EDITABLE_FIELDS'] = array_merge($arParams['EDITABLE_FIELDS'], array('BLOG_ALIAS', 'BLOG_DESCRIPTION', 'BLOG_INTERESTS', 'BLOG_AVATAR', 'BLOG_SIGNATURE'));
}

if(in_array('TIME_ZONE', $arParams['EDITABLE_FIELDS']))
	$arParams['EDITABLE_FIELDS'][] = 'AUTO_TIME_ZONE';

$arResult["urlToCancel"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $arParams["ID"]));

$CurrentUserPerms = CSocNetUserPerms::InitUserPerms($USER->GetID(), $arParams["ID"], CSocNetUser::IsCurrentUserModuleAdmin(SITE_ID, (CModule::IncludeModule("bitrix24") && CBitrix24::IsPortalAdmin($USER->GetID()) ? false : true)));
if (!$CurrentUserPerms["Operations"]["modifyuser"] || !$CurrentUserPerms["Operations"]["modifyuser_main"])
	$arParams['ID'] = $USER->GetID();

$arResult["bEdit"] = ($USER->CanDoOperation('edit_own_profile') || $USER->IsAdmin()) ? "Y" : "N";

if ($arResult['bEdit'] != 'Y')
	$APPLICATION->AuthForm(GetMessage('SONET_P_PU_NO_RIGHTS'));

if (isset($_GET["ACTIVE"]))
{
	if ($CurrentUserPerms["Operations"]["modifyuser_main"] && $arResult['bEdit'] == 'Y' && $arParams["ID"] != $USER->GetID() && !(IsModuleInstalled("bitrix24") && $arParams["ID"] == "1"))		
	{
		if ($_GET["ACTIVE"] == "D"):
			$USER->Delete($arParams["ID"]);	
		else:
			if ($_GET["ACTIVE"] == "N")
				$arFields["ACTIVE"] = "N";
			elseif ($_GET["ACTIVE"] == "Y")
				$arFields["ACTIVE"] = "Y";		
			$res = $USER->Update($arParams["ID"], $arFields);	
		endif;
	}

	$APPLICATION->RestartBuffer(); 
	die();
}

$dbUser = CUser::GetByID($arParams["ID"]);
$arResult["User"] = $dbUser->GetNext();

if ($arResult['User']['EXTERNAL_AUTH_ID'])
{
	foreach ($arParams['EDITABLE_FIELDS'] as $key => $value)
		if ($value == 'LOGIN' || $value == 'PASSWORD')
			unset($arParams['EDITABLE_FIELDS'][$key]);
}
elseif (in_array('PASSWORD', $arParams['EDITABLE_FIELDS']))
	$arParams['EDITABLE_FIELDS'][] = 'CONFIRM_PASSWORD';

if(!is_array($arResult["User"]))
	$arResult["FATAL_ERROR"] = GetMessage("SONET_P_USER_NO_USER");
else
{
	$arResult["GROUPS_CAN_EDIT"] = array();

	if ($USER->CanDoOperation('edit_all_users') || $USER->CanDoOperation('edit_subordinate_users'))
	{
		if($USER->CanDoOperation('edit_all_users'))
		{
			$dbGroup = CGroup::GetList(($by="c_sort"), ($order="asc"), array("ACTIVE" => "Y"));
			while($arGroup = $dbGroup->Fetch())
			{
				$arResult["GROUPS_CAN_EDIT"][$arGroup["ID"]] = $arGroup;
				$arGroupsCanEditID[] = $arGroup["ID"];
			}
		}
		elseif($USER->CanDoOperation('edit_subordinate_users'))
		{
			if (array_key_exists("SONET_SUBORD_GROUPS_BY_USER_ID", $GLOBALS) && array_key_exists($GLOBALS["USER"]->GetID(), $GLOBALS["SONET_SUBORD_GROUPS_BY_USER_ID"]))
				$arUserSubordinateGroups = $GLOBALS["SONET_SUBORD_GROUPS_BY_USER_ID"][$GLOBALS["USER"]->GetID()];
			else
			{
				$arUserSubordinateGroups = Array(2);
				$arUserGroups_u = CUser::GetUserGroupArray();
				for ($j = 0,$len = count($arUserGroups_u); $j < $len; $j++)
				{
					$arSubordinateGroups = CGroup::GetSubordinateGroups($arUserGroups_u[$j]);
					$arUserSubordinateGroups = array_merge($arUserSubordinateGroups, $arSubordinateGroups);
				}
				$arUserSubordinateGroups = array_unique($arUserSubordinateGroups);

				if (!array_key_exists("SONET_SUBORD_GROUPS_BY_USER_ID", $GLOBALS))
					$GLOBALS["SONET_SUBORD_GROUPS_BY_USER_ID"] = array();

				$GLOBALS["SONET_SUBORD_GROUPS_BY_USER_ID"][$GLOBALS["USER"]->GetID()] = $arUserSubordinateGroups;
			}

			$arGroupsCanEditID = $arUserSubordinateGroups;
			
			if (is_array($arGroupsCanEditID) && count($arGroupsCanEditID) > 0)
			{
				$dbGroup = CGroup::GetList(($by="c_sort"), ($order="asc"), array("ID" => implode(" | ", $arGroupsCanEditID), "ACTIVE" => "Y"));
				while($arGroup = $dbGroup->Fetch())
					$arResult["GROUPS_CAN_EDIT"][$arGroup["ID"]] = $arGroup;
			}
		}
	}
	else
	{
		foreach ($arParams['EDITABLE_FIELDS'] as $key => $value)
			if ($value == 'GROUP_ID' || $value == 'ACTIVE')
				unset($arParams['EDITABLE_FIELDS'][$key]);
	}
	
	if ($arParams['IS_FORUM'] == 'Y')
	{
		$arForumUser = CForumUser::GetByUSER_ID($arParams["ID"]);
		if (is_array($arForumUser) && count($arForumUser) > 0)
		{
			foreach ($arForumUser as $key => $value)
			{
				if (true || in_array('FORUM_'.$key, $arParams['EDITABLE_FIELDS']))
				{
					$arResult['User']['FORUM_'.$key] = htmlspecialcharsbx($value);
					$arResult['User']['~FORUM_'.$key] = $value;
				}
			}
		}
	}

	if ($arParams['IS_BLOG'] == 'Y')
	{
		$dbRes = CBlogUser::GetList(array(), array("USER_ID" => $arParams['ID']));
		if ($arBlogUser = $dbRes->Fetch())
		{
			foreach ($arBlogUser as $key => $value)
			{
				$arResult['User']['BLOG_'.$key] = htmlspecialcharsbx($value);
				$arResult['User']['~BLOG_'.$key] = $value;
			}
		}
	}

	$SONET_USER_ID = $arParams['ID'];//intval($_POST["SONET_USER_ID"]);

	if($arResult['bEdit'] == 'Y' && $_SERVER["REQUEST_METHOD"]=="POST" && strlen($_POST["submit"])>0 && check_bitrix_sessid())
	{
		if ($_POST['PERSONAL_PHOTO_ID'])
			$arPICTURE = CFile::MakeFileArray($_POST['PERSONAL_PHOTO_ID']);
		else
			$arPICTURE = $_FILES["PERSONAL_PHOTO"];
		$arPICTURE["old_file"] = $arResult["User"]["PERSONAL_PHOTO"];
		$arPICTURE["del"] = $_POST["PERSONAL_PHOTO_del"];

		$arPICTURE_WORK = $_FILES["WORK_LOGO"];
		$arPICTURE_WORK["old_file"] = $arResult["User"]["WORK_LOGO"];
		$arPICTURE_WORK["del"] = $_POST["WORK_LOGO_del"];

		$arFields = Array(
			'ACTIVE', 'GROUP_ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'PERSONAL_PHOTO', 'PERSONAL_GENDER', 'PERSONAL_BIRTHDAY', 'PERSONAL_BIRTHDATE', 'PERSONAL_PROFESSION', 'PERSONAL_NOTES',
			'EMAIL', 'PERSONAL_PHONE', 'PERSONAL_MOBILE', 'PERSONAL_WWW', 'PERSONAL_ICQ', 'PERSONAL_FAX', 'PERSONAL_PAGER', 'PERSONAL_COUNTRY', 'PERSONAL_STREET', 'PERSONAL_MAILBOX', 'PERSONAL_CITY', 'PERSONAL_STATE', 'PERSONAL_ZIP',
			'WORK_COUNTRY', 'WORK_CITY', 'WORK_COMPANY', 'WORK_DEPARTMENT', 'WORK_PROFILE', 'WORK_WWW', 'WORK_PHONE', 'WORK_FAX', 'WORK_PAGER', 'WORK_LOGO', 'WORK_POSITION',
			'LOGIN', 'PASSWORD', 'CONFIRM_PASSWORD',
		);

		$arFieldsValue = array();
		foreach ($arFields as $key)
		{
			if ('PERSONAL_PHOTO' == $key)
				$arFieldsValue[$key] = $arPICTURE;
			elseif ('WORK_LOGO' == $key)
				$arFieldsValue[$key] = $arPICTURE_WORK;
			elseif ('GROUP_ID' == $key)
			{
				if (is_array($arGroupsCanEditID) && !(IsModuleInstalled("bitrix24") && $SONET_USER_ID == "1"))
					$arFieldsValue[$key] = array_intersect($_POST[$key], $arGroupsCanEditID);
			}
			elseif ($_POST[$key] !== $arResult['User'][$key])
				$arFieldsValue[$key] = $_POST[$key];
		}

		//time zones
		$arFieldsValue['AUTO_TIME_ZONE'] = ($_POST['AUTO_TIME_ZONE'] == "Y" || $_POST['AUTO_TIME_ZONE'] == "N"? $_POST['AUTO_TIME_ZONE'] : "");
		if(isset($_POST['TIME_ZONE']))
			$arFieldsValue['TIME_ZONE'] = $_POST['TIME_ZONE'];

		if (strlen($arFieldsValue['PASSWORD']) <= 0)
			unset($arFieldsValue['PASSWORD']); unset($arFieldsValue['CONFIRM_PASSWORD']);

		$GLOBALS["USER_FIELD_MANAGER"]->EditFormAddFields("USER", $arFieldsValue);

		if (in_array('PASSWORD', $arParams['EDITABLE_FIELDS']))
			$arParams['EDITABLE_FIELDS'][] = 'CONFIRM_PASSWORD';
		$arKeys = array_intersect(array_keys($arFieldsValue), $arParams['EDITABLE_FIELDS']);

		$arNewFieldsValue = array();
		foreach ($arKeys as $key)
			$arNewFieldsValue[$key] = $arFieldsValue[$key];

		$res = $USER->Update($SONET_USER_ID, $arNewFieldsValue);

		if (!$res)
			$strErrorMessage = $USER->LAST_ERROR;
		else
		{
			if ($arParams['IS_FORUM'] == 'Y')
			{
				$arForumFields = array(
					"SHOW_NAME" => ($_POST["FORUM_SHOW_NAME"]=="Y") ? "Y" : "N",
					"HIDE_FROM_ONLINE" => ($_POST["FORUM_HIDE_FROM_ONLINE"]=="Y") ? "Y" : "N",
					"SUBSC_GROUP_MESSAGE" => ($_POST["FORUM_SUBSC_GROUP_MESSAGE"]=="Y") ? "Y" : "N",
					"SUBSC_GET_MY_MESSAGE" => ($_POST["FORUM_SUBSC_GET_MY_MESSAGE"]=="Y") ? "Y" : "N",
					"DESCRIPTION" => $_POST["FORUM_DESCRIPTION"],
					"INTERESTS" => $_POST["FORUM_INTERESTS"],
					"SIGNATURE" => $_POST["FORUM_SIGNATURE"],
					"AVATAR" => $_FILES["FORUM_AVATAR"]
				);

				foreach ($arForumFields as $key => $value)
					if (!in_array('FORUM_'.$key, $arParams['EDITABLE_FIELDS']))
						unset($arForumFields[$key]);

				if (count($arForumFields) > 0)
				{
					if (isset($arForumFields['AVATAR']))
					{
						$arForumFields["AVATAR"]["del"] = $_POST["FORUM_AVATAR_del"];
						$arForumFields["AVATAR"]["old_file"] = $arResult['User']['FORUM_AVATAR'];
					}

					if ($arResult['User']['FORUM_ID'])
						$FID = CForumUser::Update($arResult['User']['FORUM_ID'], $arForumFields);
					else
					{
						$arForumFields["USER_ID"] = $arResult["User"]['ID'];
						$FID = CForumUser::Add($arForumFields);
					}

					if (!$FID && ($ex = $APPLICATION->GetException()))
						$strErrorMessage = $ex->GetString();
				}
			}

			if (strlen($strErrorMessage) <= 0 && $arParams['IS_BLOG'] == 'Y')
			{
				$arBlogFields = Array(
					"ALIAS" => $_POST['BLOG_ALIAS'],
					"DESCRIPTION" => $_POST['BLOG_DESCRIPTION'],
					"INTERESTS" => $_POST['BLOG_INTERESTS'],
					"AVATAR" => $_FILES["BLOG_AVATAR"]
				);

				foreach ($arBlogFields as $key => $value)
					if (!in_array('BLOG_'.$key, $arParams['EDITABLE_FIELDS']))
						unset($arBlogFields[$key]);

				if (isset($arBlogFields['AVATAR']))
				{
					$arBlogFields["AVATAR"]["del"] = $_POST['BLOG_AVATAR_del'];
					$arBlogFields["AVATAR"]["old_file"] = $arResult['User']["BLOG_AVATAR"];
				}

				if (count($arBlogFields) > 0)
				{
					if ($arResult['User']['BLOG_ID'])
						$BID = CBlogUser::Update($arResult['User']['BLOG_ID'], $arBlogFields);
					else
					{
						$arBlogFields["USER_ID"] = $arParams['ID'];
						$arBlogFields["~DATE_REG"] = CDatabase::CurrentTimeFunction();
						$BID = CBlogUser::Add($arBlogFields);
					}

					if (!$BID && ($ex = $APPLICATION->GetException()))
						$strErrorMessage = $ex->GetString();
				}
			}
		}

		if(strlen($strErrorMessage)<=0)
			if ($_REQUEST['backurl'])
				LocalRedirect($_REQUEST['backurl']);
			else
				LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $arParams["ID"])));
		else
		{
			$arResult["ERROR_MESSAGE"] = $strErrorMessage;
			$bVarsFromForm = true;
		}
	}
	
	if($arResult['bEdit'] == 'Y' && $_SERVER["REQUEST_METHOD"]=="POST" && (strlen($_POST["submit_fire"])>0 || strlen($_POST["submit_recover"])>0) && check_bitrix_sessid())
	{
		if ($CurrentUserPerms["Operations"]["modifyuser_main"] && $SONET_USER_ID != $USER->GetID())		
		{
			$arFields = array("ACTIVE" => strlen($_POST["submit_fire"])>0 ? "N" : "Y");		
			$res = $USER->Update($SONET_USER_ID, $arFields);	
			$arResult["User"]["ACTIVE"] = strlen($_POST["submit_fire"])>0 ? "N" : "Y";
		}
	}

	$arResult["User"]["PERSONAL_LOCATION"] = GetCountryByID($arResult["User"]["PERSONAL_COUNTRY"]);
	if (strlen($arResult["User"]["PERSONAL_LOCATION"])>0 && strlen($arResult["User"]["PERSONAL_CITY"])>0)
		$arResult["User"]["PERSONAL_LOCATION"] .= ", ";
	$arResult["User"]["PERSONAL_LOCATION"] .= $arResult["User"]["PERSONAL_CITY"];
	$arResult["User"]["WORK_LOCATION"] = GetCountryByID($arResult["User"]["WORK_COUNTRY"]);
	if (strlen($arResult["User"]["WORK_LOCATION"])>0 && strlen($arResult["User"]["WORK_CITY"])>0)
		$arResult["User"]["WORK_LOCATION"] .= ", ";
	$arResult["User"]["WORK_LOCATION"] .= $arResult["User"]["WORK_CITY"];

	if ($USER->CanDoOperation('edit_all_users') || $USER->CanDoOperation('edit_subordinate_users'))
	{
		$arResult["User"]["GROUP_ID"] = array();
		$rsGroup = CUser::GetUserGroupList($arResult["User"]["ID"]);
		while ($arGroup = $rsGroup->Fetch())
		{
			if (strlen($arGroup["DATE_ACTIVE_FROM"]) <= 0 && strlen($arGroup["DATE_ACTIVE_TO"]) <= 0)
				$arResult["User"]["GROUP_ID"][] = $arGroup["GROUP_ID"];
		}

		$arResult["User"]["GROUP_ID"] = array_intersect($arResult["User"]["GROUP_ID"], $arGroupsCanEditID);
	}

	$arResult["arSex"] = array(
		"M" => GetMessage("SONET_P_USER_SEX_M"),
		"F" => GetMessage("SONET_P_USER_SEX_F"),
	);

	if($bVarsFromForm)
	{
		foreach($_POST as $k => $v)
		{
			if(is_array($v))
			{
				foreach($v as $k1 => $v1)
				{
					$arResult["User"][$k][$k1] = htmlspecialcharsbx($v1);
					$arResult["User"]['~'.$k][$k1] = $v1;
				}
			}
			else
			{
				$arResult["User"][$k] = htmlspecialcharsbx($v);
				$arResult["User"]['~'.$k] = $v;
			}
		}
	}

	if ($arParams["SET_TITLE"] == "Y" || $arParams["SET_NAV_CHAIN"] != "N")
	{
		if (strlen($arParams["NAME_TEMPLATE"]) <= 0)
			$arParams["NAME_TEMPLATE"] = CSite::GetNameFormat();

		$arParams["TITLE_NAME_TEMPLATE"] = str_replace(
			array("#NOBR#", "#/NOBR#"),
			array("", ""),
			$arParams["NAME_TEMPLATE"]
		);
		$bUseLogin = $arParams['SHOW_LOGIN'] != "N" ? true : false;

		$arTmpUser = array(
			'NAME' => $arResult["User"]["~NAME"],
			'LAST_NAME' => $arResult["User"]["~LAST_NAME"],
			'SECOND_NAME' => $arResult["User"]["~SECOND_NAME"],
			'LOGIN' => $arResult["User"]["~LOGIN"],
		);

		$userName = CUser::FormatName($arParams['TITLE_NAME_TEMPLATE'], $arTmpUser, $bUseLogin);
	}

	if($arParams["SET_TITLE"]=="Y")
		$APPLICATION->SetTitle(GetMessage("SONET_P_USER_TITLE")." \"".trim($userName, " ")."\"");

	if ($arParams["SET_NAV_CHAIN"] != "N")
	{
		$APPLICATION->AddChainItem($userName, CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $arResult["User"]["ID"])));
		$APPLICATION->AddChainItem(GetMessage("SONET_P_USER_TITLE_VIEW"));
	}

	if(strlen($arResult["User"]["PERSONAL_WWW"])>0)
		$arResult["User"]["PERSONAL_WWW"] = ((strpos($arResult["User"]["PERSONAL_WWW"], "http") === false)? "http://" : "").$arResult["User"]["PERSONAL_WWW"];

	$arResult["User"]["PERSONAL_PHOTO_FILE"] = CFile::GetFileArray($arResult["User"]["PERSONAL_PHOTO"]);
	if ($arResult["User"]["PERSONAL_PHOTO_FILE"] !== false)
		$arResult["User"]["PERSONAL_PHOTO_IMG"] = CFile::ShowImage($arResult["User"]["PERSONAL_PHOTO_FILE"]["ID"], 150, 150, "border=0", "", true);

	$arResult["User"]["WORK_LOGO_FILE"] = CFile::GetFileArray($arResult["User"]["WORK_LOGO"]);
	if ($arResult["User"]["WORK_LOGO_FILE"] !== false)
		$arResult["User"]["WORK_LOGO_IMG"] = CFile::ShowImage($arResult["User"]["WORK_LOGO_FILE"]["ID"], 150, 150, "border=0", "", true);

	if ($arParams['IS_FORUM'] == 'Y')
	{
		$arResult["User"]["FORUM_AVATAR_FILE"] = CFile::GetFileArray($arResult["User"]["FORUM_AVATAR"]);
		if ($arResult["User"]["FORUM_AVATAR_FILE"] !== false)
			$arResult["User"]["FORUM_AVATAR_IMG"] = CFile::ShowImage($arResult["User"]["FORUM_AVATAR_FILE"]["ID"], 150, 150, "border=0", "", true);
	}

	if ($arParams['IS_BLOG'] == 'Y')
	{
		$arResult["User"]["BLOG_AVATAR_FILE"] = CFile::GetFileArray($arResult["User"]["BLOG_AVATAR"]);
		if ($arResult["User"]["BLOG_AVATAR_FILE"] !== false)
			$arResult["User"]["BLOG_AVATAR_IMG"] = CFile::ShowImage($arResult["User"]["BLOG_AVATAR_FILE"]["ID"], 150, 150, "border=0", "", true);
	}


	// ********************* User properties ***************************************************
	/*$arResult["USER_PROPERTIES"] = array("SHOW" => "N");
	if (!empty($arParams["USER_PROPERTY"]))
	{
		$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER", $arParams["ID"], LANGUAGE_ID);
		if (count($arParams["USER_PROPERTY"]) > 0)
		{
			foreach ($arUserFields as $FIELD_NAME => $arUserField)
			{
				if (!in_array($FIELD_NAME, $arParams["USER_PROPERTY"]))
					continue;
				$arUserField["EDIT_FORM_LABEL"] = strLen($arUserField["EDIT_FORM_LABEL"]) > 0 ? $arUserField["EDIT_FORM_LABEL"] : $arUserField["FIELD_NAME"];
				$arUserField["EDIT_FORM_LABEL"] = htmlspecialcharsEx($arUserField["EDIT_FORM_LABEL"]);
				$arUserField["~EDIT_FORM_LABEL"] = $arUserField["EDIT_FORM_LABEL"];
				$arResult["USER_PROPERTIES"]["DATA"][$FIELD_NAME] = $arUserField;
			}
		}
		if (!empty($arResult["USER_PROPERTIES"]["DATA"]))
			$arResult["USER_PROPERTIES"]["SHOW"] = "Y";
		$arResult["bVarsFromForm"] = strLen($strErrorMessage) > 0 ? true : false;
	}*/
	// ******************** /User properties ***************************************************
	$arPolicy = $GLOBALS["USER"]->GetGroupPolicy($arResult["User"]["ID"]);
	$arResult["PASSWORD_MIN_LENGTH"] = intval($arPolicy["PASSWORD_LENGTH"]);
	if($arResult["PASSWORD_MIN_LENGTH"] <= 0)
		$arResult["PASSWORD_MIN_LENGTH"] = 6;
}

//time zones
$arResult["TIME_ZONE_ENABLED"] = CTimeZone::Enabled();
if($arResult["TIME_ZONE_ENABLED"])
	$arResult["TIME_ZONE_LIST"] = CTimeZone::GetZones();

$this->IncludeComponentTemplate();
?>