<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class bitrix_schoolsite extends CModule
{
	var $MODULE_ID = "bitrix.schoolsite";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function bitrix_schoolsite()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("SCHOOLSITE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SCHOOLSITE_MODULE_DESC");
		
		$this->PARTNER_NAME = GetMessage("SCHOOLSITE_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("SCHOOLSITE_PARTNER_URL");
	}

	function InstallDB($arParams = array())
	{
		RegisterModule("bitrix.schoolsite");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "bitrix.schoolsite", "CSchool", "OnBeforeIBlockElementAddHandler");
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "bitrix.schoolsite", "CSchool", "OnBeforeIBlockElementAddHandler");
		UnRegisterModule("bitrix.schoolsite");

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
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bitrix.schoolsite/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);

		return true;
	}

	function InstallPublic()
	{
	}

	function UnInstallFiles()
	{
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION, $step;

		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallEvents();
		$this->InstallPublic();

		$APPLICATION->IncludeAdminFile(GetMessage("SCHOOLSITE_INSTALL_TITLE"), $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/bitrix.schoolsite/install/step.php");
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		$APPLICATION->IncludeAdminFile(GetMessage("SCHOOLSITE_UNINSTALL_TITLE"), $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/bitrix.schoolsite/install/unstep.php");
	}
}
?>
