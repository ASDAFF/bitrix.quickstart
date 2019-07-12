<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;	

$arFilter = array(
    "TYPE_ID" => "PHONE_CALLBACK",
    "LID"     => "ru"
);
$rsET = CEventType::GetList($arFilter);
if($arET = $rsET->Fetch())
    return;
	
$et = new CEventType;
$et->Add(array(
	"LID"           => "ru",
	"EVENT_NAME"    => "PHONE_CALLBACK",
	"NAME"          => GetMessage("DVS_NAME"),
	"DESCRIPTION"   => "#NAME# - ".GetMessage("DVS_NAMEU").
"#EMAIL# - ".GetMessage("DVS_EMAIL").
"#TELEPHONE# - ".GetMessage("DVS_TELEPHONE").
"#SUBJECT# - ".GetMessage("DVS_SUBJECT").
"#TIME_FROM# - ".GetMessage("DVS_TIME_FROM").
"#TIME_TILL# - ".GetMessage("DVS_TIME_TILL")
));

$mess = array();
$mess["ACTIVE"] = "Y";
$mess["EVENT_NAME"] = "PHONE_CALLBACK";
$mess["LID"] = WIZARD_SITE_ID;
$mess["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#";
$mess["EMAIL_TO"] = "#DEFAULT_EMAIL_FROM#";
$mess["SUBJECT"] = GetMessage("DVS_SUBJ");
$mess["BODY_TYPE"] = "text";
$mess["MESSAGE"] = GetMessage("DVS_MES");

$emess = new CEventMessage;
$emess->Add($mess);
?>
