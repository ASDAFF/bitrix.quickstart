<?defined("B_PROLOG_INCLUDED")&&B_PROLOG_INCLUDED or die();
/**
 * Install selected template and secondary templates
 */

if (!defined("WIZARD_TEMPLATE_ID"))
	return;

$selectedTemplateDir = WIZARD_SITE_ROOT_PATH.BX_PERSONAL_ROOT."/templates/main-".WIZARD_THEME_ID;
$bitrixTemplateDir = WIZARD_SITE_ROOT_PATH.BX_PERSONAL_ROOT."/templates";
$componentsDir = WIZARD_SITE_ROOT_PATH.BX_PERSONAL_ROOT."/components";

// copy main template
CopyDirFiles(
	WIZARD_THEME_ABSOLUTE_PATH."/main-".WIZARD_THEME_ID,
	$selectedTemplateDir,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false,
	$exclude = "themes"
);

// copy secondary templates
CopyDirFiles(
	WIZARD_ABSOLUTE_PATH."/st/".WIZARD_THEME_ID,
	$bitrixTemplateDir,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false,
	$exclude = "themes"
);

// copy g-tech templates
CopyDirFiles(
	WIZARD_ABSOLUTE_PATH."/components",
	$componentsDir,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false,
	$exclude = "themes"
);

//Attach templates to selected site
$templates = array(
	'main-'.WIZARD_THEME_ID => array("SORT" => 100, "CONDITION" => 'CSite::InDir(\''.WIZARD_SITE_DIR.'index.php\')', "TEMPLATE" => 'main-'.WIZARD_THEME_ID),
	'subpages-'.WIZARD_THEME_ID => array("SORT" => 200, "CONDITION" => '', "TEMPLATE" => 'subpages-'.WIZARD_THEME_ID),
	'forum-'.WIZARD_THEME_ID => array("SORT" => 300, "CONDITION" => 'CSite::InDir(\''.WIZARD_SITE_DIR.'forum/\')', "TEMPLATE" => 'forum-'.WIZARD_THEME_ID),
	'catalog-'.WIZARD_THEME_ID => array("SORT" => 400, "CONDITION" => 'CSite::InDir(\''.WIZARD_SITE_DIR.'catalog/\')', "TEMPLATE" => 'catalog-'.WIZARD_THEME_ID),
);

//Attach template to default site
$obSite = CSite::GetList($by = "def", $order = "desc", Array("LID" => WIZARD_SITE_ID));
if ($arSite = $obSite->Fetch())
{
	$arTemplates = Array();
	// flags
	$found = array('main-'.WIZARD_THEME_ID => false, 'subpages-'.WIZARD_THEME_ID => false, 'forum-'.WIZARD_THEME_ID => false, 'catalog-'.WIZARD_THEME_ID => false);

	$obTemplate = CSite::GetTemplateList($arSite["LID"]);
	while($arTemplate = $obTemplate->Fetch())
	{
		// try to found same template
		foreach ($found as $code => $isFound) {
			if ( ! $isFound and Trim($arTemplate["CONDITION"]) == $templates[$code]['CONDITION']) {
				$arTemplate["TEMPLATE"] = $templates[$code]['TEMPLATE'];
				$found[$code] = true;
			}
		}
		$arTemplates[]= $arTemplate;
	}
	// for not founded templates
	foreach ($found as $code => $isFound) {
		if (!$isFound) {
			$arTemplates[]= $templates[$code];
		}
	}

	$arFields = Array(
		"TEMPLATE" => $arTemplates,
		"NAME" => GetMessage("KIDSHOP_NAME"),
	);

	$obSite = new CSite();
	$obSite->Update($arSite["LID"], $arFields);

	CWizardUtil::ReplaceMacros($selectedTemplateDir."/header.php", $replace);
	CWizardUtil::ReplaceMacros($selectedTemplateDir."/footer.php", $replace);
}

COption::SetOptionString("main", "wizard_template_id", WIZARD_TEMPLATE_ID, false, WIZARD_SITE_ID);
?>