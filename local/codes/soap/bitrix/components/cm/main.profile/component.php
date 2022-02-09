<?
/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CUserTypeManager $USER_FIELD_MANAGER
 * @param array $arParams
 * @param CBitrixComponent $this
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $USER_FIELD_MANAGER;

$arResult["ID"] = intval($USER->GetID());
$arResult["GROUP_POLICY"] = CUser::GetGroupPolicy($arResult["ID"]);

$arParams['SEND_INFO'] = $arParams['SEND_INFO'] == 'Y' ? 'Y' : 'N';
$arParams['CHECK_RIGHTS'] = $arParams['CHECK_RIGHTS'] == 'Y' ? 'Y' : 'N';

if(!($arParams['CHECK_RIGHTS'] == 'N' || $USER->CanDoOperation('edit_own_profile')) || $arResult["ID"]<=0)
{
	$APPLICATION->ShowAuthForm("");
	return;
}

$strError = '';

if($_SERVER["REQUEST_METHOD"]=="POST" && ($_REQUEST["save"] <> '' || $_REQUEST["apply"] <> '') && check_bitrix_sessid())
{
	if(COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
	{
		//possible encrypted user password
		$sec = new CRsaSecurity();
		if(($arKeys = $sec->LoadKeys()))
		{
			$sec->SetKeys($arKeys);
			$errno = $sec->AcceptFromForm(array('NEW_PASSWORD', 'NEW_PASSWORD_CONFIRM'));
			if($errno == CRsaSecurity::ERROR_SESS_CHECK)
				$strError .= GetMessage("main_profile_sess_expired").'<br />';
			elseif($errno < 0)
				$strError .= GetMessage("main_profile_decode_err", array("#ERRCODE#"=>$errno)).'<br />';
		}
	}

	if($strError == '')
	{
		$bOk = false;
		$obUser = new CUser;
	
		$arPERSONAL_PHOTO = $_FILES["PERSONAL_PHOTO"];
		$arWORK_LOGO = $_FILES["WORK_LOGO"];
	
		$rsUser = CUser::GetByID($arResult["ID"]);
		$arUser = $rsUser->Fetch();
		if($arUser)
		{
			$arPERSONAL_PHOTO["old_file"] = $arUser["PERSONAL_PHOTO"];
			$arPERSONAL_PHOTO["del"] = $_REQUEST["PERSONAL_PHOTO_del"];
	
			$arWORK_LOGO["old_file"] = $arUser["WORK_LOGO"];
			$arWORK_LOGO["del"] = $_REQUEST["WORK_LOGO_del"];
		}
	
		$arFields = array(
			"NAME" => $_REQUEST["NAME"],
			"LAST_NAME" => $_REQUEST["LAST_NAME"],
			"SECOND_NAME" => $_REQUEST["SECOND_NAME"],
			"EMAIL" => $_REQUEST["EMAIL"],
			"LOGIN" => $_REQUEST["LOGIN"],
			"PERSONAL_PROFESSION" => $_REQUEST["PERSONAL_PROFESSION"],
			"PERSONAL_WWW" => $_REQUEST["PERSONAL_WWW"],
			"PERSONAL_ICQ" => $_REQUEST["PERSONAL_ICQ"],
			"PERSONAL_GENDER" => $_REQUEST["PERSONAL_GENDER"],
			"PERSONAL_BIRTHDAY" => $_REQUEST["PERSONAL_BIRTHDAY"],
			"PERSONAL_PHOTO" => $arPERSONAL_PHOTO,
			"PERSONAL_PHONE" => $_REQUEST["PERSONAL_PHONE"],
			"PERSONAL_FAX" => $_REQUEST["PERSONAL_FAX"],
			"PERSONAL_MOBILE" => $_REQUEST["PERSONAL_MOBILE"],
			"PERSONAL_PAGER" => $_REQUEST["PERSONAL_PAGER"],
			"PERSONAL_STREET" => $_REQUEST["PERSONAL_STREET"],
			"PERSONAL_MAILBOX" => $_REQUEST["PERSONAL_MAILBOX"],
			"PERSONAL_CITY" => $_REQUEST["PERSONAL_CITY"],
			"PERSONAL_STATE" => $_REQUEST["PERSONAL_STATE"],
			"PERSONAL_ZIP" => $_REQUEST["PERSONAL_ZIP"],
			"PERSONAL_COUNTRY" => $_REQUEST["PERSONAL_COUNTRY"],
			"PERSONAL_NOTES" => $_REQUEST["PERSONAL_NOTES"],
			"WORK_COMPANY" => $_REQUEST["WORK_COMPANY"],
			"WORK_DEPARTMENT" => $_REQUEST["WORK_DEPARTMENT"],
			"WORK_POSITION" => $_REQUEST["WORK_POSITION"],
			"WORK_WWW" => $_REQUEST["WORK_WWW"],
			"WORK_PHONE" => $_REQUEST["WORK_PHONE"],
			"WORK_FAX" => $_REQUEST["WORK_FAX"],
			"WORK_PAGER" => $_REQUEST["WORK_PAGER"],
			"WORK_STREET" => $_REQUEST["WORK_STREET"],
			"WORK_MAILBOX" => $_REQUEST["WORK_MAILBOX"],
			"WORK_CITY" => $_REQUEST["WORK_CITY"],
			"WORK_STATE" => $_REQUEST["WORK_STATE"],
			"WORK_ZIP" => $_REQUEST["WORK_ZIP"],
			"WORK_COUNTRY" => $_REQUEST["WORK_COUNTRY"],
			"WORK_PROFILE" => $_REQUEST["WORK_PROFILE"],
			"WORK_LOGO" => $arWORK_LOGO,
			"WORK_NOTES" => $_REQUEST["WORK_NOTES"],
			"AUTO_TIME_ZONE" => ($_REQUEST["AUTO_TIME_ZONE"] == "Y" || $_REQUEST["AUTO_TIME_ZONE"] == "N"? $_REQUEST["AUTO_TIME_ZONE"] : ""),
		);
	
		if(isset($_REQUEST["TIME_ZONE"]))
			$arFields["TIME_ZONE"] = $_REQUEST["TIME_ZONE"];
	
		if($arUser)
		{
			if($arUser['EXTERNAL_AUTH_ID'] <> '')
			{
				$arFields['EXTERNAL_AUTH_ID'] = $arUser['EXTERNAL_AUTH_ID'];
			}
		}
	
		if($USER->IsAdmin())
		{
			$arFields["ADMIN_NOTES"] = $_REQUEST["ADMIN_NOTES"];
		}

		if($_REQUEST["NEW_PASSWORD"] <> '' && $arUser['EXTERNAL_AUTH_ID'] == '')
		{
			$arFields["PASSWORD"] = $_REQUEST["NEW_PASSWORD"];
			$arFields["CONFIRM_PASSWORD"] = $_REQUEST["NEW_PASSWORD_CONFIRM"];
		}

		$USER_FIELD_MANAGER->EditFormAddFields("USER", $arFields);
	
		if(!$obUser->Update($arResult["ID"], $arFields, true))
			$strError .= $obUser->LAST_ERROR.'<br />';
	}

	if($strError == '')
	{
		if (CModule::IncludeModule("forum"))
		{
			$APPLICATION->ResetException();
			$arforumFields = array(
				"SHOW_NAME"		=> ($_REQUEST["forum_SHOW_NAME"]=="Y") ? "Y" : "N",
				"DESCRIPTION"	=> $_REQUEST["forum_DESCRIPTION"],
				"INTERESTS"		=> $_REQUEST["forum_INTERESTS"],
				"SIGNATURE"		=> $_REQUEST["forum_SIGNATURE"],
				"AVATAR"		=> $_FILES["forum_AVATAR"]
			);

			$arforumFields["AVATAR"]["del"] = $_REQUEST["forum_AVATAR_del"];

			$ar_res = CForumUser::GetByUSER_ID($arResult["ID"]);
			if ($ar_res)
			{
				$arforumFields["AVATAR"]["old_file"] = $ar_res["AVATAR"];
				$FORUM_USER_ID = IntVal($ar_res["ID"]);
				$FORUM_USER_ID1 = CForumUser::Update($FORUM_USER_ID, $arforumFields);
				$forum_res = (IntVal($FORUM_USER_ID1)>0);
			}
			else
			{
				$arforumFields["USER_ID"] = $arResult["ID"];
				$FORUM_USER_ID = CForumUser::Add($arforumFields);
				$forum_res = (IntVal($FORUM_USER_ID)>0);
			}

			if($ex = $APPLICATION->GetException())
				$strError = $ex->GetString();
		}
	}

	if($strError == '')
	{
		if (CModule::IncludeModule("blog"))
		{
			$APPLICATION->ResetException();
			$arblogFields = array(
				"ALIAS" => $_REQUEST["blog_ALIAS"],
				"DESCRIPTION" => $_REQUEST["blog_DESCRIPTION"],
				"INTERESTS" => $_REQUEST["blog_INTERESTS"],
				"AVATAR" => $_FILES["blog_AVATAR"]
			);
			$arblogFields["AVATAR"]["del"] = $_REQUEST["blog_AVATAR_del"];

			$ar_res = CBlogUser::GetByID($arResult["ID"], BLOG_BY_USER_ID);
			if ($ar_res)
			{
				$arblogFields["AVATAR"]["old_file"] = $ar_res["AVATAR"];
				$BLOG_USER_ID = intval($ar_res["ID"]);

				$BLOG_USER_ID1 = CBlogUser::Update($BLOG_USER_ID, $arblogFields);
				$blog_res = (intval($BLOG_USER_ID1)>0);
			}
			else
			{
				$arblogFields["USER_ID"] = $arResult["ID"];
				$arblogFields["~DATE_REG"] = CDatabase::CurrentTimeFunction();

				$BLOG_USER_ID = CBlogUser::Add($arblogFields);
				$blog_res = (intval($BLOG_USER_ID)>0);
			}

			if($ex = $APPLICATION->GetException())
				$strError = $ex->GetString();
		}
	}

	if(CModule::IncludeModule("learning") && $strError == '')
	{
		$arStudentFields = array(
			"RESUME" => $_REQUEST["student_RESUME"],
			"PUBLIC_PROFILE" => ($_REQUEST["student_PUBLIC_PROFILE"]=="Y" ? "Y" : "N")
		);

		$ar_res = CStudent::GetList(array(), array("USER_ID" => $arResult["ID"]));

		if ($arStudent = $ar_res->Fetch())
		{
			$learning_res = CStudent::Update($arResult["ID"], $arStudentFields);
		}
		else
		{
			$arStudentFields["USER_ID"] = $arResult["ID"];
			$STUDENT_USER_ID = CStudent::Add($arStudentFields);
			$learning_res = (intval($STUDENT_USER_ID)>0);
		}
	}

	if($strError == '')
	{
		if($arParams['SEND_INFO'] == 'Y')
			$obUser->SendUserInfo($arResult["ID"], SITE_ID, GetMessage("ACCOUNT_UPDATE"), true);

		$bOk = true;
	}
}

$rsUser = CUser::GetByID($arResult["ID"]);
if(!$arResult["arUser"] = $rsUser->GetNext(false))
{
	$arResult["ID"] = 0;
}

if (CModule::IncludeModule("blog"))
{
	$arResult["INCLUDE_BLOG"] = "Y";

	$arResult["arBlogUser"] = array();
	$arBlg = CBlogUser::GetByID($arResult["ID"], BLOG_BY_USER_ID);
	if(is_array($arBlg))
	{
		foreach($arBlg as $key => $val)
		{
			$arResult["arBlogUser"]["~".$key] = $val;
			$arResult["arBlogUser"][$key] = htmlspecialcharsbx($val);
		}
	}
	
	if (!isset($arResult["arBlogUser"]["ALLOW_POST"]) || ($arResult["arBlogUser"]["ALLOW_POST"]!="Y" && $arResult["arBlogUser"]["ALLOW_POST"]!="N"))
		$arResult["arBlogUser"]["ALLOW_POST"] = "Y";
}

if (CModule::IncludeModule("forum"))
{
	$arResult["INCLUDE_FORUM"] = "Y";

	$rsForumUser = CForumUser::GetList(array(), array("USER_ID" => $arResult["ID"]));
	$arResult["arForumUser"] = $rsForumUser->GetNext(false);
	if (!isset($arResult["arForumUser"]["ALLOW_POST"]) || ($arResult["arForumUser"]["ALLOW_POST"]!="Y" && $arResult["arForumUser"]["ALLOW_POST"]!="N"))
		$arResult["arForumUser"]["ALLOW_POST"] = "Y";
}

if (CModule::IncludeModule("learning"))
{
	$arResult["INCLUDE_LEARNING"] = "Y";

	$dbStudent = CStudent::GetList(array(), array("USER_ID" => $arResult["ID"]));
	$arResult["arStudent"] = $dbStudent->GetNext();
	if (!isset($arResult["arStudent"]["PUBLIC_PROFILE"]) || ($arResult["arStudent"]["PUBLIC_PROFILE"]!="Y" && $arResult["arStudent"]["PUBLIC_PROFILE"]!="N"))
		$arResult["arStudent"]["PUBLIC_PROFILE"] = "N";
}

if($strError <> '')
{
	foreach($_POST as $k=>$val)
	{
		if(!is_array($val))
		{
			$arResult["arUser"][$k] = htmlspecialcharsex($val);
			$arResult["arForumUser"][$k] = htmlspecialcharsex($val);
		}
		else
		{
			$arResult["arUser"][$k] = $val;
			$arResult["arForumUser"][$k] = $val;
		}
	}
}

$arResult["FORM_TARGET"] = $APPLICATION->GetCurPage();

$arResult["arUser"]["PERSONAL_PHOTO_INPUT"] = CFile::InputFile("PERSONAL_PHOTO", 20, $arResult["arUser"]["PERSONAL_PHOTO"], false, 0, "IMAGE");

if (strlen($arResult["arUser"]["PERSONAL_PHOTO"])>0)
	$arResult["arUser"]["PERSONAL_PHOTO_HTML"] = CFile::ShowImage($arResult["arUser"]["PERSONAL_PHOTO"], 150, 150, "border=0", "", true);

$arResult["arUser"]["WORK_LOGO_INPUT"] = CFile::InputFile("WORK_LOGO", 20, $arResult["arUser"]["WORK_LOGO"], false, 0, "IMAGE");

if (strlen($arResult["arUser"]["WORK_LOGO"])>0)
	$arResult["arUser"]["WORK_LOGO_HTML"] = CFile::ShowImage($arResult["arUser"]["WORK_LOGO"], 150, 150, "border=0", "", true);

$arResult["arUser"]["WORK_LOGO_INPUT"] = CFile::InputFile("WORK_LOGO", 20, $arResult["arUser"]["WORK_LOGO"], false, 0, "IMAGE");

if (strlen($arResult["arUser"]["WORK_LOGO"])>0)
	$arResult["arUser"]["WORK_LOGO_HTML"] = CFile::ShowImage($arResult["arUser"]["WORK_LOGO"], 150, 150, "border=0", "", true);

$arResult["arForumUser"]["AVATAR_INPUT"] = CFile::InputFile("forum_AVATAR", 20, $arResult["arForumUser"]["AVATAR"], false, 0, "IMAGE");

if (strlen($arResult["arForumUser"]["AVATAR"])>0)
	$arResult["arForumUser"]["AVATAR_HTML"] = CFile::ShowImage($arResult["arForumUser"]["AVATAR"], 150, 150, "border=0", "", true);

$arResult["arBlogUser"]["AVATAR_INPUT"] = CFile::InputFile("blog_AVATAR", 20, $arResult["arBlogUser"]["AVATAR"], false, 0, "IMAGE");

if (strlen($arResult["arBlogUser"]["AVATAR"])>0)
	$arResult["arBlogUser"]["AVATAR_HTML"] = CFile::ShowImage($arResult["arBlogUser"]["AVATAR"], 150, 150, "border=0", "", true);

$arResult["IS_ADMIN"] = $USER->IsAdmin();

$arCountries = GetCountryArray();
$arResult["COUNTRY_SELECT"] = SelectBoxFromArray("PERSONAL_COUNTRY", $arCountries, $arResult["arUser"]["PERSONAL_COUNTRY"], GetMessage("USER_DONT_KNOW"));
$arResult["COUNTRY_SELECT_WORK"] = SelectBoxFromArray("WORK_COUNTRY", $arCountries, $arResult["arUser"]["WORK_COUNTRY"], GetMessage("USER_DONT_KNOW"));

$arResult["strProfileError"] = $strError;
$arResult["BX_SESSION_CHECK"] = bitrix_sessid_post();

$arResult["DATE_FORMAT"] = CLang::GetDateFormat("SHORT");

$arResult["COOKIE_PREFIX"] = COption::GetOptionString("main", "cookie_name", "BITRIX_SM");
if (strlen($arResult["COOKIE_PREFIX"]) <= 0) 
	$arResult["COOKIE_PREFIX"] = "BX";

// ********************* User properties ***************************************************
$arResult["USER_PROPERTIES"] = array("SHOW" => "N");
if (!empty($arParams["USER_PROPERTY"]))
{
	$arUserFields = $USER_FIELD_MANAGER->GetUserFields("USER", $arResult["ID"], LANGUAGE_ID);
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
	$arResult["bVarsFromForm"] = ($strError == ''? false : true);
}
// ******************** /User properties ***************************************************

if($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(GetMessage("PROFILE_DEFAULT_TITLE"));

if($bOk) 
	$arResult['DATA_SAVED'] = 'Y';

//time zones
$arResult["TIME_ZONE_ENABLED"] = CTimeZone::Enabled();
if($arResult["TIME_ZONE_ENABLED"])
	$arResult["TIME_ZONE_LIST"] = CTimeZone::GetZones();

//secure authorization
$arResult["SECURE_AUTH"] = false;
if(!CMain::IsHTTPS() && COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
{
	$sec = new CRsaSecurity();
	if(($arKeys = $sec->LoadKeys()))
	{
		$sec->SetKeys($arKeys);
		$sec->AddToForm('form1', array('NEW_PASSWORD', 'NEW_PASSWORD_CONFIRM'));
		$arResult["SECURE_AUTH"] = true;
	}
}

//socialservices
$arResult["SOCSERV_ENABLED"] = IsModuleInstalled("socialservices");

$this->IncludeComponentTemplate();
