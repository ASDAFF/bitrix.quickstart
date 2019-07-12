<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;	

if (COption::GetOptionString('smedia.childshop', 'catalog_installed', 'N',WIZARD_SITE_ID)!=='Y') {
	$iblockCode = "igrushka_".WIZARD_SITE_ID; 
	$iblockType = "catalog"; 
	$iblockID = false;
	$iblockXMLFile =WIZARD_SERVICE_RELATIVE_PATH.'/xml/'.LANGUAGE_ID."/igrushka_data_5.xml"; 
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		'sm_igrushka_tmp',
		$iblockType,
		WIZARD_SITE_ID,
		$permissions = Array(
			"1" => "W",
			"2" => "R",
		),
		true
	);
}
?>
