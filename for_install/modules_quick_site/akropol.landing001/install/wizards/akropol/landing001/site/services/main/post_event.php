<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$arEventTypes = Array();
$langs = CLanguage::GetList(($b=""), ($o=""));
while($language = $langs->Fetch())
{
	$lid = $language["LID"];
	IncludeModuleLangFile(__FILE__, $lid);
}

IncludeModuleLangFile(__FILE__);




// START ESEY POST TEMPLATE
$arFilter = Array(
    "TYPE_ID"       => "FEEDBACK_FORM",
    "ACTIVE"        => "Y",
    "SUBJECT"       => GetMessage("MF_EVENT_SUBJECT"),
    );

$rsMess = CEventMessage::GetList($by="site_id", $order="desc", $arFilter);
$GetPostTemplateEsey = $rsMess->Fetch();
if (empty($GetPostTemplateEsey))
{
	$emess = new CEventMessage;
	$arMessage = Array(
		"EVENT_NAME" => "FEEDBACK_FORM",
		"LID" => WIZARD_SITE_ID,
		"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
		"EMAIL_TO" => "#EMAIL_TO#",
		"SUBJECT" => GetMessage("MF_EVENT_SUBJECT"),
		"MESSAGE" => GetMessage("MF_EVENT_MESSAGE")
	);
	$PostTemplateID = $emess->Add($arMessage);
	//	echo $PostTemplateID;
}
else
{

	$arFilter2 = Array(
		"TYPE_ID"       => "FEEDBACK_FORM",
		"SITE_ID"       => WIZARD_SITE_ID,
		"ACTIVE"        => "Y",
		"SUBJECT"       => GetMessage("MF_EVENT_SUBJECT"),
		);

	$rsMess = CEventMessage::GetList($by="site_id", $order="desc", $arFilter2);
	$GetPostTemplateEseySite = $rsMess->Fetch();


	if (empty($GetPostTemplateEseySite))
	{


		$replacements = array(LID => array($GetPostTemplateEsey['LID'],WIZARD_SITE_ID));
		$GetPostTemplateEseyAdd = array_replace($GetPostTemplateEsey, $replacements);

		$em = new CEventMessage;
		$PostTemplateIDUp = $em->Update($GetPostTemplateEsey['ID'], $GetPostTemplateEseyAdd);
	}
	$PostTemplateID=$GetPostTemplateEsey['ID'];
	//	echo $PostTemplateID;
}
// END ESEY POST TEMPLATE

	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/sect_area_11.php", array(
		"EMAIL_TEMPLATE_ID" => htmlspecialcharsbx($PostTemplateID),
		"MAIL" => htmlspecialcharsbx($wizard->GetVar("admins_e_mail")),
	)
	);
?>