<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
	
if(!CModule::IncludeModule("search"))
	return;
	
if(!defined("WIZARD_SITE_ID")) return;
	
if(COption::GetOptionString("search", "exclude_mask") == ""){
	COption::SetOptionString("search", "exclude_mask", "/bitrix/*;/404.php;/upload/*;/local/*;");
}

if(WIZARD_SITE_ID != ""){
	$NS["SITE_ID"] = WIZARD_SITE_ID;
}
		
if(!isset($_SESSION['SearchFirst'])){
	$NS = CSearch::ReIndexAll(false, 20, $NS);
}
else{
	$NS = CSearch::ReIndexAll(false, 20, $_SESSION['SearchNS']);
}
           
if(is_array($NS)){
	//repeat step, if indexing doesn't finish
	$this->repeatCurrentService = true; 
	$_SESSION['SearchNS'] = $NS;
	$_SESSION['SearchFirst'] = 1;	
}
else{
	unset($_SESSION['SearchNS']);
	unset($_SESSION['SearchFirst']);       
}

// site name - it`s need!!!
if($wizard->GetVar('siteNameSet', true)){
	$siteName = $wizard->GetVar("siteName");
	COption::SetOptionString("main", "site_name", $siteName);	
	$obSite = new CSite;
	$arFields = array("NAME" => $siteName, "SITE_NAME" => $siteName);			
	$siteRes = $obSite->Update(WIZARD_SITE_ID, $arFields);
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_NAME" => $siteName));
}
?>