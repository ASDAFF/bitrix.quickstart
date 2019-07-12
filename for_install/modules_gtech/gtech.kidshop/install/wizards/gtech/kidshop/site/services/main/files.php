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

//CModule::IncludeModule("search");
//CSearch::ReIndexAll(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR));

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH.'tmp-'.WIZARD_SITE_ID, $arReplace);
// set rights
global $APPLICATION;
$APPLICATION->SetFileAccessPermission(
	array(WIZARD_SITE_ID,'/'),
	array('*' => 'R')
);


$arUrlRewrite = array();
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php")) {
	include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}
$arNewUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."personal/order/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:sale.personal.order",
		"PATH"	=>	WIZARD_SITE_DIR."personal/order/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."about/news/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	WIZARD_SITE_DIR."about/news/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:catalog",
		"PATH"	=>	WIZARD_SITE_DIR."catalog/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."forum/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:forum",
		"PATH"	=>	WIZARD_SITE_DIR."forum/index.php",
	),
);
foreach ($arNewUrlRewrite as $arUrl) {
	if(!in_array($arUrl, $arUrlRewrite)) { CUrlRewriter::Add($arUrl); }
}
?>