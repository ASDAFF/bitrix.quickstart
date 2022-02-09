<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule("forum")):
	return false;
elseif (!($_REQUEST["save_product_review"] == "Y" || in_array($_REQUEST['REVIEW_ACTION'], array('DEL', 'HIDE', 'SHOW')))):
	return false;
elseif (is_set($_REQUEST["ELEMENT_ID"]) && $arParams["ELEMENT_ID"] != $_REQUEST["ELEMENT_ID"]):
	return false;
endif;
$this->IncludeComponentLang("action.php");

// 1.1. Check gross errors message data
if (!check_bitrix_sessid())
{
	$arError[] = array(
		"code" => "session time is up",
		"title" => GetMessage("F_ERR_SESSION_TIME_IS_UP"));
}
// 1.3 Check Permission
elseif (ForumCurrUserPermissions($arParams["FORUM_ID"]) <= "E")
{
	$arError[] = array(
		"code" => "access denied",
		"title" => GetMessage("F_ERR_NOT_RIGHT_FOR_ADD"));
}
elseif ((empty($_REQUEST["preview_comment"]) || $_REQUEST["preview_comment"] == "N") && ($_REQUEST["save_product_review"] == "Y"))
{
	$FORUM_TOPIC_ID = 0;
	$arProperties = array();
	$needProperty = array();
	$strErrorMessage = "";
		
	// 1.2 Check Post Text
	if (strLen($_REQUEST["REVIEW_TEXT"]) < 3)
	{
		$arError[] = array(
			"code" => "post is empty",
			"title" => GetMessage("F_ERR_NO_REVIEW_TEXT"));
	}
	// 1.4 Check Captcha
	elseif (!$GLOBALS["USER"]->IsAuthorized() && ($arParams["USE_CAPTCHA"]=="Y" || $arResult["FORUM"]["USE_CAPTCHA"] == "Y"))
	{
		include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
		$captchaPass = COption::GetOptionString("main", "captcha_password", "");
		if ($arResult["FORUM"]["USE_CAPTCHA"] == "Y"):
			if (!class_exists("CForumTmpCaptcha")):
				class CForumTmpCaptcha extends CCaptcha
				{
					function CheckCaptchaCode($userCode, $sid, $bUpperCode = true)
					{
						global $DB;
						if (strlen($userCode)<=0 || strlen($sid)<=0)
							return false;
						if ($bUpperCode)
							$userCode = strtoupper($userCode);
						$res = $DB->Query("SELECT CODE FROM b_captcha WHERE ID = '".$DB->ForSQL($sid,32)."' ");
						if (!$ar = $res->Fetch())
							return false;
						if ($ar["CODE"] != $userCode)
							return false;
//						CCaptcha::Delete($sid);
						return true;
					}
					
					function CheckCode($userCode, $sid, $bUpperCode = True)
					{
						if (!defined("CAPTCHA_COMPATIBILITY"))
							return CForumTmpCaptcha::CheckCaptchaCode($userCode, $sid, $bUpperCode);
						if (!is_array($_SESSION["CAPTCHA_CODE"]) || count($_SESSION["CAPTCHA_CODE"]) <= 0)
							return False;
						if (!array_key_exists($sid, $_SESSION["CAPTCHA_CODE"]))
							return False;
						if ($bUpperCode)
							$userCode = strtoupper($userCode);
						if ($_SESSION["CAPTCHA_CODE"][$sid] != $userCode)
							return False;
//						unset($_SESSION["CAPTCHA_CODE"][$sid]);
						return True;
					}
					
					function CheckCodeCrypt($userCode, $codeCrypt, $password = "", $bUpperCode = True)
					{
						if (!defined("CAPTCHA_COMPATIBILITY"))
							return CForumTmpCaptcha::CheckCaptchaCode($userCode, $codeCrypt, $bUpperCode);
			
						if (strlen($codeCrypt) <= 0)
							return False;
			
						if (!array_key_exists("CAPTCHA_PASSWORD", $_SESSION) || strlen($_SESSION["CAPTCHA_PASSWORD"]) <= 0)
							return False;
			
						if ($bUpperCode)
							$userCode = strtoupper($userCode);
			
						$code = $this->CryptData($codeCrypt, "D", $_SESSION["CAPTCHA_PASSWORD"]);
			
						if ($code != $userCode)
							return False;
			
						return True;
					}
				}
			endif;
			$cpt = new CForumTmpCaptcha();
		else:
			$cpt = new CCaptcha();
		endif;
		if (strlen($_REQUEST["captcha_code"]) <= 0):
			if (!$cpt->CheckCode($_POST["captcha_word"], 0)):
				$arError[] = array(
					"code" => "captcha is empty",
					"title" => GetMessage("POSTM_CAPTCHA"));
			endif;
		elseif (!$cpt->CheckCodeCrypt($_POST["captcha_word"], $_POST["captcha_code"], $captchaPass)):
			$arError[] = array(
				"code" => "bad captcha",
				"title" => GetMessage("POSTM_CAPTCHA"));
		endif;
	}
	// First exit point
	if (!empty($arError)):
		return false;
	endif;
	
	// 1.5 Create Property
	$needProperty = array();
	$PRODUCT_IBLOCK_ID = intVal($arResult["ELEMENT"]["IBLOCK_ID"]);
	$PRODUCT_NAME = Trim($arResult["ELEMENT"]["~NAME"]);
	$FORUM_TOPIC_ID = intVal($arResult["ELEMENT"]["PROPERTY_FORUM_TOPIC_ID_VALUE"]);
	$FORUM_MESSAGE_CNT = intVal($arResult["ELEMENT"]["PROPERTY_FORUM_MESSAGE_CNT_VALUE"]);
	
	if ($FORUM_TOPIC_ID <= 0):
		$db_res = CIBlockElement::GetProperty($arResult["ELEMENT"]["IBLOCK_ID"], $arResult["ELEMENT"]["ID"], false, false, array("CODE" => "FORUM_TOPIC_ID"));
		if (!($db_res && $res = $db_res->Fetch()))
			$needProperty[] = "FORUM_TOPIC_ID";	
	endif;
	if ($FORUM_MESSAGE_CNT <= 0):
		$db_res = CIBlockElement::GetProperty($arResult["ELEMENT"]["IBLOCK_ID"], $arResult["ELEMENT"]["ID"], false, false, array("CODE" => "FORUM_MESSAGE_CNT"));
		if (!($db_res && $res = $db_res->Fetch()))
			$needProperty[] = "FORUM_MESSAGE_CNT";	
	endif;
	if (!empty($needProperty)):
		$obProperty = new CIBlockProperty;
		$res = true;
		foreach ($needProperty as $nameProperty)
		{
			$sName = trim($nameProperty == "FORUM_TOPIC_ID" ? GetMessage("F_FORUM_TOPIC_ID") : GetMessage("F_FORUM_MESSAGE_CNT"));
			$sName = (empty($sName) ? $nameProperty : $sName);
			$res = $obProperty->Add(array(
				"IBLOCK_ID" => $PRODUCT_IBLOCK_ID,
				"ACTIVE" => "Y",
				"PROPERTY_TYPE" => "N",
				"MULTIPLE" => "N",
				"NAME" => $sName,
				"CODE" => $nameProperty));
			if($res)
				${strToUpper($nameProperty)} = 0;
		}
	endif;
	
	// 1.5 Set NULL for topic_id if it was deleted
	if ($FORUM_TOPIC_ID > 0):
		$arTopic = CForumTopic::GetByID($FORUM_TOPIC_ID);
		if (!$arTopic || !is_array($arTopic) || count($arTopic) <= 0 || $arTopic["FORUM_ID"] != $arParams["FORUM_ID"]):
			CIBlockElement::SetPropertyValues($arParams["ELEMENT_ID"], $PRODUCT_IBLOCK_ID, 0, "FORUM_TOPIC_ID");
			$FORUM_TOPIC_ID = 0;
		endif;
	endif;
	
	// 1.6 Create New topic and add messages
	$MID = 0; $TID = 0;
	if ($FORUM_TOPIC_ID <= 0 && $arParams["POST_FIRST_MESSAGE"] == "Y")
	{
	// 1.6.a Create New topic
	// 1.6.a.1 Get author info
		$arUserStart = array(
			"ID" => intVal($arResult["ELEMENT"]["~CREATED_BY"]),
			"NAME" => $GLOBALS["FORUM_STATUS_NAME"]["guest"]);
		if ($arUserStart["ID"] > 0)
		{
			$res = array();
			$db_res = CForumUser::GetListEx(array(), array("USER_ID" => $arResult["ELEMENT"]["~CREATED_BY"]));
			if ($db_res && $res = $db_res->Fetch()):
				$res["FORUM_USER_ID"] = intVal($res["ID"]);
				$res["ID"] = $res["USER_ID"];
			else:
				$db_res = CUser::GetByID($arResult["ELEMENT"]["~CREATED_BY"]);
				if ($db_res && $res = $db_res->Fetch()):
					$res["SHOW_NAME"] = COption::GetOptionString("forum", "USER_SHOW_NAME", "Y"); 
					$res["USER_PROFILE"] = "N"; 
				endif;
			endif;
			if (!empty($res)):
				$arUserStart = $res;
				$sName = ($res["SHOW_NAME"] == "Y" ? trim(CUser::FormatName($arParams["NAME_TEMPLATE"], $res)) : "");
				$arUserStart["NAME"] = (empty($sName) ? trim($res["LOGIN"]) : $sName);
			endif;
		}
		$arUserStart["NAME"] = (empty($arUserStart["NAME"]) ? $GLOBALS["FORUM_STATUS_NAME"]["guest"] : $arUserStart["NAME"]);
	// 1.6.a.1 Add Topic
		$DB->StartTransaction();
		$arFields = Array(
			"TITLE"			=> $arResult["ELEMENT"]["~NAME"],
			"TAGS"			=> $arResult["ELEMENT"]["~TAGS"],
			"FORUM_ID"		=> $arParams["FORUM_ID"],
			"USER_START_ID"	=> $arUserStart["ID"],
			"USER_START_NAME" => $arUserStart["NAME"],
			"LAST_POSTER_NAME" => $arUserStart["NAME"],
			"APPROVED" => "Y");
		$TID = CForumTopic::Add($arFields);
		if (intVal($TID) <= 0)
		{
			$arError[] = array(
				"code" => "topic is not created",
				"title" => GetMessage("F_ERR_ADD_TOPIC"));
		}
		else 
		{
	// 1.6.b Add post as new message 
			$sImage = ""; $arSection = array();
			$url = (empty($arParams["URL_TEMPLATES_DETAIL"]) ? $arResult["ELEMENT"]["DETAIL_PAGE_URL"] : $arParams["URL_TEMPLATES_DETAIL"]);
			if (strpos($arParams["URL_TEMPLATES_DETAIL"], "#SECTION_CODE#") !== false && intVal($arResult["ELEMENT"]["IBLOCK_SECTION_ID"]) > 0):
				$db_res = CIBlockSection::GetList(array(), array("ID" => $arResult["ELEMENT"]["IBLOCK_SECTION_ID"]), false, array("ID", "NAME", "CODE"));
				if ($db_res && $res = $db_res->Fetch()):
					$arSection = $res;
				endif;
			endif;
			$url = str_replace(
				array("#ELEMENT_ID#", "#ID#", "#ELEMENT_CODE#", "#SECTION_ID#", "#SECTION_CODE#"), 
				array($arResult["ELEMENT"]["ID"], $arResult["ELEMENT"]["ID"], $arResult["ELEMENT"]["CODE"], 
							$arResult["ELEMENT"]["IBLOCK_SECTION_ID"], $arSection["CODE"]), $url);
			if (intVal($arResult["ELEMENT"]["PREVIEW_PICTURE"]) > 0):
				$arImage = CFile::GetFileArray($arResult["ELEMENT"]["PREVIEW_PICTURE"]);
				if (!empty($arImage)):
					$sImage = ($arResult["FORUM"]["ALLOW_IMG"] == "Y" ? "[IMG]".$arImage["SRC"]."[/IMG]" : '');
				endif;
			endif;
			$sElementPreview = $arResult["ELEMENT"]["~PREVIEW_TEXT"];
			if ($arAllow["HTML"] != "Y")
				$sElementPreview = strip_tags($sElementPreview);
			$arFields = Array(
				"POST_MESSAGE" => str_replace(array("#IMAGE#", "#TITLE#", "#BODY#", "#LINK#"),
					array($sImage, $arResult["ELEMENT"]["~NAME"], $sElementPreview, $url), 
					$arParams["POST_FIRST_MESSAGE_TEMPLATE"]),
				"AUTHOR_ID" => $arUserStart["ID"],
				"AUTHOR_NAME" => $arUserStart["NAME"],
				"FORUM_ID" => $arParams["FORUM_ID"],
				"TOPIC_ID" => $TID,
				"APPROVED" => "Y",
				"NEW_TOPIC" => "Y",
				"PARAM1" => "IB", 
				"PARAM2" => intVal($arParams["ELEMENT_ID"]));
			$MID = CForumMessage::Add($arFields, false, array("SKIP_INDEXING" => "Y", "SKIP_STATISTIC" => "N"));
			
			if (intVal($MID) <= 0)
			{
				$arError[] = array(
					"code" => "message is not added 1",
					"title" => GetMessage("F_ERR_ADD_MESSAGE"));
				CForumTopic::Delete($TID);
				$TID = 0;
			}
			elseif ($arParams["SUBSCRIBE_AUTHOR_ELEMENT"] == "Y" && intVal($arResult["ELEMENT"]["~CREATED_BY"]) > 0)
			{
				if ($arUserStart["USER_PROFILE"] == "N"):
					$arUserStart["FORUM_USER_ID"] = CForumUser::Add(array("USER_ID" => $arResult["ELEMENT"]["~CREATED_BY"]));
				endif;
				if (intVal($arUserStart["FORUM_USER_ID"]) > 0):
					CForumSubscribe::Add(array(
						"USER_ID" => $arResult["ELEMENT"]["~CREATED_BY"],
						"FORUM_ID" => $arParams["FORUM_ID"],
						"SITE_ID" => SITE_ID,
						"TOPIC_ID" => $TID, 
						"NEW_TOPIC_ONLY" => "N"));
					BXClearCache(true, "/bitrix/forum/user/".$arResult["ELEMENT"]["~CREATED_BY"]."/subscribe/"); // Sorry, Max.
				endif;
			}
		}
	// Second exit point
		if (!empty($arError)):
			$DB->Rollback();
			return false;
		else:
			$DB->Commit();
		endif;
	}
		// 1.6.1 Add post comment
	$arFieldsG = array(
		"POST_MESSAGE" => $_POST["REVIEW_TEXT"],
		"AUTHOR_NAME" => trim($_POST["REVIEW_AUTHOR"]),
		"AUTHOR_EMAIL" => $_POST["REVIEW_EMAIL"],
		"USE_SMILES" => $_POST["REVIEW_USE_SMILES"],
		"PARAM2" => intVal($arParams["ELEMENT_ID"]), 
		"TITLE" => $PRODUCT_NAME);
		
	if (!empty($_FILES["REVIEW_ATTACH_IMG"]))
	{
		$arFieldsG["ATTACH_IMG"] = $_FILES["REVIEW_ATTACH_IMG"]; 
	}
	else
	{
		$arFiles = array();
		if (!empty($_REQUEST["FILES"])):
			foreach ($_REQUEST["FILES"] as $key):
				$arFiles[$key] = array("FILE_ID" => $key);
				if (!in_array($key, $_REQUEST["FILES_TO_UPLOAD"]))
					$arFiles[$key]["del"] = "Y";
			endforeach;
		endif;
		if (!empty($_FILES)):
			$res = array();
			foreach ($_FILES as $key => $val):
				if (substr($key, 0, strLen("FILE_NEW")) == "FILE_NEW" && !empty($val["name"])):
					$arFiles[] = $_FILES[$key];
				endif;
			endforeach;
		endif;
		if (!empty($arFiles))
			$arFieldsG["FILES"] = $arFiles; 
	}
	$TOPIC_ID = ($FORUM_TOPIC_ID > 0 ? $FORUM_TOPIC_ID : $TID);
	$MID = ForumAddMessage(($TOPIC_ID > 0 ? "REPLY" : "NEW"), $arParams["FORUM_ID"], $TOPIC_ID, 0, $arFieldsG, $strErrorMessage, $strOKMessage, false, 
		$_POST["captcha_word"], 0, $_POST["captcha_code"], $arParams["NAME_TEMPLATE"]);

	if ($MID <= 0 || !empty($strErrorMessage)):
		$arError[] = array(
			"code" => "message is not added 2",
			"title" => (empty($strErrorMessage) ? GetMessage("F_ERR_ADD_MESSAGE") : $strErrorMessage));
		$arResult['RESULT'] = false;
		$arResult["OK_MESSAGE"] = '';
	else:
		if ($TOPIC_ID <= 0):
			$res = CForumMessage::GetByID($MID);
			$FORUM_TOPIC_ID = $TID = intVal($res["TOPIC_ID"]);
		endif;
		if ($arParams["AUTOSAVE"])
			$arParams["AUTOSAVE"]->Reset();
	// 1.7 Update Iblock Property
		if ($TID > 0)
		{
			CIBlockElement::SetPropertyValues($arParams["ELEMENT_ID"], $PRODUCT_IBLOCK_ID, intVal($TID), "FORUM_TOPIC_ID");
		}
		else
		{
			if ($TOPIC_ID > 0)
				$TID = $TOPIC_ID;
			if ($FORUM_TOPIC_ID > 0)
				$TID = $FORUM_TOPIC_ID;
		}

		$FORUM_MESSAGE_CNT = CForumMessage::GetList(array(), array("TOPIC_ID" => $TID, "APPROVED" => "Y", "!PARAM1" => "IB"), true);
		CIBlockElement::SetPropertyValues($arParams["ELEMENT_ID"], $PRODUCT_IBLOCK_ID, intVal($FORUM_MESSAGE_CNT), "FORUM_MESSAGE_CNT");

		$strOKMessage = GetMessage("COMM_COMMENT_OK");
		$arResult["FORUM_TOPIC_ID"] = intVal($FORUM_TOPIC_ID);

		ForumClearComponentCache($componentName);

		// SUBSCRIBE
		if ($_REQUEST["TOPIC_SUBSCRIBE"] == "Y"):
			ForumSubscribeNewMessagesEx($arParams["FORUM_ID"], $FORUM_TOPIC_ID, "N", $strErrorMessage, $strOKMessage);
			BXClearCache(true, "/bitrix/forum/user/".$GLOBALS["USER"]->GetID()."/subscribe/");
		endif;

		$strURL = (!empty($_REQUEST["back_page"]) ? $_REQUEST["back_page"] : $APPLICATION->GetCurPageParam("", 
			array("MID", "SEF_APPLICATION_CUR_PAGE_URL", BX_AJAX_PARAM_ID, "result")));
		$bNotModerated =  ($arResult["FORUM"]["MODERATION"] != "Y" || CForumNew::CanUserModerateForum($arParams["FORUM_ID"], $USER->GetUserGroupArray()));
		$strURL = ForumAddPageParams($strURL, array("MID" => $MID, "result" => ($bNotModerated ? "reply" : "not_approved")));
		$strURL .= ($bNotModerated ? "#message".$MID : "#reviewnote");

		if ($arParams["NO_REDIRECT_AFTER_SUBMIT"] != "Y")
			LocalRedirect($strURL);
		else
		{
			$arResult['RESULT'] = $MID;
			$strOKMessage = ($bNotModerated ? GetMessage("COMM_COMMENT_OK") : GetMessage("COMM_COMMENT_OK_AND_NOT_APPROVED"));
		}
	endif;
}
elseif ($_REQUEST["save_product_review"] == "Y") // preview
{
	$arParams['SHOW_MINIMIZED'] = 'N';
	$arAllow["SMILES"] = ($_POST["REVIEW_USE_SMILES"] !="Y" ? "N" : $arResult["FORUM"]["ALLOW_SMILES"]);
	$arResult["MESSAGE_VIEW"] = array(
		"POST_MESSAGE_TEXT" => $_POST["REVIEW_TEXT"],
		"AUTHOR_NAME" => htmlspecialcharsEx($arResult["USER"]["SHOWED_NAME"]), 
		"AUTHOR_ID" => intVal($USER->GetID()),
		"AUTHOR_URL" => CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PROFILE_VIEW"], array("UID" => $USER->GetID())), 
		"POST_DATE" => CForumFormat::DateFormat($arParams["DATE_TIME_FORMAT"], time()+CTimeZone::GetOffset()), 
		"FILES" => array());

	$arFields = array(
			"FORUM_ID" => intVal($arParams["FORUM_ID"]), 
			"TOPIC_ID" => 0, 
			"MESSAGE_ID" => 0, 
			"USER_ID" => intVal($GLOBALS["USER"]->GetID()));
	$arFiles = array();
	$arFilesExists = array();
	$res = array();
	
	foreach ($_FILES as $key => $val):
		if ((substr($key, 0, strLen("FILE_NEW")) == "FILE_NEW") && !empty($val["name"])):
			$arFiles[] = $_FILES[$key];
		endif;
	endforeach;
	foreach ($_REQUEST["FILES"] as $key => $val):
		if (!in_array($val, $_REQUEST["FILES_TO_UPLOAD"]))
		{
			$arFiles[$val] = array("FILE_ID" => $val, "del" => "Y");
			unset($_REQUEST["FILES"][$key]);
			unset($_REQUEST["FILES_TO_UPLOAD"][$key]);
		}
		else 
		{
			$arFilesExists[$val] = array("FILE_ID" => $val);
		}
	endforeach;
	
	if (!empty($arFiles)):
		$res = CForumFiles::Save($arFiles, $arFields);
		$res1 = $GLOBALS['APPLICATION']->GetException();
		if ($res1):
			$arError[] = array(
				"code" => "file upload error",
				"title" => $res1->GetString());
		endif;
	endif;

	$res = is_array($res) ? $res : array();
	foreach ($res as $key => $val):
		$arFilesExists[$key] = $val;
	endforeach;
	$arFilesExists = array_keys($arFilesExists);
	sort($arFilesExists);
	$arResult["MESSAGE_VIEW"]["FILES"] = $_REQUEST["FILES"] = $arFilesExists;
	$arResult["MESSAGE_VIEW"]["POST_MESSAGE_TEXT"] = $parser->convert($_POST["REVIEW_TEXT"], $arAllow, "html", $arFilesExists);

}
if (isset($_REQUEST['REVIEW_ACTION'])) {
	$arFields = array();
	if (empty($arError))
	{
		if (isset($_REQUEST['MID']) && intval($_REQUEST['MID']) > 0)
			$arFields = array("MID" => intval($_REQUEST['MID']));
		$result = ForumActions($_REQUEST['REVIEW_ACTION'], $arFields, $strErrorMessage, $strOKMessage);
		if ($result)
		{
			ForumClearComponentCache($componentName);

			if (isset($arFields['MID']))
			{
				$res = CForumMessage::GetByID($arFields['MID']);
				$TID = intVal($res["TOPIC_ID"]);
				$FORUM_MESSAGE_CNT = CForumMessage::GetList(array(), array("TOPIC_ID" => $TID, "APPROVED" => "Y", "!PARAM1" => "IB"), true);

				$PRODUCT_IBLOCK_ID = intVal($arResult["ELEMENT"]["IBLOCK_ID"]);
				CIBlockElement::SetPropertyValues($arParams["ELEMENT_ID"], $PRODUCT_IBLOCK_ID, intVal($FORUM_MESSAGE_CNT), "FORUM_MESSAGE_CNT");
			}
		}
	}
	if (isset($_REQUEST['AJAX_CALL']))
	{
		$GLOBALS['APPLICATION']->RestartBuffer();
		if (empty($arError))
		{
			$arRes = array('status' => $result, 'message' => ($result ? $strOKMessage : $strErrorMessage));
		} else {
			$arRes = array('status' => false, 'message' => $arError[0]['title']);
		}
		echo CUtil::PhpToJSObject($arRes);
		die();
	} else {
		LocalRedirect($APPLICATION->GetCurPageParam("", array("REVIEW_ACTION", "sessid", "MID")));
	}
}
?>
