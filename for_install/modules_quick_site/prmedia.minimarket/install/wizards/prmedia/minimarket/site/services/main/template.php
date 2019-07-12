<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

if (!defined('WIZARD_TEMPLATE_ID'))
{
	return;
}

// copy template and theme files
$templateName =  WIZARD_TEMPLATE_ID . '_' . WIZARD_THEME_ID;
$wizardTemplateDir = $_SERVER['DOCUMENT_ROOT'] . WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH . '/site') . '/' . WIZARD_TEMPLATE_ID;
$bitrixTemplateDir = $_SERVER['DOCUMENT_ROOT'] . BX_PERSONAL_ROOT . "/templates/$templateName";
CopyDirFiles(
	$wizardTemplateDir,
	$bitrixTemplateDir,
	$rewrite = true,
	$recursive = true, 
	$delete_after_copy = false,
	$exclude = 'themes'
);
CopyDirFiles(
	$wizardTemplateDir . '/themes/' . WIZARD_THEME_ID,
	$bitrixTemplateDir,
	$rewrite = true,
	$recursive = true, 
	$delete_after_copy = false
);

// attach template to default site
$rsSite = CSite::GetList($by = 'def', $order = 'desc', array('LID' => WIZARD_SITE_ID));
if ($site = $rsSite->Fetch())
{
	$arTemplates = Array();
	$found = false;
	$foundEmpty = false;
	$rsTemplate = CSite::GetTemplateList($site['LID']);
	while($template = $rsTemplate->Fetch())
	{
		if(!$found && strlen(trim($template['CONDITION'])) <= 0)
		{
			$template['TEMPLATE'] = $templateName;
			$found = true;
		}
		if($template['TEMPLATE'] == 'empty')
		{
			$foundEmpty = true;
			continue;
		}
		$templates[] = $template;
	}
	if (!$found)
	{
		$templates[] = array(
			'CONDITION' => '',
			'SORT' => 150,
			'TEMPLATE' => $templateName
		);
	}
	$fields = array(
		'TEMPLATE' => $templates,
		'NAME' => $site['NAME'],
	);

	$obSite = new CSite();
	$obSite->Update($site['LID'], $fields);
}

// replace template macros
$replaceArray = array(
	'SITE_DIR' => WIZARD_SITE_DIR
);
WizardServices::ReplaceMacrosRecursive($_SERVER['DOCUMENT_ROOT'] . BX_PERSONAL_ROOT . "/templates/$templateName", $replaceArray);