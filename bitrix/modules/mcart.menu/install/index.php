<?
IncludeModuleLangFile( __FILE__);
if(class_exists("mcart_menu")) 
	return;
	
Class mcart_menu extends CModule
{
var $MODULE_ID = "mcart.menu";
var $MODULE_VERSION;
var $MODULE_VERSION_DATE;
var $MODULE_NAME;
var $MODULE_DESCRIPTION;
var $MODULE_CSS;

function mcart_menu()
{
$arModuleVersion = array();

$path = str_replace("\\", "/", __FILE__);
$path = substr($path, 0, strlen($path) - strlen("/index.php"));
include($path."/version.php");

if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
{
$this->MODULE_VERSION = $arModuleVersion["VERSION"];
$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
}

$this->MODULE_NAME = GetMessage("NAME");
$this->MODULE_DESCRIPTION = GetMessage("DESCRIPTION");
        $this->PARTNER_NAME = GetMessage("COMPANY");
        $this->PARTNER_URI  = "http://mcart.ru/";
}

function InstallComponent($arParams = array())
{
	CopyDirFiles(
		$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.menu/install/components", 
		$_SERVER["DOCUMENT_ROOT"]."/bitrix/components", 
		true, 
		true
	);
return true;
}

function UnInstallComponent()
{
DeleteDirFilesEx("/bitrix/components/mcart.menu");
return true;
}

function DoInstall()
{
global $DOCUMENT_ROOT, $APPLICATION, $step;
		$this->InstallComponent();
		RegisterModule($this->MODULE_ID);
$APPLICATION->IncludeAdminFile(GetMessage("INSTAL_MODULE"), $DOCUMENT_ROOT."/bitrix/modules/mcart.menu/install/step.php");
}

function DoUninstall()
{
global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallComponent();
        UnRegisterModule($this->MODULE_ID);
UnRegisterModule("mcart.menu");
$APPLICATION->IncludeAdminFile(GetMessage("UNINSTAL_MODULE"), $DOCUMENT_ROOT."/bitrix/modules/mcart.menu/install/unstep.php");
}
}
?>