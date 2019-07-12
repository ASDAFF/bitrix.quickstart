<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_PATH"))
	return;

if (!WIZARD_IS_RERUN){
	CopyDirFiles(
		WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/common",
		WIZARD_SITE_PATH.WIZARD_SITE_DIR,
		$rewrite = false, 
		$recursive = true
	);
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH.WIZARD_SITE_DIR, Array("SITE_DIR" => WIZARD_SITE_DIR));
}
else {
	copy (WIZARD_SITE_PATH.WIZARD_SITE_DIR.'index.php',WIZARD_SITE_PATH.WIZARD_SITE_DIR.'index_old.php');
	copy (WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/common/_index.php",WIZARD_SITE_PATH.WIZARD_SITE_DIR.'_index.php');
	copy (WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/common/include/company_name.php",WIZARD_SITE_PATH.WIZARD_SITE_DIR.'include/company_name.php');
}

if(!file_exists(WIZARD_SITE_PATH.WIZARD_SITE_DIR."include/addToCartAjax.php")) 
{ 
	copy (WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/common/include/addToCartAjax.php",WIZARD_SITE_PATH.WIZARD_SITE_DIR.'include/addToCartAjax.php');
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH.WIZARD_SITE_DIR, Array("SITE_DIR" => WIZARD_SITE_DIR));
}  

if(!file_exists(WIZARD_SITE_PATH.WIZARD_SITE_DIR."include/youHaveSeen.php")) 
{ 
	copy (WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/common/include/youHaveSeen.php",WIZARD_SITE_PATH.WIZARD_SITE_DIR.'include/youHaveSeen.php');
}  

if(!file_exists(WIZARD_SITE_PATH.WIZARD_SITE_DIR."include/checkDisableAddToCart.php")) 
{ 
	copy (WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/common/include/checkDisableAddToCart.php", WIZARD_SITE_PATH.WIZARD_SITE_DIR.'include/checkDisableAddToCart.php');
}  

if(!file_exists(WIZARD_SITE_PATH.WIZARD_SITE_DIR."auth/index.php")) 
{ 
	if(!is_dir(WIZARD_SITE_PATH.WIZARD_SITE_DIR."auth"))					
		mkdir(WIZARD_SITE_PATH.WIZARD_SITE_DIR."auth", 0770);
	if(is_dir(WIZARD_SITE_PATH.WIZARD_SITE_DIR."auth"))
		copy (WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/common/auth/index.php",WIZARD_SITE_PATH.WIZARD_SITE_DIR.'auth/index.php');
}  
?>
