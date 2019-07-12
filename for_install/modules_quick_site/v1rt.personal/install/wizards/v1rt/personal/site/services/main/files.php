<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!defined("WIZARD_SITE_ID")) return;
if (!defined("WIZARD_SITE_DIR")) return;
if (!defined("WIZARD_TEMPLATE_ID")) return;

// установим в настройках корневую папку для чпу
COption::SetOptionString($moduleId, "SEF_FOLDER", WIZARD_SITE_DIR);
$wizard =& $this->GetWizard();

if(WIZARD_INSTALL_DEMO_DATA)
{
	$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/".WIZARD_TEMPLATE_ID."/"); 
	$handle = @opendir($path);
	if ($handle)
	{
		while ($file = readdir($handle))
		{
			if (in_array($file, array(".", "..")))
				continue; 
			
			CopyDirFiles(
				$path.$file,
				WIZARD_SITE_PATH."/".$file,
				$rewrite = true, 
				$recursive = true,
				$delete_after_copy = false
			);
		}

        if(CModule::IncludeModule("search") && $wizard->GetVar("reIndex"))
            CSearch::ReIndexAll(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR));
	}

	WizardServices::PatchHtaccess(WIZARD_SITE_PATH);
    WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));

	$arUrlRewrite = array(); 
	if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
	{
		include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
	}

	$arNewUrlRewrite = array(
		array(
			"CONDITION"	=> "#^".WIZARD_SITE_DIR."gallery/#",
			"PATH"	    => WIZARD_SITE_DIR."gallery/index.php",
            "ID"        => "v1rt.personal:medialibrary"
		),
		array(
			"CONDITION"	=> "#^".WIZARD_SITE_DIR."news/#",
			"PATH"	    => WIZARD_SITE_DIR."news/index.php",
            "ID"        => "bitrix:news"
		)
	); 
	
	foreach ($arNewUrlRewrite as $arUrl)
	{
		if (!in_array($arUrl, $arUrlRewrite))
		{
			CUrlRewriter::Add($arUrl);
		}
	}
}

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));
?>