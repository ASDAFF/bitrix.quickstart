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
___writeToAreasFile("$includes/header_contact.php", $wizard->GetVar('headercontact'));
___writeToAreasFile("$includes/schedule.php", $wizard->GetVar('schedule'));
___writeToAreasFile("$includes/phone.php", $wizard->GetVar('phone'));
___writeToAreasFile("$includes/phone_footer.php", $wizard->GetVar('phonefooter'));
___writeToAreasFile("$includes/sitename_footer.php", $wizard->GetVar('sitenamefooter'));
___writeToAreasFile("$includes/slogan_footer.php", $wizard->GetVar('sloganfooter'));
___writeToAreasFile("$includes/phonelabel_footer.php", $wizard->GetVar('phonelabelfooter'));
___writeToAreasFile("$includes/phone_footer.php", $wizard->GetVar('phonefooter'));

// logo
$logo = intval($wizard->GetVar('logo'));
if ($logo > 0)
{
	$rsFile = CFile::GetByID($logo);
	if ($logo = $rsFile->Fetch())
	{
		$logoPath = WIZARD_SITE_ROOT_PATH . '/';
		$logoPath .= COption::GetOptionString('main', 'upload_dir', 'upload') . '/' . $logo['SUBDIR'] . '/' . $logo['FILE_NAME'];
		@copy($logoPath, WIZARD_SITE_PATH . 'images/logo.png');
		CFile::Delete($logo['ID']);
		$logo = WIZARD_SITE_DIR . 'images/logo.png';
		COption::SetOptionString($moduleId, 'logo', $logo, false, WIZARD_SITE_ID);
	}
}
else if (!file_exists(WIZARD_SITE_DIR . 'images/logo.png'))
{
	@copy(WIZARD_ABSOLUTE_PATH . '/site/public/' . LANGUAGE_ID . '/images/def-logo.png', WIZARD_SITE_PATH . 'images/logo.png');
}


if($wizardInstalled == 'Y' && !WIZARD_INSTALL_DEMO_DATA)
{
	return;
}
WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

// replace macros
$replaceArray = array(
	'SITE_DIR' => WIZARD_SITE_DIR,
	'SITE_DESCRIPTION' => htmlspecialcharsbx($wizard->GetVar('metadescription')),
	'SITE_KEYWORDS' => htmlspecialcharsbx($wizard->GetVar('metakeywords')),
	'SITE_NAME' => htmlspecialcharsbx($wizard->GetVar('sitename')),
	'FEEDBACK_EMAIL' => $wizard->GetVar('shopemail')
);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . '_index.php', $replaceArray);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . '.section.php', $replaceArray);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . 'contacts/index.php', $replaceArray);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . 'include_areas/logo.php', $replaceArray);

// add store
if (\Bitrix\Main\Loader::includeModule('catalog'))
{
	$rsSalepoint = \Bitrix\Catalog\StoreTable::getList();
	if (!($salepoint = $rsSalepoint->fetch()))
	{
		$fields = array(
			'TITLE' => htmlspecialcharsbx($wizard->GetVar('sitename')),
			'ACTIVE' => 'Y',
			'GPS_N' => '55.754952',
			'GPS_S' => '37.618324'
		);
		\Bitrix\Catalog\StoreTable::add($fields);
	}
}

// set url rewrite rules
$arUrlRewrite = array();
if (file_exists(WIZARD_SITE_ROOT_PATH . '/urlrewrite.php'))
{
	include WIZARD_SITE_ROOT_PATH . '/urlrewrite.php';
}

$rewrite = array(
	array(
		'CONDITION' => '#^' . WIZARD_SITE_DIR . 'catalog/#',
		'RULE' => '',
		'ID' => 'bitrix:catalog',
		'PATH' => WIZARD_SITE_DIR . 'catalog/index.php'
	)
);
foreach ($rewrite as $url)
{
	if (!in_array($url, $arUrlRewrite))
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