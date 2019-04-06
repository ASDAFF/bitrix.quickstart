<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));
Class alfa1c_hlexim extends CModule
{
var $MODULE_ID = "alfa1c.hlexim";
var $MODULE_VERSION;
var $MODULE_VERSION_DATE;
var $MODULE_NAME;
var $MODULE_DESCRIPTION;
var $MODULE_CSS;

function alfa1c_hlexim()
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

	$this->MODULE_NAME = GetMessage("HLEXIM_MODULE_NAME");
	$this->MODULE_DESCRIPTION = GetMessage("HLEXIM_MODULE_DESCRIPTION");
	$this->PARTNER_NAME = GetMessage("HLEXIM_PARTNER_NAME");
	$this->PARTNER_URI = GetMessage("HLEXIM_PARTNER_URI");
}

function InstallFiles($arParams = array())
{
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alfa1c.hlexim/install/hlexim_admin.php", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/hlexim_admin.php");
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alfa1c.hlexim/install/hlexim_admin_import.php", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/hlexim_admin_import.php");
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alfa1c.hlexim/install/hlexim_execute.php", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/hlexim_execute.php");
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alfa1c.hlexim/install/themes/alfa1c.hlexim.css", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default/alfa1c.hlexim.css");
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alfa1c.hlexim/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/alfa1c.hlexim");
	return true;
}
function InstallDB()
{
	RegisterModule("alfa1c.hlexim");
return true;
}
function UnInstallFiles()
{
	DeleteDirFilesEx("/bitrix/admin/hlexim_admin.php");
	DeleteDirFilesEx("/bitrix/admin/hlexim_admin_import.php");
	DeleteDirFilesEx("/bitrix/admin/hlexim_execute.php");
	DeleteDirFilesEx("/bitrix/themes/.default/alfa1c.hlexim.css");
	DeleteDirFilesEx("/bitrix/js/alfa1c.hlexim");
	return true;
}
	function UnInstallDB()
	{
		UnRegisterModule("alfa1c.hlexim");
return true;
	}
function DoInstall()
{
	global $DOCUMENT_ROOT, $APPLICATION;
	$this->InstallFiles();
	$this->InstallDB();
	$APPLICATION->IncludeAdminFile(GetMessage("HLEXIM_MODULE_INSTALL"), $DOCUMENT_ROOT."/bitrix/modules/alfa1c.hlexim/install/step.php");
}

function DoUninstall()
{
	global $DOCUMENT_ROOT, $APPLICATION;
	$this->UnInstallFiles();
		$this->UnInstallDB();
	$APPLICATION->IncludeAdminFile(GetMessage("HLEXIM_MODULE_UNINSTALL"), $DOCUMENT_ROOT."/bitrix/modules/alfa1c.hlexim/install/unstep.php");
}

}
?>