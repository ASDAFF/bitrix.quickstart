<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

if(class_exists("elipseart_formdesignmodify")) return;
Class elipseart_formdesignmodify  extends CModule
{
	var $MODULE_ID = "elipseart.formdesignmodify";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function elipseart_formdesignmodify()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("SPER_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SPER_INSTALL_DESCRIPTION");
		
		$this->PARTNER_NAME = GetMessage("PARTNER_NAME");
		$this->PARTNER_URI = "http://www.elipseart.ru/";
	}

	function InstallDB($install_wizard = true)
	{
		global $DB, $DBType, $APPLICATION;

		RegisterModule("elipseart.formdesignmodify");

		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		global $DB, $DBType, $APPLICATION;

		UnRegisterModule("elipseart.formdesignmodify");

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

	function InstallFiles()
	{
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/elipseart.formdesignmodify/install/components/",
			$_SERVER["DOCUMENT_ROOT"].BX_ROOT."/components/",
			true, true
		);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx(BX_ROOT."/components/elipseart/form.design_modify/");
		
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION, $step;

		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallEvents();

		$APPLICATION->IncludeAdminFile(GetMessage("SPER_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/elipseart.formdesignmodify/install/step.php");
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();

		$APPLICATION->IncludeAdminFile(GetMessage("SPER_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/elipseart.formdesignmodify/install/unstep.php");
	}

	function GetModuleRightList()
	{
		$arr = array(
			
		);
		return $arr;
	}
}
?>