<?
global $MESS;
IncludeModuleLangFile(__FILE__);

Class page_error_404 extends CModule
{
	var $MODULE_ID = "page.error_404";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	function page_error_404()
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
		else
		{
			$this->MODULE_VERSION = '1.1.0';
			$this->MODULE_VERSION_DATE = '2011-12-11 23:43:00';
		}

		$this->PARTNER_NAME = GetMessage("PARTNER_NAME");
		$this->PARTNER_URI = "http://asdaff.ru/";

		$this->MODULE_NAME = GetMessage("ERR404_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("ERR404_MODULE_DESC");
	}

	function InstallDB($arParams = array())
	{
    return true;
	}

	function UnInstallDB($arParams = array())
	{
		return true;
	}

	function InstallEvents()
	{
		RegisterModule("page.error_404");
		RegisterModuleDependences("main", "OnEpilog", "page.error_404", "CErr404", "handler404", 10000);

		return true;
	}

	function UnInstallEvents()
	{
		COption::RemoveOption("page.error_404");
		UnRegisterModuleDependences("main", "OnEpilog", "page.error_404", "CErr404", "handler404");
		UnRegisterModule("page.error_404");

		return true;
	}

	function InstallFiles($arParams = array())
	{
		return true;
	}

	function UnInstallFiles()
	{
		return true;
	}

	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallEvents();
		$APPLICATION->IncludeAdminFile(GetMessage("ERR404_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/page.error_404/install/step.php");
	}

	function DoUninstall()
	{
		$this->UnInstallEvents();
	}
}
?>
