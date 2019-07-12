<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class maevrika_avtoservice extends CModule
{
	var $MODULE_ID = "maevrika.avtoservice";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function maevrika_avtoservice()
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
			$this->MODULE_VERSION = "1.0.5";
            $this->MODULE_VERSION_DATE = "2012-10-19 17:00:00";
		}
		$this->PARTNER_NAME = GetMessage("evrica_avtoservice_partner");
		$this->PARTNER_URI = GetMessage("evrica_avtoservice_site");
		$this->MODULE_NAME = GetMessage("evrica_avtoservice_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("evrica_avtoservice_MODULE_DESC");
	}

	function InstallDB($install_wizard = true)
	{
		global $DB, $DBType, $APPLICATION;

		RegisterModule("maevrika.avtoservice");
		RegisterModuleDependences("main", "OnBeforeProlog", "maevrika.avtoservice", "CSiteAvtoservice", "ShowPanel");

		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		global $DB, $DBType, $APPLICATION;

		UnRegisterModuleDependences("main", "OnBeforeProlog", "maevrika.avtoservice", "CSiteAvtoservice", "ShowPanel"); 
		UnRegisterModule("maevrika.avtoservice");

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
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/maevrika.avtoservice/install/wizards/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/", true, true);
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/wizards/evrika/");
		return true;
	}

	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallEvents();
		$APPLICATION->IncludeAdminFile(GetMessage("evrica_avtoservice_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/maevrika.avtoservice/install/step.php");
	}

	function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallFiles();
		$this->UnInstallDB();
		$this->UnInstallEvents();
		$APPLICATION->IncludeAdminFile(GetMessage("evrica_avtoservice_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/maevrika.avtoservice/install/unstep.php");
	}
}
?>