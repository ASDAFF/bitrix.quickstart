<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

if (!defined('WIZARD_SITE_ID') || !defined('WIZARD_SITE_DIR'))
{
	return;
}

$moduleId = 'prmedia.minimarket';
$wizardInstalled = COption::GetOptionString($moduleId, 'wizard_installed', 'N', WIZARD_SITE_ID);

if (COption::GetOptionString('main', 'upload_dir') == '')
{
	COption::SetOptionString('main', 'upload_dir', 'upload');
}

// copy files to /public directory
if($wizardInstalled == 'N' || WIZARD_INSTALL_DEMO_DATA)
{
	if(file_exists(WIZARD_ABSOLUTE_PATH . '/site/public/' . LANGUAGE_ID . '/'))
	{
		CopyDirFiles(
			WIZARD_ABSOLUTE_PATH . '/site/public/' . LANGUAGE_ID . '/',
			WIZARD_SITE_PATH,
			$rewrite = true,
			$recursive = true,
			$delete_after_copy = false
		);
	}
}

// create included areas
$wizard =& $this->GetWizard();
$includes = WIZARD_SITE_PATH . 'include_areas/';
___writeToAreasFile("$includes/sitename.php", $wizard->GetVar('sitename'));
___writeToAreasFile("$includes/slogan.php", $wizard->GetVar('slogan'));
___writeToAreasFile("$includes/sloganfooter.php", $wizard->GetVar('sloganfooter'));
___writeToAreasFile("$includes/schedule.php", $wizard->GetVar('schedule'));
___writeToAreasFile("$includes/phone.php", $wizard->GetVar('phone'));

if($wizardInstalled == "Y" && !WIZARD_INSTALL_DEMO_DATA)
{
	return;
}
WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

// replace macros
$replaceArray = array(
	'SITE_DIR' => WIZARD_SITE_DIR,
	'SITE_DESCRIPTION' => htmlspecialcharsbx($wizard->GetVar('metadescription')),
	'SITE_KEYWORDS' => htmlspecialcharsbx($wizard->GetVar('metakeywords'))
);
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH . 'about/', $replaceArray);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . '_index.php', $replaceArray);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . '.section.php', $replaceArray);

// set url rewrite rules
$currentRewrite = array();
if (file_exists(WIZARD_SITE_ROOT_PATH . '/urlrewrite.php'))
{
	include WIZARD_SITE_ROOT_PATH . '/urlrewrite.php';
}

$rewrite = array(
	
);
foreach ($rewrite as $url)
{
	if (!in_array($url, $currentRewrite))
	{
		CUrlRewriter::Add($url);
	}
}

function ___writeToAreasFile($path, $text)
{
	$fd = @fopen($path, 'wb');
	if (!$fd)
	{
		return false;
	}
	if (false === fwrite($fd, $text))
	{
		fclose($fd);
		return false;
	}
	fclose($fd);
	if (defined('BX_FILE_PERMISSIONS'))
	{
		@chmod($path, BX_FILE_PERMISSIONS);
	}
}