<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_TEMPLATE_ID"))
	return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID;

CopyDirFiles(
	$_SERVER["DOCUMENT_ROOT"].WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site")."/".WIZARD_TEMPLATE_ID,
	$bitrixTemplateDir,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false,
	$exclude = "themes"
);

//Attach template to default site
$obSite = CSite::GetList($by = "def", $order = "desc", Array("LID" => WIZARD_SITE_ID));
if ($arSite = $obSite->Fetch())
{
	$arTemplates = Array();
	$found = false;
	$foundEmpty = false;
	$obTemplate = CSite::GetTemplateList($arSite["LID"]);
	while($arTemplate = $obTemplate->Fetch())
	{
		if(!$found && strlen(trim($arTemplate["CONDITION"]))<=0)
		{
			$arTemplate["TEMPLATE"] = WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID;
			$found = true;
		}
		if($arTemplate["TEMPLATE"] == "empty")
		{
			$foundEmpty = true;
			continue;
		}
		$arTemplates[]= $arTemplate;
	}

	if (!$found)
		$arTemplates[]= Array("CONDITION" => "", "SORT" => 150, "TEMPLATE" => WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID);

	$arFields = Array(
		"TEMPLATE" => $arTemplates,
		"NAME" => $arSite["NAME"],
	);

	$obSite = new CSite();
	$obSite->Update($arSite["LID"], $arFields);
}
COption::SetOptionString("main", "wizard_template_id", WIZARD_TEMPLATE_ID, false, WIZARD_SITE_ID);

//NEW ORDER_ADMIN USER MAIL TEMPLATE AND TYPE
$rsET = CEventType::GetByID("NEW_ORDER_ADMIN", "ru");
if($arET = $rsET->Fetch()){}else{
	$et = new CEventType;
	$et->Add(array(
		"LID"           => "ru",
		"EVENT_NAME"    => "NEW_ORDER_ADMIN",
		"NAME"          => GetMessage("EVENT_TYPE_NEW_ORDER_ADMIN_NAME"),
		"DESCRIPTION"   => GetMessage("EVENT_TYPE_NEW_ORDER_ADMIN_DESCRIPTION"),
	));
}
$rsMess = CEventMessage::GetList($by="site_id", $order="desc", array("TYPE_ID" => "NEW_ORDER_ADMIN"));
if($arMess = $rsMess->Fetch()){
	$EVENT_MESSAGE_NEW_ORDER_ADMIN = $arMess["ID"];
}else{
	$arr["ACTIVE"] = "Y";
	$arr["EVENT_NAME"] = "NEW_ORDER_ADMIN";
	$arr["LID"] = array("s1");
	$arr["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#";
	$arr["EMAIL_TO"] = "#DEFAULT_EMAIL_FROM#";
	$arr["SUBJECT"] = GetMessage("EVENT_MESSAGE_NEW_ORDER_ADMIN_SUBJECT");
	$arr["BODY_TYPE"] = "html";
	$arr["MESSAGE"] = GetMessage("EVENT_MESSAGE_NEW_ORDER_ADMIN_MESSAGE");
	$emess = new CEventMessage;
	$EVENT_MESSAGE_NEW_ORDER_ADMIN = $emess->Add($arr);
}

//NEW ORDER USER MAIL TEMPLATE AND TYPE
$rsET = CEventType::GetByID("NEW_ORDER_USER", "ru");
if($arET = $rsET->Fetch()){}else{
	$et = new CEventType;
	$et->Add(array(
		"LID"           => "ru",
		"EVENT_NAME"    => "NEW_ORDER_USER",
		"NAME"          => GetMessage("EVENT_TYPE_NEW_ORDER_USER_NAME"),
		"DESCRIPTION"   => GetMessage("EVENT_TYPE_NEW_ORDER_USER_DESCRIPTION"),
	));
}
$rsMess = CEventMessage::GetList($by="site_id", $order="desc", array("TYPE_ID" => "NEW_ORDER_USER"));
if($arMess = $rsMess->Fetch()){
	$EVENT_MESSAGE_NEW_ORDER_USER = $arMess["ID"];
}else{
	$arr["ACTIVE"] = "Y";
	$arr["EVENT_NAME"] = "NEW_ORDER_USER";
	$arr["LID"] = array("s1");
	$arr["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#";
	$arr["EMAIL_TO"] = "#USERMAIL#";	
	$arr["SUBJECT"] = GetMessage("EVENT_MESSAGE_NEW_ORDER_USER_SUBJECT");
	$arr["BODY_TYPE"] = "html";
	$arr["MESSAGE"] = GetMessage("EVENT_MESSAGE_NEW_ORDER_USER_MESSAGE");
	$emess = new CEventMessage;
	$EVENT_MESSAGE_NEW_ORDER_USER = $emess->Add($arr);
}

CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT']."/developer/send.php", array("NEW_ORDER_ADMIN" => $EVENT_MESSAGE_NEW_ORDER_ADMIN));
CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT']."/developer/send.php", array("NEW_ORDER_USER" => $EVENT_MESSAGE_NEW_ORDER_USER));
?>

