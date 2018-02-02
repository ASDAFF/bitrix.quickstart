<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));
Class alfa1c_adsectlist extends CModule
{
var $MODULE_ID = "alfa1c.adsectlist";
var $MODULE_VERSION;
var $MODULE_VERSION_DATE;
var $MODULE_NAME;
var $MODULE_DESCRIPTION;
var $MODULE_CSS;

function alfa1c_adsectlist()
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

	$this->MODULE_NAME = GetMessage("MODULE_NAME");
	$this->MODULE_DESCRIPTION = GetMessage("MODULE_DESCRIPTION");
	$this->PARTNER_NAME = GetMessage("PARTNER_NAME");
	$this->PARTNER_URI = GetMessage("PARTNER_URI");
}

function InstallFiles($arParams = array())
{
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alfa1c.adsectlist/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/alfa1c.adsectlist");
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alfa1c.adsectlist/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/", true, true);
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/alfa1c.adsectlist/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
	return true;
}

function UnInstallFiles()
{
	DeleteDirFilesEx("/bitrix/components/alfa1c/catalog.section.list");
	DeleteDirFilesEx("/bitrix/themes/.default/alfa1c.adsectlist");
	DeleteDirFilesEx("/bitrix/themes/.default/alfa1c.adsectlist.css");
	DeleteDirFilesEx("/bitrix/components/alfa1c/catalog.section.list");
	DeleteDirFilesEx("/bitrix/js/alfa1c.adsectlist");
	return true;
}

function DoInstall()
{
	global $DOCUMENT_ROOT, $APPLICATION;
	$this->InstallFiles();
	RegisterModule("alfa1c.adsectlist");
	$APPLICATION->IncludeAdminFile(GetMessage("MODULE_INSTALL"), $DOCUMENT_ROOT."/bitrix/modules/alfa1c.adsectlist/install/step.php");
		print_r($DOCUMENT_ROOT);
}

function DoUninstall()
{
	global $DOCUMENT_ROOT, $APPLICATION;
	$this->UnInstallFiles();
	UnRegisterModule("alfa1c.adsectlist");
	$APPLICATION->IncludeAdminFile(GetMessage("MODULE_UNINSTALL"), $DOCUMENT_ROOT."/bitrix/modules/alfa1c.adsectlist/install/unstep.php");
}
}
?>