<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
$arFilter = Array("TYPE_ID" => "KREATTIKA_FEEDBACK_FORM", "ACTIVE" => "Y");
if($site !== false)
	$arFilter["LID"] = $site;

$arEvent = Array();
$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
while($arType = $dbType->GetNext())
	$arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["SUBJECT"];

$arComponentParameters = array(
	"GROUPS" => array(
		"FIELDS_SETTINGS_NAME" => array(
			"NAME" => GetMessage("KFF_FIELDS_SETTINGS_NAME"),
		),
		"FIELDS_SETTINGS_PHONE" => array(
			"NAME" => GetMessage("KFF_FIELDS_SETTINGS_PHONE"),
		),
		"FIELDS_SETTINGS_EMAIL" => array(
			"NAME" => GetMessage("KFF_FIELDS_SETTINGS_EMAIL"),
		),
		"FIELDS_SETTINGS_TEXT" => array(
			"NAME" => GetMessage("KFF_FIELDS_SETTINGS_TEXT"),
		),
		"MAIN_SETTINGS" => array(
			"NAME" => GetMessage("KFF_MAIN_SETTINGS"),
		),
	),
	"PARAMETERS" => array(
		"AJAX_MODE" => array(),
		"USE_FIELD_NAME" => Array(
			"NAME" => GetMessage("KFF_FIELD_NAME"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "FIELDS_SETTINGS_NAME",
			"REFRESH" => "Y",
		),
		"USE_FIELD_PHONE" => Array(
			"NAME" => GetMessage("KFF_FIELD_PHONE"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "FIELDS_SETTINGS_PHONE",
			"REFRESH" => "Y",
		),
		"USE_FIELD_EMAIL" => Array(
			"NAME" => GetMessage("KFF_FIELD_EMAIL"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "FIELDS_SETTINGS_EMAIL",
			"REFRESH" => "Y",
		),
		"USE_FIELD_TEXT" => Array(
			"NAME" => GetMessage("KFF_FIELD_TEXT"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "FIELDS_SETTINGS_TEXT",
			"REFRESH" => "Y",
		),
		"USE_CAPTCHA" => Array(
			"NAME" => GetMessage("KFF_CAPTCHA"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "MAIN_SETTINGS",
		),
		"SUBMIT_TITLE" => Array(
			"NAME" => GetMessage("KFF_SUBMIT"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("KFF_SUBMIT_TITLE"), 
			"PARENT" => "MAIN_SETTINGS",
		),
		"OK_TEXT" => Array(
			"NAME" => GetMessage("KFF_OK_MESSAGE"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("KFF_OK_TEXT"), 
			"PARENT" => "MAIN_SETTINGS",
		),
		"EMAIL_TO" => Array(
			"NAME" => GetMessage("KFF_EMAIL_TO"), 
			"TYPE" => "STRING",
			"DEFAULT" => htmlspecialcharsbx(COption::GetOptionString("main", "email_from")), 
			"PARENT" => "MAIN_SETTINGS",
		),
		"EVENT_MESSAGE_ID" => Array(
			"NAME" => GetMessage("KFF_EMAIL_TEMPLATES"), 
			"TYPE"=>"LIST", 
			"VALUES" => $arEvent,
			"DEFAULT"=>"", 
			"MULTIPLE"=>"Y", 
			"COLS"=>25, 
			"PARENT" => "MAIN_SETTINGS",
		),

	)
);

if($arCurrentValues["USE_FIELD_NAME"]!="N")
{
	$arComponentParameters["PARAMETERS"]["CHECK_FIELD_NAME"] = array(
		"PARENT" => "FIELDS_SETTINGS_NAME",
		"NAME" => GetMessage("KFF_CHECK_FIELD_NAME"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["FIELD_NAME_TITLE"] = array(
		"PARENT" => "FIELDS_SETTINGS_NAME",
		"NAME" => GetMessage("KFF_FIELD_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("KFF_FIELD_NAME_TITLE"),
	);
}

if($arCurrentValues["USE_FIELD_PHONE"]!="N")
{
	$arComponentParameters["PARAMETERS"]["CHECK_FIELD_PHONE"] = array(
		"PARENT" => "FIELDS_SETTINGS_PHONE",
		"NAME" => GetMessage("KFF_CHECK_FIELD_PHONE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["FIELD_PHONE_TITLE"] = array(
		"PARENT" => "FIELDS_SETTINGS_PHONE",
		"NAME" => GetMessage("KFF_FIELD_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("KFF_FIELD_PHONE_TITLE"),
	);
}

if($arCurrentValues["USE_FIELD_EMAIL"]!="N")
{
	$arComponentParameters["PARAMETERS"]["CHECK_FIELD_EMAIL"] = array(
		"PARENT" => "FIELDS_SETTINGS_EMAIL",
		"NAME" => GetMessage("KFF_CHECK_FIELD_EMAIL"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["FIELD_EMAIL_TITLE"] = array(
		"PARENT" => "FIELDS_SETTINGS_EMAIL",
		"NAME" => GetMessage("KFF_FIELD_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("KFF_FIELD_EMAIL_TITLE"),
	);
}

if($arCurrentValues["USE_FIELD_TEXT"]!="N")
{
	$arComponentParameters["PARAMETERS"]["CHECK_FIELD_TEXT"] = array(
		"PARENT" => "FIELDS_SETTINGS_TEXT",
		"NAME" => GetMessage("KFF_CHECK_FIELD_TEXT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	);
	$arComponentParameters["PARAMETERS"]["FIELD_TEXT_TITLE"] = array(
		"PARENT" => "FIELDS_SETTINGS_TEXT",
		"NAME" => GetMessage("KFF_FIELD_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("KFF_FIELD_TEXT_TITLE"),
	);
}


?>