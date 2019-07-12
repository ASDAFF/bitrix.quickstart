<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")) return;

if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;
if(!defined("WIZARD_TEMPLATE_ID")) return;
if(!defined("WIZARD_TEMPLATE_ABSOLUTE_PATH")) return;
if(!defined("WIZARD_THEME_ID")) return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";

if(isset($_SESSION["OPTIMUS_CATALOG_ID"]) && $_SESSION["OPTIMUS_CATALOG_ID"])
	COption::SetOptionString("aspro.optimus", "CATALOG_IBLOCK_ID", $_SESSION["OPTIMUS_CATALOG_ID"], "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "MAX_DEPTH_MENU", 4, "", WIZARD_SITE_ID);
?>