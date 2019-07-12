<?defined("B_PROLOG_INCLUDED")&&B_PROLOG_INCLUDED or die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;



//throw new Exception;

$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/");

CopyDirFiles(
	$path,
	WIZARD_SITE_PATH,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false
);


$path_components=str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/components/");
CopyDirFiles(
	$path_components,
	WIZARD_SITE_ROOT_PATH.BX_PERSONAL_ROOT."/components",
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false
);

CModule::IncludeModule("search");
CSearch::ReIndexAll(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR));

$arrReplace = array("SITE_DIR"=>WIZARD_SITE_DIR);
$arrReplace["EMAIL_TO"]=$wizard->GetVar("EMAIL_FROM");
$arrReplace["PHONE"]=$wizard->GetVar("siteTelephone");
$arrReplace["ADDRESS"]=$wizard->GetVar("siteAddress");
$arrReplace["COPYRIGHT"]=$wizard->GetVar("siteCopy");
$arrReplace["SCHEDULE"]=$wizard->GetVar("siteSchedule");
$arrReplace["SHOP_NAME"]=$wizard->GetVar("siteName");
//$arrReplace["SHOP_NAME"]=str_replace(array('"'), array('\"'), $arrReplace["SHOP_NAME"]);
$arrReplace["SHOP_NAME"]=htmlspecialcharsEx($arrReplace["SHOP_NAME"]);
$arrReplace["SITE_DESCRIPTION"]=$wizard->GetVar("siteMetaDescription");
$arrReplace["SITE_KEYWORDS"]=$wizard->GetVar("siteMetaKeywords");

$arrReplace["SS_FB"]=$wizard->GetVar("shopFacebook");
$arrReplace["SS_VK"]=$wizard->GetVar("shopTwitter");
$arrReplace["SS_TWITTER"]=$wizard->GetVar("shopVk");

if($wizard->GetVar('siteLogoSet', true)){
	$ff = CFile::GetByID($wizard->GetVar("siteLogo"));	
	if($zr = $ff->Fetch())
	{
		$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
		@copy($strOldFile, WIZARD_SITE_PATH."include/logo.png");
		$arrReplace["LOGO_SRC"]=WIZARD_SITE_DIR."include/logo.png";
		$arrReplace["LOGO_WIDTH"]=$zr["WIDTH"];
		$arrReplace["LOGO_HEIGHT"]=$zr["HEIGHT"];
		CFile::Delete($siteLogo);
	}else if (!file_exists(WIZARD_SITE_PATH."include/logo.jpg")){
		copy(WIZARD_THEME_ABSOLUTE_PATH."/lang/".LANGUAGE_ID."/logo.png", WIZARD_SITE_PATH."include/bx_default_logo.png");
		$arrReplace["LOGO_SRC"]=WIZARD_SITE_DIR."include/bx_default_logo.png";
		$arrReplace["LOGO_WIDTH"]="199";
		$arrReplace["LOGO_HEIGHT"]="40";
			
	}
}


WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, $arrReplace);

// set rights
global $APPLICATION;
$APPLICATION->SetFileAccessPermission(
	array(WIZARD_SITE_ID,'/'),
	array('*' => 'R')
);

$arUrlRewrite = array(); 
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
{
    include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}

$arNewUrlRewrite = array(
    array(
        "CONDITION"    =>    "#^".WIZARD_SITE_DIR."news/#",
        "RULE"    =>    "",
        "ID"    =>    "bitrix:news",
        "PATH"    =>     WIZARD_SITE_DIR."news/index.php",
        ), 

    );     


$arNewUrlRewrite[] = array(
    array(
        "CONDITION"    =>    "#^".WIZARD_SITE_DIR."manufacturers/#",
        "RULE"    =>    "",
        "ID"    =>    "bitrix:news",
        "PATH"    =>     WIZARD_SITE_DIR."manufacturers/index.php",
        ), 

    );     

$arNewUrlRewrite[] =
    array(
        "CONDITION"    =>    "#^".WIZARD_SITE_DIR."catalog/#",
        "RULE"    =>    "",
        "ID"    =>    "bitrix:catalog",
        "PATH"    =>     WIZARD_SITE_DIR."catalog/index.php",
        );    


$arNewUrlRewrite[] =
    array(
        "CONDITION"    =>    "#^".WIZARD_SITE_DIR."personal/order/#",
        "RULE"    =>    "",
        "ID"    =>    "bitrix:sale.personal.order",
        "PATH"    =>     WIZARD_SITE_DIR."personal/order/index.php",
        );



foreach ($arNewUrlRewrite as $arUrl)
{
    if (!in_array($arUrl, $arUrlRewrite))
    {
        CUrlRewriter::Add($arUrl);
    }
}



?>