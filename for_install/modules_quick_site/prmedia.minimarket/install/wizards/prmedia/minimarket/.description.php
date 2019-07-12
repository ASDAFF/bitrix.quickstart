<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

// default site (if isset)
if (!defined('WIZARD_DEFAULT_SITE_ID') && !empty($_REQUEST['wizardSiteID']))
{
	define('WIZARD_DEFAULT_SITE_ID', $_REQUEST['wizardSiteID']);
}

// version
include dirname(__FILE__) . '/version.php';

// wizard description
$arWizardDescription = Array(
	'NAME' => GetMessage('PRMEDIA_WMM_WIZARD_NAME'),
	'DESCRIPTION' => GetMessage('PRMEDIA_WMM_WIZARD_DESCRIPTION'),
	'VERSION' => $arWizardVersion['VERSION'],
	'START_TYPE' => 'WINDOW',
	'WIZARD_TYPE' => 'INSTALL',
	'IMAGE' => 'images/' . LANGUAGE_ID . '/box.jpg',
	'PARENT' => 'wizard_sol',
	'TEMPLATES' => array(
		array(
			'SCRIPT' => 'wizard_sol'
		)
	),
	'STEPS' => array(
		'WelcomeStep',
		'SelectSiteStep',
		'SelectThemeStep',
		'SiteSettingsStep',
		'ShopSettingsStep',
		'PersonTypeStep',
		'PaySystemStep',
		'DataInstallStep',
		'FinishStep',
		'CancelStep'
	)
);