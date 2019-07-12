<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

if(!function_exists('redsign_add_recall_type'))
{
	function redsign_add_recall_type()
	{
		global $DB, $DBType, $APPLICATION;
		$return = false;
		$et = new CEventType;
		$EventTypeID = $et->Add(array(
			"LID"           => "ru",
			"EVENT_NAME"    => "REDSIGN_RECALL",
			"NAME"          => GetMessage("INSTALL_EVENT_TYPE_NAME"),
			"DESCRIPTION"   => GetMessage("INSTALL_EVENT_TYPE_DESCRIPTION")
			));
		if($EventTypeID>0)
		{
			COption::SetOptionInt("redsign.opt", "installed_event_type_id", $EventTypeID);
			$arSites = array();
			$rsSites = CSite::GetList($by="sort", $order="desc", array());
			while ($arSite = $rsSites->Fetch())
			{
				$arSites[] = $arSite["LID"];
			}
			$arr["ACTIVE"] = "Y";
			$arr["EVENT_NAME"] = "REDSIGN_RECALL";
			$arr["LID"] = $arSites;
			$arr["EMAIL_FROM"] = "#AUTHOR_EMAIL#";
			$arr["EMAIL_TO"] = "#EMAIL_TO#";
			$arr["BCC"] = "";
			$arr["SUBJECT"] = "#THEME#";
			$arr["BODY_TYPE"] = "text";
			$arr["MESSAGE"] = GetMessage("INSTALL_EVENT_TEMPLATE_BODY");

			$emess = new CEventMessage;
			$EventTemplateID = $emess->Add($arr);
			if($EventTemplateID>0)
			{
				COption::SetOptionInt("redsign.opt", "installed_event_template_id", $EventTemplateID);
				$return = true;
			}
		} else {
			$return = false;
		}
		return $return;
	}
}

if($_REQUEST["redsignRecall"]=="Y" && class_exists(CEventType) && class_exists(CEventMessage))
{
	$arFilter = array("TYPE_ID" => "REDSIGN_RECALL", "LID" => "ru");
	$rsET = CEventType::GetList($arFilter);
	if(!$arET = $rsET->Fetch())
	{
		redsign_add_recall_type();
	}
	$arResult["LAST_ERROR"] = "";
	$arResult["GOOD_SEND"] = "";
	$THEME = GetMessage("ALFA_MSG_THEME");
	$AUTHOR = trim(htmlspecialchars($_REQUEST["REDSIGN_AUTHOR"]));
	$COMPANY_NAME = trim(htmlspecialchars($_REQUEST["REDSIGN_COMPANY_NAME"]));
	$AUTHOR_EMAIL = trim(htmlspecialchars($_REQUEST["REDSIGN_AUTHOR_EMAIL"]));
	$AUTHOR_PHONE = trim(htmlspecialchars($_REQUEST["REDSIGN_AUTHOR_PHONE"]));
	$AUTHOR_COMMENT = trim(htmlspecialchars($_REQUEST["REDSIGN_AUTHOR_COMMENT"]));
	$EMAIL_TO = trim(htmlspecialchars($arParams["ALFA_EMAIL_TO"]));
	
	if(check_bitrix_sessid())
	{
		if($arParams["ALFA_USE_CAPTCHA"] == "Y")
		{
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
			$captcha_code = $_POST["captcha_sid"];
			$captcha_word = $_POST["captcha_word"];
			$cpt = new CCaptcha();
			$captchaPass = COption::GetOptionString("main", "captcha_password", "");
			if (strlen($captcha_word) > 0 && strlen($captcha_code) > 0)
			{
				if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
					$arResult["LAST_ERROR"] = GetMessage("ALFA_MSG_CAPTCHA_WRONG");
			} else {
				$arResult["LAST_ERROR"] = GetMessage("ALFA_MSG_CAPTCHA_EMPRTY");
			}
		}
		
		if($arResult["LAST_ERROR"]=="")
		{
			if($AUTHOR!="" && $AUTHOR_PHONE!="")
			{
				$arEventFields = array(
					"THEME" => $THEME,
					"AUTHOR" => $AUTHOR,
					"COMPANY_NAME" => $COMPANY_NAME,
					"AUTHOR_EMAIL" => $AUTHOR_EMAIL,
					"AUTHOR_PHONE" => $AUTHOR_PHONE,
					"AUTHOR_COMMENT" => $AUTHOR_COMMENT,
					"EMAIL_TO" => $EMAIL_TO,
				);
				CEvent::Send("REDSIGN_RECALL", SITE_ID, $arEventFields, "N");
				$arResult["GOOD_SEND"] = "Y";
			} else {
				$arResult["LAST_ERROR"] = GetMessage("ALFA_MSG_EMPTY_REQUIRED_FIELDS");
			}
		}
	} else {
		$arResult["LAST_ERROR"] = GetMessage("ALFA_MSG_OLD_SESS");
	}
}

if($arParams["ALFA_USE_CAPTCHA"]=="Y")
	$arResult["CATPCHA_CODE"] = htmlspecialchars($APPLICATION->CaptchaGetCode());
	
$arResult["ACTION_URL"] = $APPLICATION->GetCurPage();

$this->IncludeComponentTemplate();
?>