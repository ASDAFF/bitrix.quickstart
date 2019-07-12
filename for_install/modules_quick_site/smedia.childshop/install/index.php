<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php")); 

Class smedia_childshop extends CModule
{
	var $MODULE_ID = "smedia.childshop";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';
	function smedia_childshop()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		$this->PARTNER_NAME = "Media-Service";
        $this->PARTNER_URI = "http://www.smedia.ru";

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("SMCHILDSHOP_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SMCHILDSHOP_MODULE_DESC");
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		RegisterModule("smedia.childshop");
		RegisterModuleDependences('main', 'OnBeforeProlog', 'smedia.childshop', 'SMChildShopEvents', 'ShowPanel');
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		UnRegisterModuleDependences('main', 'OnBeforeProlog', 'smedia.childshop', 'SMChildShopEvents', 'ShowPanel');
		UnRegisterModule("smedia.childshop");
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/smedia.childshop/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", false, true);
		return true;
	}

	function UnInstallFiles()
	{
		return true;
	}

	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallDB();
		$this->InstallFiles();
		$APPLICATION->IncludeAdminFile(GetMessage("SMCHILDSHOP_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/smedia.childshop/install/step.php");
	}

	function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallDB();
		$this->UnInstallFiles();
		$APPLICATION->IncludeAdminFile(GetMessage("SMCHILDSHOP_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/smedia.childshop/install/unstep.php");
	}
}
?>