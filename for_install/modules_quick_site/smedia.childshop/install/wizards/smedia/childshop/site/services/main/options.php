<?
$wizard =& $this->GetWizard();

if (WIZARD_IS_RERUN===true) 
	return;

COption::SetOptionString("iblock", "use_htmledit", "Y");
COption::SetOptionString("iblock", "combined_list_mode", "Y");
COption::SetOptionString("main", "map_top_menu_type", "top,main3,bottom");
COption::SetOptionString("main", "map_left_menu_type", "right");
COption::SetOptionString("main", "save_original_file_name", "Y");

COption::SetOptionString('main', 'captcha_registration', 'Y');
COption::SetOptionString("main", "wizard_allow_guests", 'Y');
COption::SetOptionString("main", "new_user_registration", "Y");

$siteName = htmlspecialcharsEx($wizard->GetVar("siteName"));
$companyLogo = $wizard->GetVar("siteLogo");
$includePath = WIZARD_SITE_PATH.WIZARD_SITE_DIR.'include/';
$phone = $wizard->GetVar("company_phone");
$icq = $wizard->GetVar("company_icq");
$skype = $wizard->GetVar("company_skype");
$address = $wizard->GetVar("company_address");
$mail = $wizard->GetVar("company_mail");
if (!empty ($skype))
	$skypeText = '<p><b>Skype</b>: <a href="skype:'.$skype.'" >'.$skype.'</a></p>';
else
	$skypeText='';
if (!empty ($icq))
	$icqText = '<p><strong>ICQ:</strong> '.icq.' </p>';
else
	$icqText='';
$phoneCode = $wizard->GetVar("company_phone_code");
if (strpos ($phoneCode,'(')===false)
	$phoneCode = '('.$phoneCode;
if (strpos ($phoneCode,')')===false)
	$phoneCode = $phoneCode.')';

CWizardUtil::ReplaceMacros($includePath.'icq.php', Array("ICQ" => $icq));
CWizardUtil::ReplaceMacros($includePath.'telephone.php', Array("PHONE_CODE" => $phoneCode,"PHONE" => $phone));
CWizardUtil::ReplaceMacros($includePath.'contacts.php', Array("PHONE_CODE" => $phoneCode,"PHONE" => $phone,"ICQ" => $icqText, "ADDRESS" => $address, "EMAIL" => $mail));
CWizardUtil::ReplaceMacros($includePath.'delivery.php', Array("PHONE_CODE" => $phoneCode,"PHONE" => $phone,"SKYPE" => $skypeText, "EMAIL" => $mail));
CWizardUtil::ReplaceMacros($includePath.'mail.php', Array("EMAIL" => $mail));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR.'about/contacts/index.php', Array("EMAIL" => $mail));
COption::SetOptionString("main", "email_from", $mail);
COption::SetOptionString("sale", "order_email", $mail);
CWizardUtil::ReplaceMacros($includePath.'company_name.php', Array("SHOP_NAME" => $siteName));
$server_name=$wizard->GetVar("server_name");
COption::SetOptionString("main", "server_name", $server_name);
COption::SetOptionString("main", "site_name", $siteName);
$obSite = new CSite;
$arFields=array('NAME'=>$siteName, "SERVER_NAME"=>$server_name);
$result = $obSite->Update(WIZARD_SITE_ID, $arFields);

$arUrlRewrite = array(); 
if (file_exists(WIZARD_SITE_PATH."/urlrewrite.php"))
{
	include(WIZARD_SITE_PATH."/urlrewrite.php");
}
$arNewUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."personal/order/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:sale.personal.order",
		"PATH"	=>	WIZARD_SITE_DIR."personal/order/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:catalog",
		"PATH"	=>	WIZARD_SITE_DIR."catalog/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."news/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	WIZARD_SITE_DIR."news/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."info/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	WIZARD_SITE_DIR."info/index.php",
	),
);
foreach ($arNewUrlRewrite as $arUrl)
{
	if (!in_array($arUrl, $arUrlRewrite))
	{
		CUrlRewriter::Add($arUrl);
	}
}

?>