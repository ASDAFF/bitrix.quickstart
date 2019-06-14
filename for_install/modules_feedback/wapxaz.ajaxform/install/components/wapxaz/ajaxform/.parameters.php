<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*
$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
$arFilter = Array("TYPE_ID" => "FEEDBACK_FORM", "ACTIVE" => "Y");
if($site !== false)
	$arFilter["LID"] = $site;

$arEvent = Array();
$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
while($arType = $dbType->GetNext())
	$arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["SUBJECT"];
*/

$arComponentParameters = array(
	"PARAMETERS" => array(
		"USE_JQUERY3_2_1" => Array(
			"NAME" => GetMessage("USE_JQUERY3_2_1"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "BASE",
		),
		"FORM_TITLE" => Array(
			"NAME" => GetMessage("FORM_TITLE"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("FORM_TITLE_DEFAULT"), 
			"PARENT" => "BASE",
		),
		"USE_NAME" => Array(
			"NAME" => GetMessage("USE_NAME"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "BASE",
		),
		"USE_PHONE" => Array(
			"NAME" => GetMessage("USE_PHONE"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "BASE",
		),
		"USE_MESSAGE" => Array(
			"NAME" => GetMessage("USE_MESSAGE"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "BASE",
		),
		"USE_RULE" => Array(
			"NAME" => GetMessage("USE_RULE"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "BASE",
		),
		"USE_CAPTCHA" => Array(
			"NAME" => GetMessage("MFP_CAPTCHA"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "BASE",
		),
		"OK_TEXT" => Array(
			"NAME" => GetMessage("MFP_OK_MESSAGE"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MFP_OK_TEXT"), 
			"PARENT" => "BASE",
		),
		"EMAIL_TO" => Array(
			"NAME" => GetMessage("MFP_EMAIL_TO"), 
			"TYPE" => "STRING",
			"DEFAULT" => htmlspecialcharsbx(COption::GetOptionString("main", "email_from")), 
			"PARENT" => "BASE",
		),
		"FORM_BTN_SUBMIT" => Array(
			"NAME" => GetMessage("FORM_BTN_SUBMIT"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("FORM_BTN_SUBMIT_DEFAULT"), 
			"PARENT" => "BASE",
		),
		"URL_RULES" => Array(
			"NAME" => GetMessage("URL_RULES"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("URL_RULES_DEFAULT"), 
			"PARENT" => "BASE",
		),
	)
);


?>