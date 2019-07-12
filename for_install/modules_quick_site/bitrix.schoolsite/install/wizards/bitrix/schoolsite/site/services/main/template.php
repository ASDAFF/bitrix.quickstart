<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

//echo "WIZARD_SITE_ID=".WIZARD_SITE_ID." | ";
//echo "WIZARD_SITE_PATH=".WIZARD_SITE_PATH." | ";
//echo "WIZARD_RELATIVE_PATH=".WIZARD_RELATIVE_PATH." | ";
//echo "WIZARD_ABSOLUTE_PATH=".WIZARD_ABSOLUTE_PATH." | ";
//echo "WIZARD_TEMPLATE_ID=".WIZARD_TEMPLATE_ID." | ";
//echo "WIZARD_TEMPLATE_RELATIVE_PATH=".WIZARD_TEMPLATE_RELATIVE_PATH." | ";
//echo "WIZARD_TEMPLATE_ABSOLUTE_PATH=".WIZARD_TEMPLATE_ABSOLUTE_PATH." | ";
//echo "WIZARD_THEME_ID=".WIZARD_THEME_ID." | ";
//echo "WIZARD_THEME_RELATIVE_PATH=".WIZARD_THEME_RELATIVE_PATH." | ";
//echo "WIZARD_THEME_ABSOLUTE_PATH=".WIZARD_THEME_ABSOLUTE_PATH." | ";
//echo "WIZARD_SERVICE_RELATIVE_PATH=".WIZARD_SERVICE_RELATIVE_PATH." | ";
//echo "WIZARD_SERVICE_ABSOLUTE_PATH=".WIZARD_SERVICE_ABSOLUTE_PATH." | ";
//echo "WIZARD_IS_RERUN=".WIZARD_IS_RERUN." | ";
//die();

if (!defined("WIZARD_TEMPLATE_ID"))
	return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/";

CopyDirFiles(
	$_SERVER["DOCUMENT_ROOT"].WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site")."/".WIZARD_TEMPLATE_ID,
	$bitrixTemplateDir.WIZARD_TEMPLATE_ID,
	$rewrite = true,
	$recursive = true
);

$logo = WIZARD_SITE_LOGO;
if (intval($logo))
	$logo = CFile::GetPath($logo);
else
{
 if(WIZARD_TEMPLATE_ID == "school_modern")
	 $logo = '/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/images/logo_small.png';
 else
  $logo = '/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/images/logo.png';
}

CWizardUtil::ReplaceMacros(
	$bitrixTemplateDir.WIZARD_TEMPLATE_ID.'/include_areas/school_logo.php',
	array(
		"SCHOOL_LOGO" => $logo,
	)
);
if(WIZARD_TEMPLATE_ID == "school_modern")
{
 $background = WIZARD_SITE_BACKGROUND;
 if(intval($background))
  $background = '<img class="head" src="'.CFile::GetPath($background).'" alt=""/>';
 else
  $background = '';

 CWizardUtil::ReplaceMacros(
	 $bitrixTemplateDir.WIZARD_TEMPLATE_ID.'/include_areas/school_bg.php',
	 array(
		 "SITE_BACKGROUND" => $background,
	 )
 );
}




//Save options
COption::SetOptionString("main", "wizard_site_logo", WIZARD_SITE_LOGO);
COption::SetOptionString("main", "wizard_site_background", WIZARD_SITE_BACKGROUND);


//Attach template to default site
$obSite = CSite::GetList($by = "def", $order = "desc", Array("LID" => WIZARD_SITE_ID));
if($arSite = $obSite->Fetch())
{
	$arTemplates = Array();
	$found = false;
	$foundPrint = false;
	$obTemplate = CSite::GetTemplateList($arSite["LID"]);
	while($arTemplate = $obTemplate->Fetch())
	{
		if(!$found && strlen(trim($arTemplate["CONDITION"]))<=0)
		{
			$arTemplate["TEMPLATE"] = WIZARD_TEMPLATE_ID;
			$found = true;
		}
  
  if($arTemplate["TEMPLATE"] == "print")
			$foundPrint = true;
		
		$arTemplates[]= $arTemplate;
	}

	if(!$found)
		$arTemplates[]= Array("CONDITION" => "", "SORT" => 150, "TEMPLATE" => WIZARD_TEMPLATE_ID);
 
 if(!$foundPrint)
		$arTemplates[]= Array("CONDITION" => "\$_GET['print']=='Y'", "SORT" => 250, "TEMPLATE" => "school_print");

	$arFields = Array(
		"TEMPLATE" => $arTemplates,
		"NAME" => $arSite["NAME"],
	);

	$obSite = new CSite();
	$obSite->Update($arSite["LID"], $arFields);
}
COption::SetOptionString("main", "wizard_template_id", WIZARD_TEMPLATE_ID);
?>
