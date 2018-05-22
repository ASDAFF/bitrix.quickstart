<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

// copy public files
CopyDirFiles(
	WIZARD_ABSOLUTE_PATH.'/site/public/'.LANGUAGE_ID.'/',
	WIZARD_SITE_PATH,
	$rewrite = true, 
	$recursive = true,
	$delete_after_copy = false
);

WizardServices::PatchHtaccess(WIZARD_SITE_PATH);