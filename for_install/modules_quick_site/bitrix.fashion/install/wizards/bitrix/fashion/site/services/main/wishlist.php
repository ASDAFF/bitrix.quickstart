<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;	

$arFilter = array(
    "TYPE_ID" => "UF_WISHLIST_SEND",
    "LID"     => "ru"
);
$rsET = CEventType::GetList($arFilter);
if($arET = $rsET->Fetch())
    return;
	
$et = new CEventType;
$et->Add(array(
	"LID"           => "ru",
	"EVENT_NAME"    => "UF_WISHLIST_SEND",
	"NAME"          => GetMessage("DVS_NAME"),
	"DESCRIPTION"   => "#EMAIL#
#URL#
#LOGIN#"
));

$mess = array();
$mess["ACTIVE"] = "Y";
$mess["EVENT_NAME"] = "UF_WISHLIST_SEND";
$mess["LID"] = WIZARD_SITE_ID;
$mess["EMAIL_FROM"] = "#EMAILFROM#";
$mess["EMAIL_TO"] = "#EMAILTO#";
$mess["SUBJECT"] = GetMessage("DVS_SUBJ");
$mess["BODY_TYPE"] = "html";
$mess["MESSAGE"] = GetMessage("DVS_MES");

$emess = new CEventMessage;
$emess->Add($mess);
?>
