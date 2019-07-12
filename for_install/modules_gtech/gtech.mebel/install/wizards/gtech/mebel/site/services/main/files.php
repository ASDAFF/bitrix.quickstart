<?defined("B_PROLOG_INCLUDED")&&B_PROLOG_INCLUDED or die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;

$arReplace = unserialize($wizard->GetVar('MAGIC_REPLACE'));

//throw new Exception;

$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/");

CopyDirFiles(
	$path,
	WIZARD_SITE_PATH.'tmp-'.WIZARD_SITE_ID,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false
);

CModule::IncludeModule("search");
CSearch::ReIndexAll(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR));

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH.'tmp-'.WIZARD_SITE_ID, $arReplace);
// set rights
global $APPLICATION;
$APPLICATION->SetFileAccessPermission(
	array(WIZARD_SITE_ID,'/'),
	array('*' => 'R')
);


?>