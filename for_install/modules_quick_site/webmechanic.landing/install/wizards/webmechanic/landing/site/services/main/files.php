<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();
    

/*echo "WIZARD_SITE_ID=".WIZARD_SITE_ID." | ";
echo "WIZARD_SITE_DIR=".WIZARD_SITE_DIR." | ";
echo "WIZARD_SITE_PATH=".WIZARD_SITE_PATH." | ";
echo "WIZARD_RELATIVE_PATH=".WIZARD_RELATIVE_PATH." | ";
echo "WIZARD_ABSOLUTE_PATH=".WIZARD_ABSOLUTE_PATH." | ";
echo "WIZARD_TEMPLATE_ID=".WIZARD_TEMPLATE_ID." | ";
echo "WIZARD_TEMPLATE_RELATIVE_PATH=".WIZARD_TEMPLATE_RELATIVE_PATH." | ";
echo "WIZARD_TEMPLATE_ABSOLUTE_PATH=".WIZARD_TEMPLATE_ABSOLUTE_PATH." | ";
echo "WIZARD_THEME_ID=".WIZARD_THEME_ID." | ";
echo "WIZARD_THEME_RELATIVE_PATH=".WIZARD_THEME_RELATIVE_PATH." | ";
echo "WIZARD_THEME_ABSOLUTE_PATH=".WIZARD_THEME_ABSOLUTE_PATH." | ";
echo "WIZARD_SERVICE_RELATIVE_PATH=".WIZARD_SERVICE_RELATIVE_PATH." | ";
echo "WIZARD_SERVICE_ABSOLUTE_PATH=".WIZARD_SERVICE_ABSOLUTE_PATH." | ";
echo "WIZARD_IS_RERUN=".WIZARD_IS_RERUN." | ";
die();*/

if (!defined("WIZARD_SITE_ID"))
    return;

if (!defined("WIZARD_SITE_DIR"))
    return;
 
CopyDirFiles(
    //WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/",
    WIZARD_ABSOLUTE_PATH."/site/public/",
    WIZARD_SITE_PATH,
    $rewrite = true, 
    $recursive = true,
    $delete_after_copy = false,
    $exclude = "bitrix"
);

CopyDirFiles(
    WIZARD_ABSOLUTE_PATH."/site/components/",
    $_SERVER['DOCUMENT_ROOT']."/bitrix/components/",
    $rewrite = false, 
    $recursive = true,
    $delete_after_copy = false
);
    

?>
