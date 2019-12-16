<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/************************************
*
* redsign.devcom
* last update 02.07.2014
*
************************************/

global $APPLICATION;

$arParams["EVENT_TYPE"] = "REDSIGN_EMAIL_TO_FRIEND";
$arParams["REQUEST_PARAM_NAME"] = "redsign_email2friend";
$arResult["ACTION_URL"] = $APPLICATION->GetCurPage();
$arParams["SHOW_FIELDS"][] = "RS_EMAIL_TO";
$arParams["REQUIRED_FIELDS"][] = "RS_EMAIL_TO";
$arParams["SHOW_FIELDS"][] = "RS_LINK";
$arParams["REQUIRED_FIELDS"][] = "RS_LINK";
$arResult["PARAMS_HASH"] = md5(serialize($arParams).$this->GetTemplateName());

$arFields = array(
	array(
		"CONTROL_NAME" => "RS_AUTHOR_NAME",
		"CONTROL_ID" => "RS_AUTHOR_NAME",
		"SHOW" => in_array("RS_AUTHOR_NAME", $arParams["SHOW_FIELDS"]) ? "Y" : "N",
		"EVENT_FIELD_NAME" => "AUTHOR",
		"VALUE" => "",
		"HTML_VALUE" => "",
	),
	array(
		"CONTROL_NAME" => "RS_EMAIL_TO",
		"CONTROL_ID" => "RS_EMAIL_TO",
		"SHOW" => in_array("RS_EMAIL_TO", $arParams["SHOW_FIELDS"]) ? "Y" : "N",
		"EVENT_FIELD_NAME" => "EMAIL_TO",
		"VALUE" => "",
		"HTML_VALUE" => "",
	),
	array(
		"CONTROL_NAME" => "RS_AUTHOR_COMMENT",
		"CONTROL_ID" => "RS_AUTHOR_COMMENT",
		"SHOW" => in_array("RS_AUTHOR_COMMENT", $arParams["SHOW_FIELDS"]) ? "Y" : "N",
		"EVENT_FIELD_NAME" => "AUTHOR_COMMENT",
		"VALUE" => "",
		"HTML_VALUE" => "",
	),
	array(
		"CONTROL_NAME" => "RS_LINK",
		"CONTROL_ID" => "RS_LINK",
		"SHOW" => in_array("RS_LINK", $arParams["SHOW_FIELDS"]) ? "Y" : "N",
		"EVENT_FIELD_NAME" => "LINK",
		"VALUE" => ( $arParams['ALFA_LINK']!='' ? $arParams['ALFA_LINK'] : "http://".$_SERVER["HTTP_HOST"].$APPLICATION->GetCurPageParam() ),
		"HTML_VALUE" => "",
	),
);
$arResult["FIELDS"] = $arFields;

if(!function_exists('redsign_add_email2friend_type'))
{
	function redsign_add_email2friend_type($EVENT_TYPE)
	{
		global $DB, $DBType, $APPLICATION;
		$return = false;
		$et = new CEventType;
		$EventTypeID = $et->Add(array(
			"LID"           => "ru",
			"EVENT_NAME"    => $EVENT_TYPE,
			"NAME"          => GetMessage("INSTALL_EVENT_TYPE_NAME"),
			"DESCRIPTION"   => GetMessage("INSTALL_EVENT_TYPE_DESCRIPTION")
			)
		);
		if($EventTypeID>0)
		{
			$arSites = array();
			$rsSites = CSite::GetList($by="sort", $order="desc", array());
			while ($arSite = $rsSites->Fetch())
			{
				$arSites[] = $arSite["LID"];
			}
			$arr["ACTIVE"] = "Y";
			$arr["EVENT_NAME"] = $EVENT_TYPE;
			$arr["LID"] = $arSites;
			$arr["EMAIL_FROM"] = "#EMAIL_FROM#";
			$arr["EMAIL_TO"] = "#EMAIL_TO#";
			$arr["BCC"] = "";
			$arr["SUBJECT"] = "#THEME#";
			$arr["BODY_TYPE"] = "text";
			$arr["MESSAGE"] = GetMessage("INSTALL_EVENT_TEMPLATE_BODY");

			$emess = new CEventMessage;
			$EventTemplateID = $emess->Add($arr);
			if($EventTemplateID>0)
			{
				$return = true;
			}
		} else {
			$return = false;
		}
		return $return;
	}
}

