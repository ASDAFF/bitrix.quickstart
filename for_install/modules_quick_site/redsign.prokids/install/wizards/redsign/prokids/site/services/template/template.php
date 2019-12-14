<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
	
if (!defined('WIZARD_TEMPLATE_ID'))
	return;

$bitrixTemplateDir = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT.'/templates/'.WIZARD_TEMPLATE_ID;

CopyDirFiles(
	$_SERVER['DOCUMENT_ROOT'].WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH.'/site').'/'.WIZARD_TEMPLATE_ID,
	$bitrixTemplateDir,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = false,
	$exclude = 'themes'
);

//Attach template to default site
$obSite = CSite::GetList($by = 'def', $order = 'desc', Array('LID' => WIZARD_SITE_ID));
if($arSite = $obSite->Fetch()){
	$arTemplates = Array();
	$found = false;
	$foundEmpty = false;
	$obTemplate = CSite::GetTemplateList($arSite['LID']);
	while($arTemplate = $obTemplate->Fetch()){
		if(!$found && strlen(trim($arTemplate['CONDITION']))<=0){
			$arTemplate['TEMPLATE'] = WIZARD_TEMPLATE_ID;
			$found = true;
		}
		if($arTemplate['TEMPLATE'] == 'empty'){
			$foundEmpty = true;
			continue;
		}
		$arTemplates[]= $arTemplate;
	}

	if(!$found){
		$arTemplates[]= Array('CONDITION' => '', 'SORT' => 101, 'TEMPLATE' => WIZARD_TEMPLATE_ID);
	}
	$arFields = Array(
		'TEMPLATE' => $arTemplates,
		'NAME' => $arSite['NAME'],
	);

	$obSite = new CSite();
	$obSite->Update($arSite['LID'], $arFields);
}
CopyDirFiles($_SERVER['DOCUMENT_ROOT'].WIZARD_RELATIVE_PATH.'/site/services/template/lang/'.LANGUAGE_ID.'/', $bitrixTemplateDir, true, true, true, 'themes');