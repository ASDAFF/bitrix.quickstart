<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");
$arParams["EVENT_NAME"] = trim($arParams["EVENT_NAME"]);
if(strlen($arParams["EVENT_NAME"]) <= 0)
	$arParams["EVENT_NAME"] = "CALLBACK_FORM";
$arParams["EMAIL_TO"] = trim($arParams["EMAIL_TO"]);
if(strlen($arParams["EMAIL_TO"]) <= 0)
	$arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");
$arParams["OK_TEXT"] = trim($arParams["OK_TEXT"]);
if(strlen($arParams["OK_TEXT"]) <= 0)
	$arParams["OK_TEXT"] = GetMessage("MF_OK_MESSAGE");

if($_SERVER["REQUEST_METHOD"] == "POST" && strlen($_POST["submit"]) > 0)
{
	if(check_bitrix_sessid())
	{
//		$_POST["user_phone"] = preg_replace("/(-|\s|\+|\(|\))/","",$_POST["user_phone"]);
		if(empty($arParams["REQUIRED_FIELDS"]) || !in_array("NONE", $arParams["REQUIRED_FIELDS"]))
		{
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["user_name"]) <= 1)
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_NAME");		
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["user_phone"]) <= 1)
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_PHONE");
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("TIME", $arParams["REQUIRED_FIELDS"])) && (strlen($_POST["user_time_from"]) <= 1 || strlen($_POST["user_time_to"]) <= 1))
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_TIME");
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["MESSAGE"]) <= 3)
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_MESSAGE");
		}
		if(strlen($_POST["user_phone"]) > 1 && !preg_match("/^(-|\s|\+|\(|\)|\d)+$/",$_POST["user_phone"]))
			$arResult["ERROR_MESSAGE"][] = GetMessage("MF_PHONE_NOT_VALID");
		if($arParams["USE_CAPTCHA"] == "Y")
		{
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
			$captcha_code = $_POST["captcha_sid"];
			$captcha_word = $_POST["captcha_word"];
			$cpt = new CCaptcha();
			$captchaPass = COption::GetOptionString("main", "captcha_password", "");
			if (strlen($captcha_word) > 0 && strlen($captcha_code) > 0)
			{
				if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
					$arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTCHA_WRONG");
			}
			else
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTHCA_EMPTY");

		}
		if(empty($arResult))
		{
			$arFields = Array(
				"AUTHOR" => $_POST["user_name"],
				"AUTHOR_PHONE" => $_POST["user_phone"],
				"TIME_FROM" => $_POST["user_time_from"],
				"TIME_TO" => $_POST["user_time_to"],
				"EMAIL_TO" => $arParams["EMAIL_TO"],
				"TEXT" => $_POST["MESSAGE"],
			);
			if(!empty($arParams["EVENT_MESSAGE_ID"]))
			{
				foreach($arParams["EVENT_MESSAGE_ID"] as $v)
					if(IntVal($v) > 0)
						CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields, "N", IntVal($v));
			}
			else
				CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields);
			$_SESSION["MF_NAME"] = htmlspecialcharsEx($_POST["user_name"]);
			$_SESSION["MF_PHONE"] = htmlspecialcharsEx($_POST["user_phone"]);
			$_SESSION["MF_TIME_FROM"] = htmlspecialcharsEx($_POST["user_time_from"]);
			$_SESSION["MF_TIME_TO"] = htmlspecialcharsEx($_POST["user_time_to"]);
			LocalRedirect($APPLICATION->GetCurPageParam("success=Y", Array("success")));
		}
		
		$arResult["MESSAGE"] = htmlspecialcharsEx($_POST["MESSAGE"]);
		$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($_POST["user_name"]);
		$arResult["AUTHOR_PHONE"] = htmlspecialcharsEx($_POST["user_phone"]);
		$arResult["TIME_FROM"] = htmlspecialcharsEx($_POST["user_time_from"]);
		$arResult["TIME_TO"] = htmlspecialcharsEx($_POST["user_time_to"]);
	}
	else
		$arResult["ERROR_MESSAGE"][] = GetMessage("MF_SESS_EXP");
}
elseif($_REQUEST["success"] == "Y")
{
	$arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
}

if(empty($arResult["ERROR_MESSAGE"]))
{
	if($USER->IsAuthorized())
	{
		$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($USER->GetFullName());
		$arUser = CUser::GetList(($by="ID"),($order="asc"),array("ID"=>$USER->GetID()),array("FIELDS"=>array("PERSONAL_PHONE", "PERSONAL_MOBILE")));
		$arResult["AUTHOR_PHONE"] = "";
		while ($rsUser=$arUser->GetNext())
			$arResult["AUTHOR_PHONE"] = strlen(htmlspecialcharsEx($rsUser["PERSONAL_PHONE"]))>0? htmlspecialcharsEx($rsUser["PERSONAL_PHONE"]): htmlspecialcharsEx($rsUser["PERSONAL_MOBILE"]);
		$arResult["TIME_FROM"] = "";
		$arResult["TIME_TO"] = "";
	}
	else
	{
		if(strlen($_SESSION["MF_NAME"]) > 0)
			$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($_SESSION["MF_NAME"]);
		if(strlen($_SESSION["MF_PHONE"]) > 0)
			$arResult["AUTHOR_PHONE"] = htmlspecialcharsEx($_SESSION["MF_PHONE"]);
		if(strlen($_SESSION["MF_TIME_FROM"]) > 0)
			$arResult["TIME_FROM"] = htmlspecialcharsEx($_SESSION["MF_TIME_FROM"]);
		if(strlen($_SESSION["MF_TIME_TO"]) > 0)
			$arResult["TIME_TO"] = htmlspecialcharsEx($_SESSION["MF_TIME_TO"]);
	}
}

if($arParams["USE_CAPTCHA"] == "Y")
	$arResult["capCode"] =  htmlspecialchars($APPLICATION->CaptchaGetCode());

$this->IncludeComponentTemplate();
?>