if($USER->IsAuthorized()) $arParams["ALFA_USE_CAPTCHA"] = "N";

if($arParams["ALFA_USE_CAPTCHA"]=="Y")
{
	include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
	$cpt = new CCaptcha();
	$captchaPass = COption::GetOptionString("main", "captcha_password", "");
	if(strlen($captchaPass) <= 0)
	{
		$captchaPass = randString(10);
		COption::SetOptionString("main", "captcha_password", $captchaPass);
	}
	$cpt->SetCodeCrypt($captchaPass);
	$arResult["CATPCHA_CODE"] = htmlspecialchars( $cpt->GetCodeCrypt() );
}

if($_REQUEST[$arParams["REQUEST_PARAM_NAME"]]=="Y" && class_exists(CEventType) && class_exists(CEventMessage))
{
	$arFilter = array("TYPE_ID" => $arParams["EVENT_TYPE"], "LID" => "ru");
	$rsET = CEventType::GetList($arFilter);
	if(!$arET = $rsET->Fetch())
	{
		redsign_add_email2friend_type($arParams["EVENT_TYPE"]);
	}
	$arResult["LAST_ERROR"] = "";
	$arResult["GOOD_SEND"] = "";
	
	if(check_bitrix_sessid() && (!isset($_REQUEST["PARAMS_HASH"]) || $arResult["PARAMS_HASH"] === $_REQUEST["PARAMS_HASH"]))
	{
		if($arParams["ALFA_USE_CAPTCHA"] == "Y")
		{
			if(strlen($_POST["captcha_word"])<1 && strlen($_POST["captcha_sid"])<1)
			{
				$arResult["LAST_ERROR"] = GetMessage("ALFA_MSG_CAPTCHA_EMPRTY");
			} elseif(!$APPLICATION->CaptchaCheckCode($_POST["captcha_word"], $_POST["captcha_sid"]))
			{
				$arResult["LAST_ERROR"] = GetMessage("ALFA_MSG_CAPTCHA_WRONG");
			}
		}
		
		if($arResult["LAST_ERROR"]=="")
		{
			$arEventFields = array();
			$arEventFields["THEME"] = $arParams["ALFA_MESSAGE_THEMES"];
			
			foreach($arResult["FIELDS"] as $key => $arField)
			{
				$arEventFields[$arField["EVENT_FIELD_NAME"]] = trim( ( $_REQUEST[$arField["CONTROL_NAME"]] ) );
				$arEventFields["THEME"] = str_replace("#".$arField["EVENT_FIELD_NAME"]."#", $_REQUEST[$arField["CONTROL_NAME"]], $arEventFields["THEME"]);
				if($arField["EVENT_FIELD_NAME"]=="EMAIL_TO")
				{
					$arEventFields["EMAIL_TO"] = trim( ( $_REQUEST[$arField["CONTROL_NAME"]] ) );
					if(!check_email($arEventFields["EMAIL_TO"]))
					{
						$arResult["LAST_ERROR"] = GetMessage("ALFA_MSG_BAD_EMAIL_TO_CHECK");
					}
				}
				if((empty($arParams["REQUIRED_FIELDS"]) || in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])) && strlen($_REQUEST[$arField["CONTROL_NAME"]]) <= 1)
				{
					$arResult["LAST_ERROR"] = GetMessage("ALFA_MSG_EMPTY_REQUIRED_FIELDS");
				}
			}
			if($arResult["LAST_ERROR"]=="")
			{
				CEvent::Send($arParams["EVENT_TYPE"], SITE_ID, $arEventFields, "N");
				$arResult["GOOD_SEND"] = "Y";
			}
		}
		foreach($arResult["FIELDS"] as $key => $arField)
		{
			// set request
			$arResult["FIELDS"][$key]["HTML_VALUE"] = $_REQUEST[$arField["CONTROL_NAME"]];
		}
	} else {
		$arResult["LAST_ERROR"] = GetMessage("ALFA_MSG_OLD_SESS");
	}
}

$this->IncludeComponentTemplate();